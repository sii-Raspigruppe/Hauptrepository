//-------------------- Akkutester -------------------------------------
//
//-------------- verwendet wurde ein ESP8266 --------------------------
// 
// LED Bedeutung:          
// Rote  LED brennt:   Lastwiderstand wurde abgeschaltet
// Grüne LED brennt:   Die Endladespannung wurde erreicht Kapazitätsmessung beendet
// Rot+Grün  brennt:   INA Overflow zu hoher Strom  Messgerät wird abgeschaltet
//
//---------------------------------------------------------------------
#include <Wire.h>

//----------------------------------------------------------------------
//                      Programm Variable 
//----------------------------------------------------------------------
unsigned long previousMillis = 0;
unsigned long currentMillis  = 0;
float shuntvoltage    = 0;
float busvoltage      = 0;
float current_mA      = 0;
float current_mA_old  = 0;
float current_mAh     = 0;
float loadvoltage     = 0;
float loadvoltage_old = 0;
float energy_Wh       = 0;
float dU              = 0;
float dI              = 0;
float Ri              = 0;    // Innenwiderstand
int count             = 10;   // immer mit Messung ohne Last beginnen
const int LedGreen    = D7;   // Anzeige- Entlade-Ende
const int LedRed      = D8;   // Anzeige Widerstands-Messung (Lastabwurf)
const int LDoff       = D4;   // Relais- Ansteuerung Lastabwurf
int Interval          = 1000; // 1sec Zeitinterval
bool ina219_overflow  = false;
bool Stop             = false;
bool RiMess           = true;  // Innenwiderstand messen = true  Widersatandsmessung wird durchgeführt

//---------------------------------------------------------------------
//                   Display SSD1306 Einstellung 
//---------------------------------------------------------------------
#include <Adafruit_SSD1306.h>
#define SCREEN_WIDTH 128 // OLED display width, in pixels
#define SCREEN_HEIGHT 64 // OLED display height, in pixels
Adafruit_SSD1306 display(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, -1);
//----------------------------------------------------------------------
//                  WiFi umd MQTT Einstellungen 
//----------------------------------------------------------------------
#include <ESP8266WiFi.h>
#include <PubSubClient.h>
const char* SSID = "SSID Netzwerk";         								// Hier entsprechende Werte einsetzen !!!!!!
const char* PSK  = "Passwort";
const char* MQTT_BROKER = "IP Adresse";

WiFiClient espClient;
PubSubClient client(espClient);
long lastMsg = 0;
char msg[50];
int value = 0;
//------------------------------------------------------------------
//                     INA Einstellung 
//------------------------------------------------------------------
/* There are several ways to create your INA219 object:
 * INA219_WE ina219 = INA219_WE()              -> uses Wire / I2C Address = 0x40
 * INA219_WE ina219 = INA219_WE(ICM20948_ADDR) -> uses Wire / I2C_ADDRESS
 * INA219_WE ina219 = INA219_WE(&wire2)        -> uses the TwoWire object wire2 / I2C_ADDRESS
 * INA219_WE ina219 = INA219_WE(&wire2, I2C_ADDRESS) -> all together
 * Successfully tested with two I2C busses on an ESP32
 */
#include <INA219_WE.h>  												// siehe https://wolles-elektronikkiste.de/
#define I2C_ADDRESS 0x40  
INA219_WE ina219 = INA219_WE(I2C_ADDRESS);
//------------------------------------------------------------------
//             SETUP
//------------------------------------------------------------------
void setup() {
  Serial.begin(115200);
  display.begin(SSD1306_SWITCHCAPVCC, 0x3C);
  
  setup_wifi();
  client.setServer(MQTT_BROKER, 1883); 
  
  pinMode(LedGreen, OUTPUT); 
  pinMode(LedRed,   OUTPUT);
  pinMode(LDoff,    OUTPUT);
  digitalWrite(LDoff, HIGH);
  Wire.begin();
  
  if(!ina219.init()){
    Serial.println("INA219 nicht verbunden!");
  }
  else{
    Serial.println("INA219 verbunden!");
  }
  
  /* Set ADC Mode for Bus and ShuntVoltage
  * Mode *            * Res / Samples *       * Conversion Time *
  BIT_MODE_9        9 Bit Resolution             84 �s
  BIT_MODE_10       10 Bit Resolution            148 �s  
  BIT_MODE_11       11 Bit Resolution            276 �s
  BIT_MODE_12       12 Bit Resolution            532 �s  (DEFAULT)
  SAMPLE_MODE_2     Mean Value 2 samples         1,06 ms
  SAMPLE_MODE_4     Mean Value 4 samples         2,13 ms
  SAMPLE_MODE_8     Mean Value 8 samples         4,26 ms
  SAMPLE_MODE_16    Mean Value 16 samples        8,51 ms     
  SAMPLE_MODE_32    Mean Value 32 samples        17,02 ms
  SAMPLE_MODE_64    Mean Value 64 samples        34,05 ms
  SAMPLE_MODE_128   Mean Value 128 samples       68,10 ms
  */
  ina219.setADCMode(SAMPLE_MODE_128); // choose mode and uncomment for change of default
  
  /* Set measure mode
  POWER_DOWN - INA219 switched off
  TRIGGERED  - measurement on demand
  ADC_OFF    - Analog/Digital Converter switched off
  CONTINUOUS  - Continuous measurements (DEFAULT)
  */
  //ina219.setMeasureMode(POWER_DOWN);  
  
  ina219.setMeasureMode(TRIGGERED); // Triggered measurements for this example
  
  /* Set PGain
  * Gain *  * Shunt Voltage Range *   * Max Current *
   PG_40       40 mV                    0,4 A
   PG_80       80 mV                    0,8 A
   PG_160      160 mV                   1,6 A
   PG_320      320 mV                   3,2 A (DEFAULT)
  */
   ina219.setPGain(PG_40); // choose gain and uncomment for change of default
  
  /* Set Bus Voltage Range
   BRNG_16   -> 16 V
   BRNG_32   -> 32 V (DEFAULT)
  */
  ina219.setBusRange(BRNG_16); // choose range and uncomment for change of default
  
  /* If the current values delivered by the INA219 differ by a constant factor
     from values obtained with calibrated equipment you can define a correction factor.
     Correction factor = current delivered from calibrated equipment / current delivered by INA219
  */
  // ina219.setCorrectionFactor(0.98); // insert your correction factor if necessary 
}
//-------------------------------------------------------------------------------------------
//                              WiFi Setup
//-------------------------------------------------------------------------------------------
void setup_wifi() {
  delay(10);
  Serial.println();
  Serial.print("Connecting to ");
  Serial.println(SSID);

  WiFi.begin(SSID, PSK);

  while (WiFi.status() != WL_CONNECTED) {
    delay(100);
    Serial.print(".");
  }
  Serial.println("");
  Serial.println("WiFi connected");
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());  
}
//---------------------------------------------------------------------------------------
void reconnect() {
  while (!client.connected()) {
    Serial.print("Reconnecting...");
    if (!client.connect("ESP8266Client")) {
      Serial.print("failed, rc=");
      Serial.print(client.state());
      Serial.println(" retrying in 5 seconds");
      delay(5000);
    }
  }
}
//------------------------------------------------------------------------------------------------
//                Programmschleife  
//------------------------------------------------------------------------------------------------
void loop() {
  if (Stop){
    return;          // Endlosschleife wenn Messung beendet ist
  }

  if (!client.connected()) {
    reconnect();
  }
  client.loop();
  
  currentMillis = millis();
  if (currentMillis - previousMillis >= Interval)    // jede Sekunde 
  {
    previousMillis = currentMillis;
    count=count+1;
    //Serial.println(count);
    if (count >= 6 && RiMess == true)                                   // Messung des Innenwiderstandes alle 5 Sekunden
    {
      digitalWrite(LDoff, LOW);                                         // Relais_ Lastabwurf einschalten  
      digitalWrite(LedRed,HIGH);                                        // Rote LED einschalten
      delay(50);
      ina219valuesRi();
      displaydata_Ri();
      client.publish("/SmartHome/Akku/Innenwiderstand Ohm", String(Ri).c_str()); 
      delay(Interval*2);                                                 // 2 sec Innenwiderstand anzeigen
      digitalWrite(LDoff, HIGH);                                         // Relais_ Lastabwurf ausschalten
      digitalWrite(LedRed,LOW);       
      count = 0;
      delay(50);
    }
    else
    {    
      loadvoltage_old = loadvoltage;                                     // Messung der Kapazität
      current_mA_old  = current_mA;
      //analogWrite(LDoff,127);                   // 50% Duty Cycle INA zeigt den halben Strom, Netzgerät den ganzen Strom ???
      ina219values();
      displaydata();
      client.publish("/SmartHome/Akku/Strom mA",  String(current_mA).c_str());       // MQTT Übertragung an IoBroker
      client.publish("/SmartHome/Akku/Spannung Volt", String(loadvoltage).c_str()); 
      client.publish("/SmartHome/Akku/Kapazitaet mAh", String(current_mAh).c_str());   
      check_loadvoltage_min();
      delay(5);  
    }   
  } 
}
//--------------------------------------------------------------------------------------------------------
//                           Display Anzeige bei Kapazitätsmessung 
//--------------------------------------------------------------------------------------------------------
void displaydata() {
  display.clearDisplay();
  display.setTextColor(WHITE);
  display.setTextSize(1);
  display.setCursor(0, 0);
  display.println(loadvoltage,3);
  display.setCursor(35, 0);
  display.println("V");
  display.setCursor(50, 0);
  display.println(current_mA);
  display.setCursor(95, 0);
  display.println("mA");
  display.setTextSize(2);
  display.setCursor(0, 20);
  display.println(current_mAh);
  display.setCursor(90, 20);
  display.println("mAh");
  display.setCursor(0, 50);
  display.println(energy_Wh,3);
  display.setCursor(100, 50);
  display.println("Wh");
  display.display();
}
//-----------------------------------------------------------------------------------------------------
//           Display Anzeige bei Widerstands-Messung
//-----------------------------------------------------------------------------------------------------
void displaydata_Ri() {
  display.clearDisplay();
  display.setTextColor(WHITE);
  display.setTextSize(1);
  display.setCursor(0, 0);
  display.println(loadvoltage,3);
  display.setCursor(35, 0);
  display.println("V");
  display.setCursor(50, 0);
  display.println(current_mA);
  display.setCursor(95, 0);
  display.println("mA");
  display.setTextSize(2);
  display.setCursor(0, 20);
  display.println(Ri,3);
  display.setCursor(90, 20);
  display.println("Ohm");  
  display.display();
}
//-------------------------------------------------------------------------------------------------
//             INA WERTE lesen mit Laststrom
//-------------------------------------------------------------------------------------------------
void ina219values() {
  ina219.startSingleMeasurement(); // triggers single-shot measurement and waits until completed
  shuntvoltage    = ina219.getShuntVoltage_mV();
  busvoltage      = ina219.getBusVoltage_V();
  loadvoltage     = busvoltage + shuntvoltage/1000;         // mVolt
  current_mA      = ina219.getCurrent_mA();
  current_mAh     = current_mAh + current_mA*Interval/3600000;  // mAh
  energy_Wh       = current_mAh*loadvoltage/1000;               // Watth
  loadvoltage_old = loadvoltage;
  current_mA_old  = current_mA;
  ina219_overflow = ina219.getOverflow(); 
  //check_ina_overflow();    

  if (count == 1){
    Serial.println();
    Serial.println(" Kapazitaets- Messung ");
    Serial.print(" Shunt    mVolt = ");Serial.println(shuntvoltage);
    Serial.print(" Bus       Volt = ");Serial.println(busvoltage);
    Serial.print(" Load      Volt = ");Serial.println(loadvoltage);
    Serial.print(" Strom       mA = ");Serial.println(current_mA);
    Serial.print(" Kapazitaet mAh = ");Serial.println( current_mAh);
    Serial.print(" Kapazitaet  Wh = ");Serial.println( energy_Wh); 
  }       
}
//-------------------------------------------------------------------------------------------------
//             INA Werte lesen ohne Laststrom
//------------------------------------------------------------------------------------------------
void ina219valuesRi() {
  ina219.startSingleMeasurement(); // triggers single-shot measurement and waits until completed
  shuntvoltage = ina219.getShuntVoltage_mV();
  busvoltage   = ina219.getBusVoltage_V();
  loadvoltage  = busvoltage + shuntvoltage/1000; //Volt 
  current_mA   = ina219.getCurrent_mA(); 
  dU = loadvoltage - loadvoltage_old;
  dI = (current_mA_old - current_mA);
  Ri = dU / dI*1000;
  
  Serial.println();
  Serial.println(" Innenwiderstands-Messung ");
  Serial.print(" Shunt    mVolt =");Serial.println(shuntvoltage);
  Serial.print(" Bus       Volt = ");Serial.println(busvoltage);
  Serial.print(" Load      Volt = ");Serial.println(loadvoltage);
  Serial.print(" delta U   Volt = ");Serial.println(dU);
  Serial.print(" delta I     mA = ");Serial.println(dI);  
  Serial.print(" Widerstand Ohm = ");Serial.println(Ri,5);
      
}
//--------------------------------------------------------------------------------------------------
//             auf Entlade Spannung und Overflow pruefen
//--------------------------------------------------------------------------------------------------
void check_loadvoltage_min(){
  if (loadvoltage < -0.05){
    digitalWrite(LedGreen, HIGH);
    digitalWrite(LDoff, LOW);
    Stop = true;
  }
  else{
    digitalWrite(LedGreen, LOW); 
    digitalWrite(LDoff, HIGH);
    if (ina219_overflow){
      digitalWrite(LedRed, HIGH);
      digitalWrite(LedGreen, HIGH);
      Serial.println(" Overflow! Choose higher PGAIN");
      Stop = true; 
    }
  }
  delay(10); 
}
//------------------------------------------------------------------------------------------------------
//              Ende
//------------------------------------------------------------------------------------------------------