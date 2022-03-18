/*
 * Hier sind alle selbst erstellten Funktionen
 *
 * 04.03.2022 - Udo Besenreuther
 */
 void printArray(int n, int *t) {
   Serial.printf("Das Beep-Array - Anz %3d  Elemente: ",n);
   for( int i=0; i < n; i++) {
      Serial.printf("%3d:",t[i]);
   }
   Serial.println();
 }

 void beepOn_add( int dauer) {
   int temparray[anzahl];
   int len = sizeof(beepOn);
   printf(" beepOn: len: %2d - ", len);
   for (int i=0; i<len; i++) {
     printf(" anzahl:%3d len:%2d i:%2d %3d %3d\n", anzahl, len, i, dauer, beepOn[i]);
     temparray[i] = beepOn[i];
   }
   delay(1000);
 }
 void beepOff_add(int dauer) {
   int len = sizeof(beepOff);
   beepOff[len] = dauer;
   printf(" beepOff: len: %2d - ", len);
   printArray(sizeof(beepOff), beepOff);
   delay(1000);
 }


 void beep(int n, int *t) {

   // Testausgabe, ob auch das richtige Beep-Array ankommt
   // Das Beep-Array - Anz  4 Elemente: 200:100:200:100:
   // Das Beep-Array - Anz  6 Elemente: 200:200:200:200:200:200
   printArray(n, t);

   // Das Beep-Array der Reihe nach abarbeiten
   dauer = millis();
   anzahl = 0;
   for( int i=0; i < n; i++) {
     anzahl += 1;
     beepOn_add(dauer);
     dauer  += t[i];
     anzahl += 1;
     beepOff_add(dauer);
     i++;
     dauer  += t[i];
   }

   // Das Beep-Array der Reihe nach abarbeiten
   for( int i=0; i < n; i++) {
     digitalWrite(pinBeep, HIGH);
     delay(t[i]);
     i++;
     digitalWrite(pinBeep, LOW);
     delay(t[i]);
   }
 }


 void updateDisplay(){
   // Steuern der LEDs auf Grund der Eingaben
   Serial.printf("Status in Kommunikation: %1d / %1d / %1d \n\n", incomingStatus.green, incomingStatus.yellow, incomingStatus.red);

   // wenn gelb, zuerst mal nur die LED anmachen
   if (incomingStatus.yellow) {
     digitalWrite(pinLEDyellow, HIGH);
   } else {
     digitalWrite(pinLEDyellow, LOW);
   }
   // wenn rot, dann LED anmachen und beepen
   if (incomingStatus.red) {
     digitalWrite(pinLEDred, HIGH);
     beep(sizeof(timeBeepRed)/2, timeBeepRed);
   } else {
     digitalWrite(pinLEDred, LOW);

     // bei gelb und nicht rot die gelbe Anzahl beepen, da sonst die Anzahls Beeps summiert würde
     if (incomingStatus.yellow) {
       beep(sizeof(timeBeepYellow)/2, timeBeepYellow);
     }

   }
 }


 void LED_Test(uint delay1, uint delay2) {

   // Beim Start einmal alle LEDs ein- und ausschalten zum Test
   digitalWrite(pinLEDgreen,HIGH);
   delay(delay1);
   digitalWrite(pinLEDyellow,HIGH);
   delay(delay1);
   digitalWrite(pinLEDred,HIGH);
   delay(delay2);
   digitalWrite(pinLEDred,LOW);
   delay(delay1);
   digitalWrite(pinLEDyellow,LOW);
   delay(delay1);
   digitalWrite(pinLEDgreen,LOW);
 }

 void LED_Setup() {

   // LED-, Taster- und Beep-PINs definieren
   pinMode(pinGreen,  INPUT_PULLUP);
   pinMode(pinYellow, INPUT_PULLUP);
   pinMode(pinRed,    INPUT_PULLUP);

   pinMode(pinLEDgreen, OUTPUT);
   pinMode(pinLEDyellow, OUTPUT);
   pinMode(pinLEDred, OUTPUT);

   pinMode(pinBeep, OUTPUT);
 }
