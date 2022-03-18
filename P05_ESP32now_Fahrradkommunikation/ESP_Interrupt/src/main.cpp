// Nach Beschreibung auf dieser Seite
// https://techtutorialsx.com/2017/09/30/esp32-arduino-external-interrupts/

#include <Arduino.h>
const byte interruptPinRed    = 18;
const byte interruptPinYellow = 19;
const byte interruptPinGreen  = 04;

 int interruptCounterRed    = 0;
 int interruptCounterYellow = 0;
 int interruptCounterGreen  = 0;

int numberOfInterruptsRed    = 0;
int numberOfInterruptsYellow = 0;
int numberOfInterruptsGreen  = 0;

int nextGreen = 0;
int nextYellow = 0;
int nextRed = 0;

portMUX_TYPE muxRed    = portMUX_INITIALIZER_UNLOCKED;
portMUX_TYPE muxYellow = portMUX_INITIALIZER_UNLOCKED;
portMUX_TYPE muxGreen  = portMUX_INITIALIZER_UNLOCKED;

void IRAM_ATTR handleInterruptRed() {
  portENTER_CRITICAL_ISR(&muxRed);
  interruptCounterRed++;
  portEXIT_CRITICAL_ISR(&muxRed);
}
void IRAM_ATTR handleInterruptYellow() {
  portENTER_CRITICAL_ISR(&muxYellow);
  interruptCounterYellow++;
  portEXIT_CRITICAL_ISR(&muxYellow);
}
void IRAM_ATTR handleInterruptGreen() {
  portENTER_CRITICAL_ISR(&muxGreen);
  interruptCounterGreen++;
  portEXIT_CRITICAL_ISR(&muxGreen);
}

void setup() {

  Serial.begin(115200);
  Serial.println("Monitoring interrupts: ");
  pinMode(interruptPinRed,    INPUT_PULLUP);
  pinMode(interruptPinYellow, INPUT_PULLUP);
  pinMode(interruptPinGreen,  INPUT_PULLUP);

  attachInterrupt(digitalPinToInterrupt(interruptPinRed),    handleInterruptRed,    FALLING);
  attachInterrupt(digitalPinToInterrupt(interruptPinYellow), handleInterruptYellow, FALLING);
  attachInterrupt(digitalPinToInterrupt(interruptPinGreen),  handleInterruptGreen,  FALLING);

}

void loop() {

  if(interruptCounterRed > 0){
      portENTER_CRITICAL(&muxRed);
      interruptCounterRed = 0;
      numberOfInterruptsRed++;
      Serial.printf("An interrupt Red has occurred. Total rot: %3d  - gelb: %3d - grün: %3d\r", numberOfInterruptsRed, numberOfInterruptsYellow, numberOfInterruptsGreen);
      //delay(200);
      portEXIT_CRITICAL(&muxRed);
  }
  if(interruptCounterYellow > 0 and nextYellow < millis()){
      portENTER_CRITICAL(&muxYellow);
      interruptCounterYellow--;
      numberOfInterruptsYellow++;
      nextYellow = millis() + 3000;
      Serial.printf("An interrupt Red has occurred. Total rot: %3d  - gelb: %3d - grün: %3d\r", numberOfInterruptsRed, numberOfInterruptsYellow, numberOfInterruptsGreen);
      //delay(200);
      portEXIT_CRITICAL(&muxYellow);
  }
  if(interruptCounterGreen>0){
      portENTER_CRITICAL(&muxGreen);
      interruptCounterGreen--;
      numberOfInterruptsGreen++;
      Serial.printf("An interrupt Red has occurred. Total rot: %3d  - gelb: %3d - grün: %3d\r", numberOfInterruptsRed, numberOfInterruptsYellow, numberOfInterruptsGreen);
      //delay(200);
      portEXIT_CRITICAL(&muxGreen);
  }
}
