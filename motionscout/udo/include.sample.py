#import MySQL-Lib
import mysql.connector
# MySQL-Verbindung herstellen
mydb = mysql.connector.connect(
  host="localhost",
  user="Username",
  password="Passwort",
  database="motionscout"
)
#print(mydb)

# GPIO des aktuellen Aufbaus
GPIO_PIR = 14 #PIN 11
GPIO_LED = 5  #PIN 29

