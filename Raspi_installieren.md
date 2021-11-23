# Raspberry installieren - ENTWURF
## Raspi-Imager herunterladen
Raspi-OS 32bit auswählen und auf SD-Karte schreiben

## Linux-Pakete auf den neuesten Stand bringen
 `sudo apt update`

## Apache installieren
https://raspberrytips.com/install-mariadb-raspberry-pi/
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
```
sudo apt install -y mariadb-server
sudo mysql_secure_installation
```
Alle Fragen mit y beantworten und ggf. Passwort für DB eingeben

## PhpMyAdmin installieren
```
https://raspberrytips.com/install-mariadb-raspberry-pi/
```


sudo mysqladmin -u root -p flush-privileges password 'rootoor'

