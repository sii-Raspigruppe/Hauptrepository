/*
 * Hier sind die lokalen Einstellungen enthalten
 * Da jeder andere Ports und andere Mac-Adressen benutzt,
 * kann man diese Datei einfach austauschen und durch die eigene ersetzen.
 *
 * 04.03.2022 - Udo Besenreuther
 *
 */

// Hier die Mac-Adressen meiner ESPs
uint8_t Arduino_ESP00[]   = {0x4C, 0xEB, 0xD6, 0x74, 0x49, 0xE8};  //0 COM11 A01
uint8_t Arduino_ESP01[]   = {0x58, 0xBF, 0x25, 0x82, 0x45, 0xAC};  //1 COM07 A02
uint8_t Arduino_ESP02[]   = {0x24, 0x6F, 0x28, 0x7C, 0x91, 0xC4};  //2 COM08 - Einbauverseion
uint8_t Arduino_ESP03[]   = {0xC8, 0xC9, 0xA3, 0xC9, 0x77, 0x70};  //3 COM08 - NodeMCU 77:70
uint8_t *Arduino_ESP[]    = { Arduino_ESP00, Arduino_ESP01, Arduino_ESP02, Arduino_ESP03 };

// hier wird der jeweilige Partner eingetragen
uint8_t *broadcastAddress = Arduino_ESP[3];

  // true, wenn erweiterte Testausgaben gewünscht werden
  bool test = false;

  // ESP32 NodeMCU
  int pinGreen     = 04;
  int pinYellow    = 19;
  int pinRed       = 18;

  int pinLEDgreen  = 05;
  int pinLEDyellow = 17;
  int pinLEDred    = 16;

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
