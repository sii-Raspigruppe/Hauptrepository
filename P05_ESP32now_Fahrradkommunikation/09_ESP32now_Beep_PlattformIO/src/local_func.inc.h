/*
 * Hier sind alle selbst erstellten Funktionen
 *
 * 04.03.2022 - Udo Besenreuther
 */

 void beep(int n, uint16_t *t) {

   // Testausgabe, ob auch das richtige Beep-Array ankommt
   // Das Beep-Array - Anz  4 Elemente: 200:100:200:100:
   // Das Beep-Array - Anz  6 Elemente: 200:200:200:200:200:200
   Serial.printf("Das Beep-Array - Anz %3d  Elemente: ",n);
   for( int i=0; i < n; i++) {
      Serial.printf("%3d:",t[i]);
   }
   Serial.println();

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


 // Scan for slaves in AP mode
 void ScanForSlave() {
   int8_t scanResults = WiFi.scanNetworks();
   // reset on each scan
   bool slaveFound = 0;
   //memset(&slave, 0, sizeof(slave));

   Serial.println("");
   if (scanResults == 0) {
     Serial.println("No WiFi devices in AP Mode found");
   } else {
     Serial.print("Found "); Serial.print(scanResults); Serial.println(" devices ");
     for (int i = 0; i < scanResults; ++i) {
       // Print SSID and RSSI for each device found
       String SSID = WiFi.SSID(i);
       int32_t RSSI = WiFi.RSSI(i);
       String BSSIDstr = WiFi.BSSIDstr(i);

       if (true) {
         Serial.print(i + 1);
         Serial.print(": ");
         Serial.print(SSID);
         Serial.print(" (");
         Serial.print(RSSI);
         Serial.print(")");
         Serial.println("");
       }
       delay(10);
       // Check if the current device starts with `Slave`
       if (SSID.indexOf("Slave") == 0) {
         // SSID of interest
         Serial.println("Found a Slave.");
         Serial.print(i + 1); Serial.print(": "); Serial.print(SSID); Serial.print(" ["); Serial.print(BSSIDstr); Serial.print("]"); Serial.print(" ("); Serial.print(RSSI); Serial.print(")"); Serial.println("");
         // Get BSSID => Mac Address of the Slave
         int mac[6];
         if ( 6 == sscanf(BSSIDstr.c_str(), "%x:%x:%x:%x:%x:%x",  &mac[0], &mac[1], &mac[2], &mac[3], &mac[4], &mac[5] ) ) {
           for (int ii = 0; ii < 6; ++ii ) {
             //slave.peer_addr[ii] = (uint8_t) mac[ii];
           }
         }

         //slave.channel = CHANNEL; // pick a channel
         //slave.encrypt = 0; // no encryption

         slaveFound = 1;
         // we are planning to have only one slave in this example;
         // Hence, break after we find one, to be a bit efficient
         break;
       }
     }
   }

   if (slaveFound) {
     Serial.println("Slave Found, processing..");
   } else {
     Serial.println("Slave Not Found, trying again.");
   }

   // clean up ram
   WiFi.scanDelete();
 }
