# Raspberry installieren - ENTWURF
## Linux-Pakete auf den neuesten Stand bringen
 `sudo apt update`

## Apache installieren
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
`sudo apt install -y mariadb-server`

## falls Passwort nicht abgefragt wude, so setzen
```
sudo /etc/init.d/mysql stop
sudo mysqld_safe – skip-grant-tables &
```


sudo mysqladmin -u root -p flush-privileges password 'rootoor'

