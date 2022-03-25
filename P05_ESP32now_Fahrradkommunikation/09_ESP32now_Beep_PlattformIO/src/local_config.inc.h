/*
 * Hier sind die lokalen Einstellungen enthalten
 * Da jeder andere Ports und andere Mac-Adressen benutzt,
 * kann man diese Datei einfach austauschen und durch die eigene ersetzen.
 *
 * 04.03.2022 - Udo Besenreuther
 *
 */

// hier wird der jeweilige Partner aktiviert, der andere als Kommentar definiert
//uint8_t *broadcastAddress = {0xC8, 0xC9, 0xA3, 0xC9, 0x77, 0x70}; //3 COM08 - NodeMCU 77:70
uint8_t *broadcastAddress = {0x30, 0xC6, 0xF7, 0x55, 0x89, 0x9C}; //4 COM011 - ESP32-2 01

  // true, wenn erweiterte Testausgaben gewünscht werden
  bool test = false;

  // ESP32 NodeMCU
  int pinGreen     = 05;
  int pinYellow    = 18;
  int pinRed       = 19;

  int pinLEDgreen  = 04;
  int pinLEDyellow = 16;
  int pinLEDred    = 17;

  int pinBeep      = 02;

  // für rot oder gelb kann ein eigenes Beep-Muster hinterlegt werden
  uint16_t timeBeepYellow[4] = {200,100,200,100};
  uint16_t timeBeepRed[6]    = {200,100,200,100,200,100};

  // Vorbelegung der Variablen
  // my_xxx = Status der LED von diesem Gerät
  bool my_red          = false;
  bool my_yellow       = false;
  bool my_green        = false;
  // incoming_xxx = Status der LEDs vom Partnergerät
  bool incoming_green  = false;
  bool incoming_yellow = false;
  bool incoming_red    = false;

  // damit man nicht mit delay arbeiten muss, die das Programm stoppen, steht in den Variablen wann der nächste Zeitpunkt ist
  uint time_loop = millis();
  uint time_delta = 1000;  // sooft findet Übertragung statt
  uint blink_loop = millis();
  uint blink_delta = 1300; // so schnell blinkt das grüne Licht
