# Raspberry installieren
## Raspi-Imager herunterladen
Raspi-OS 32bit auswählen und auf SD-Karte schreiben

## Linux-Pakete auf den neuesten Stand bringen
 `sudo apt update`

## Apache installieren
https://raspberrytips.com/web-server-setup-on-raspberry-pi/#PHPMyAdmin
`sudo apt install apache2 -y`

Homeverzeichnis: /var/www/html

Berechtigungen anpassen: 
``` 
sudo usermod -a -G www-data pi
sudo chown www-data:www-data -R /var/www/html
sudo reboot now
```

## php installieren
```
sudo apt install php -y
nano /var/www/html/pi.php
  <?php
  echo phpinfo();
http://localhost/pi.php
```
PHP-Version auf der angezeigten Seite merken (z.B. 7.4.25)

## MySQL installieren
https://raspberrytips.com/install-mariadb-raspberry-pi/
```
sudo apt install -y mariadb-server
sudo mysql_secure_installation
```
Alle Fragen mit y beantworten und ggf. Passwort für DB eingeben

## PhpMyAdmin installieren
offiizieller Weg, hat nicht geklappt 
```
sudo apt install -y phpmyadmin
```
manuelle Installation als PHP-Programm:
```
PhpMyAdmin hier herunterladen: https://www.phpmyadmin.net/downloads/
Dateien in das Verzeichnis `/var/www/html/phpmyadmin` entpacken
Adresse: http://localhost/phpmyadmin
```
## Python in Apache lauffähig machen
```
sudo apt install libapache2-mod-python
sudo nano /etc/apache2/sites-available/default

        ...
      <Directory /var/www/>
              Options Indexes FollowSymLinks
              AllowOverride None
              Require all granted
              Order allow, deny
              allow from all
              AddHandler mod_python .py
              PythonHandler mod_python.publisher
              PythonDebug On
      </Directory>

       ...
```
