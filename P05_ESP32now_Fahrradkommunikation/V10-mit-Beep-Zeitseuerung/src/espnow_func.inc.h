/*
 * Hier befinden sich alle Definitionen und Funktionen rund um ESPnow
 *
 * 04.03.2022 - Udo Besenreuther
 *
 */


// Variable to store if sending data was successful
String success;

//Structure example to send data
//Must match the receiver structure
typedef struct struct_message {
    char a[32];
    bool green;
    bool yellow;
    bool red;
} struct_message;

// Create a struct_message called BME280Readings to hold sensor readings
struct_message myStatus;

// Create a struct_message to hold incoming sensor readings
struct_message incomingStatus;

// Callback when data is sent
void OnDataSent(const uint8_t *mac_addr, esp_now_send_status_t status) {
  if (test) {
    Serial.print("\r\nLast Packet Send Status:\t");
    Serial.println(status == ESP_NOW_SEND_SUCCESS ? "Delivery Success" : "Delivery Fail");
  }
  if (status == 0){
    success = "Delivery Success :)";

    // grün blinkt, wenn erfolgreich empfangen (Status bleibt erhalten)
    if ((millis()-blink_loop) > blink_delta) {
      digitalWrite(pinLEDgreen, !my_green);
      delay(100);
      digitalWrite(pinLEDgreen, my_green);
      blink_loop = millis();
    }

  } else {
    success = "Delivery Fail :(";

    // rot blinkt, wenn NICHT erfolgreich empfangen (Status bleibt erhalten)
    if ((millis()-blink_loop) > blink_delta) {
      digitalWrite(pinLEDred, !incoming_red);
      delay(200);
      digitalWrite(pinLEDred, incoming_red);
      blink_loop = millis();
    }

  }
}

// Callback wenn Daten empfangen wurden
void OnDataRecv(const uint8_t * mac, const uint8_t *incomingData, int len) {
  memcpy(&incomingStatus, incomingData, sizeof(incomingStatus));
  if (test) {
    Serial.printf("Bytes received: %5d \n", len);
  }
  incoming_green  = incomingStatus.green;
  incoming_yellow = incomingStatus.yellow;
  incoming_red    = incomingStatus.red;
}
