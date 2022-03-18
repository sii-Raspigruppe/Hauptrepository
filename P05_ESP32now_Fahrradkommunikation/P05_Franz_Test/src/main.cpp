/*
  Raspigruppe des Netzwerk-sii-BW
  Projekt P05 - Fahradkommunikation

  Grundlage waren die Infos von Rui Santos
  Complete project details at https://RandomNerdTutorials.com/esp-now-two-way-communication-esp32/

  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files.

  The above copyright notice and this permission notice shall be included in all
  copies or substantial portions of the Software.
*/

#include <esp_now.h>
#include <WiFi.h>

// REPLACE THE VALUES WITH THE MAC Address of your receiver and sender - and COM-Interface
uint8_t Arduino_ESP01[]   = {0x58, 0xBF, 0x25, 0x9D, 0xD7, 0x14};  //COM06
uint8_t Arduino_ESP02[]   = {0xC8, 0xC9, 0xA3, 0xC6, 0x8B, 0x68};  //COM08
uint8_t *Arduino_ESP[]    = { Arduino_ESP01, Arduino_ESP02 };
uint8_t *broadcastAddress   = Arduino_ESP[1];

// REPLACE WITH THE "BUTTON-PINS" ON YOUR BOARD
 int pinButtonGreen   = 4;
 int pinButtonYellow  = 19;
 int pinButtonRed     = 18;

// REPLACE WITH THE "LED-PINS" ON YOUR BOARD
 int pinLEDgreen      = 23;
 int pinLEDyellow     = 19;
 int pinLEDred        = 18;

 bool b_button_green_pressed;
 bool b_button_yellow_pressed;
 bool b_button_red_pressed;

// Interrupt-Service-Routine vor "green_button_pressed"
 void IRAM_ATTR isr_green() {
  b_button_green_pressed  = true;
  b_button_yellow_pressed = false;
  b_button_red_pressed    = false;
  Serial.printf("Button green has been pressed in interrupt-service-routine \n");
}

// Interrupt-Service-Routine vor "yellow_button_pressed"
 void IRAM_ATTR isr_yellow() {
  b_button_yellow_pressed = true;
  b_button_green_pressed  = false;
  b_button_red_pressed    = false;
  Serial.printf("Button yellow has been pressed in interrupt-service-routine \n");
}

// Interrupt-Service-Routine vor "yellow_button_pressed"
 void IRAM_ATTR isr_red() {
  b_button_red_pressed    = true;
  b_button_green_pressed  = false;
  b_button_yellow_pressed = false;
  Serial.printf("Button red has been pressed in interrupt-service-routine \n");
}

void setup() {
  Serial.begin(115200);

  pinMode(pinLEDgreen, OUTPUT);
  pinMode(pinLEDyellow, OUTPUT);
  pinMode(pinLEDred, OUTPUT);

  //typedef Structure  to send data - must match the receiver structure
typedef struct struct_message {

 bool  b_lamp_green;
 bool  b_lamp_green_blink;
 float f_lamp_green_timer;

 bool  b_lamp_yellow;
 bool  b_lamp_yellow_blink;
 float f_lamp_yellow_timer;

 bool  b_lamp_red;
 bool  b_lamp_red_blink;
 float f_lamp_red_timer;

 bool  b_button_yellow_pressed;
 bool  b_button_red_pressed;
 bool  b_button_green_pressed;

 bool  b_horn;
 float f_horn_timer;

} struct_message;

// Create a objekt of type "struct_message" called "myStruct"
/// volatile struct_message myStruct;
struct_message myStruct;

// Initiate the struct called "myStruct" to hold all the parameters

  myStruct.b_lamp_green             = false;
  myStruct.b_lamp_green_blink       = false;
  myStruct.f_lamp_green_timer       = 1;

  myStruct.b_lamp_yellow            = false;
  myStruct.b_lamp_yellow_blink      = false;
  myStruct.f_lamp_yellow_timer      = 2;

  myStruct.b_lamp_red               = false;
  myStruct.b_lamp_red_blink         = false;
  myStruct.f_lamp_red_timer         = 3;

  myStruct.b_button_yellow_pressed  = false;
  myStruct.b_button_red_pressed     = false;
  myStruct.b_button_green_pressed   = false;

  myStruct.b_horn                   = false;
  myStruct.f_horn_timer             = 1;

// Attach Interrupt-Method green on Setup
  attachInterrupt(pinButtonGreen, isr_green, RISING);

// Attach Interrupt-Method yellow on Setup
  attachInterrupt(pinButtonYellow, isr_yellow, RISING);

// Attach Interrupt-Method red on Setup
  attachInterrupt(pinButtonRed, isr_red, RISING);

    // Set device as a Wi-Fi Station
  WiFi.mode(WIFI_STA);
}

void loop() {

// do nothing and wait only vor Interrupt

// The Interrupt green is true
    if (b_button_green_pressed == true) {
      Serial.println("Button green has been pressed in loop");
      b_button_green_pressed  = false;
      detachInterrupt(pinButtonGreen);
      Serial.println("Interrupt green Detached to prevent multiple klicks on button!");
      // Attach Interrupt-Method again on Loop after delay
      delay(200);
      attachInterrupt(pinButtonGreen, isr_green, RISING);
      Serial.println("Interrupt green atached again!");
     }

// The Interrupt yellow is true
    if (b_button_yellow_pressed == true) {
      Serial.println("Button yellow has been pressed in loop");
      b_button_yellow_pressed  = false;
      detachInterrupt(pinButtonYellow);
      Serial.println("Interrupt yellow Detached to prevent multiple klicks on button!");
      // Attach Interrupt-Method again on Loop after delay
      delay(200);
      attachInterrupt(pinButtonYellow, isr_green, RISING);
      Serial.println("Interrupt green atached again!");
     }

// The Interrupt red is true
    if (b_button_red_pressed == true) {
      Serial.println("Button red has been pressed in loop");
      b_button_red_pressed  = false;
      detachInterrupt(pinButtonRed);
      Serial.println("Interrupt red Detached to prevent multiple klicks on button!");
      // Attach Interrupt-Method again on Loop after delay
            delay(200);
      attachInterrupt(pinButtonRed, isr_red, RISING);
     }

}
