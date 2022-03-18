// Nach Beschreibung auf dieser Seite
// https://techtutorialsx.com/2017/09/30/esp32-arduino-external-interrupts/
/*
#include <Arduino.h>
const byte interruptPinRed    = 18;
const byte interruptPinYellow = 19;
const byte interruptPinGreen  = 04;
int countRed = 0;

void setup() {

  Serial.begin(115200);
  Serial.println("Monitoring interrupts: ");
  pinMode(interruptPinRed,    INPUT_PULLUP);
  pinMode(interruptPinYellow, INPUT_PULLUP);
  pinMode(interruptPinGreen,  INPUT_PULLUP);
}

void loop() {
  int statusGreen  = digitalRead(interruptPinGreen);
  int statusYellow = digitalRead(interruptPinYellow);
  int statusRed    = digitalRead(interruptPinRed);
  if( statusRed < 1) {

      Serial.printf("An Taste Red has occurred. Total: %2d %2d %2d - %5d \n",statusGreen, statusYellow, statusRed, countRed++);
  }
  delay(100);
}
*/
