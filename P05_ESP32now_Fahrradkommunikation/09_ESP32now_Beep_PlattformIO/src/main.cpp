/*
  Raspigruppe des Netzwerk-sii-BW

  06.03.2022 - Udo Besenreuther

  Grundlage waren die Infos von Rui Santos
  Complete project details at https://RandomNerdTutorials.com/esp-now-two-way-communication-esp32/

  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files.

  The above copyright notice and this permission notice shall be included in all
  copies or substantial portions of the Software.
*/
#define CONFIG_ESPNOW_ENABLE_LONG_RANGE

// die Standard-libs
#include "WiFi.h"
#include <esp_now.h>
#include "esp_wifi.h"

// Individuelle Includedateien, um mehr Übersichtlichkeit zu erhalten
#include "local_config_privat.inc.h"
#include "espnow_func.inc.h"
#include "local_func.inc.h"

void setup() {
  // Init Serial Monitor
  Serial.begin(115200);

  // PINs festlegen und LED-Test starten
  LED_Setup();
  LED_Test(200, 1000);

  // Wi-Fi starten und aktuelle Mac-Adressen ausgeben
  WiFi.mode(WIFI_STA);
  #ifdef CONFIG_ESPNOW_ENABLE_LONG_RANGE
    WiFi.disconnect();
    ESP_ERROR_CHECK(esp_wifi_set_protocol(WIFI_IF_STA,WIFI_PROTOCOL_LR));
  #endif
  Serial.print("Meine Mac-Adresse: ");
  Serial.println(WiFi.macAddress());
  Serial.print("Partner-Mac-Adresse: ");
  Serial.printf(" %2X:%2X:%2X:%2X:%2X:%2X \n\n", broadcastAddress[0], broadcastAddress[1], broadcastAddress[2], broadcastAddress[3], broadcastAddress[4], broadcastAddress[5]);

  // WLANs suchen
  ScanForSlave();

  // Init ESP-NOW
  if (esp_now_init() != ESP_OK) {
    Serial.println("Error initializing ESP-NOW");
    return;
  }

  // Once ESPNow is successfully Init, we will register for Send CB to
  // get the status of Trasnmitted packet
  esp_now_register_send_cb(OnDataSent);

  // Register peer
  esp_now_peer_info_t peerInfo;
  memcpy(peerInfo.peer_addr, broadcastAddress, 6);
  peerInfo.channel = 0;
  peerInfo.encrypt = false;

  // Add peer
  if (esp_now_add_peer(&peerInfo) != ESP_OK){
    Serial.println("Failed to add peer");
    return;
  }
  // Register for a callback function that will be called when data is received
  esp_now_register_recv_cb(OnDataRecv);
}

void getReadings() {

  // die Tasten auslesen
  my_green  = !digitalRead(pinGreen);
  my_yellow = !digitalRead(pinYellow);
  my_red    = !digitalRead(pinRed);

  // Status der my_xxx-Variablen ausgeben
  Serial.print ("                      grün gelb rot\n");
  Serial.printf("Status der Tasten:       %1d / %1d / %1d \n", my_green, my_yellow, my_red);
}

void loop() {

  // Sensortasten lesen
  getReadings();

  // Wenn in der Empfangsstruktur (incomingStatus.) eine Taste gesetzt ist oder
  // die eigene Taste gedrückt, dann setze den Status auch in der Sendestruktur (myStatus.)
  if ( incomingStatus.yellow or my_yellow ) myStatus.yellow = true;
  if ( incomingStatus.red    or my_red    ) myStatus.red    = true;
  // Wenn grün empfangen oder gedrückt wird, dann lösche gelb und rot und sende grün weiter
  if ( incomingStatus.green  or my_green  ) {
    myStatus.green  = true;
    myStatus.yellow = false;
    myStatus.red    = false;
  }
  // wenn kein rot und kein gelb mehr leuchet, dann setze auch grün auf false
  if ( !incomingStatus.red and !incomingStatus.yellow ) {
    myStatus.green = false;
  }

  // Ausgabe der Sendestruktur (myStatus)
  Serial.printf("Status der LEDs:         %1d / %1d / %1d \n", myStatus.green, myStatus.yellow, myStatus.red);
  //Serial.printf("Signalstärke             %3.1f dBm \n", WiFi.RSSI());
  Serial.println(WiFi.RSSI());

  // Send message via ESP-NOW
  esp_err_t result = esp_now_send(broadcastAddress, (uint8_t *) &myStatus, sizeof(myStatus));

  // Klartextausgabe, ob Senden erfolgreich
  if (test) {
    if (result == ESP_OK) {
      Serial.println("Sent with success");
    } else {
      Serial.println("Error sending the data");
    }
  }
  // aktuellen Status mit den LEDs anzeigen
  if ((millis() - time_loop) > time_delta) {
    updateDisplay();
    time_loop = millis();
  }
  // solang nix tun, dann nächste Runde
  delay(100);
}
