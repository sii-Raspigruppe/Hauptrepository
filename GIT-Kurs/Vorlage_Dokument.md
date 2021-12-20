
1. IOBroker Allgemein:

Iobroker ist mittlerweile eine der beliebtesten Softwarelösungen für die Geräteautomatisierung im smart home. Denn hiermit kann ich eine Plattform erschaffen, wo Geräte unterschiedlicher Hersteller und Protokolle einfach miteinander verbunden werden können.

Also z.B. ein Aquara Bewegungsmelder, der das Zigbee Protokoll nutzt, mit einer Yeelight Lampe, die über WLAN angesteuert wird.

Es gibt mittlerweile weit über 300 Adapter zur Integration von Herstellern und die Zahl steigt stetig. Und das beste daran, iobroker ist in der Grundinstallation, die für die meisten ausreichend ist, kostenlos.

In diesem Video zeige ich dir Schritt für Schritt, wie die Installation von iobroker aktuell auf einem Raspberry PI4 durchgeführt wird.


2. Video Tutorial: 
https://www.youtube.com/watch?v=rW3qjP2Yins&t=7s


3. Installation unter Putty:

Die folgenden Kommandozeilen können per drag & drop in putty eingefügt werden.

Jede Zeile ist mit Enter abzuschießen!
sudo apt-get update && sudo apt-get dist-upgrade
sudo apt-get --purge remove node
sudo apt-get --purge remove nodejs
sudo apt autoremove
sudo reboot

Als nächstes installieren wir das node.js repository. Zum Zeitpunkt des Tutorials ist dies in der Version 13.x verfügbar. Falls es eine aktuellere Version gibt, kannst du einfach die nachfolgenden Pfade entsprechend anpassen.


3b: Information
Auf dieser Seite findest du die aktuelle Version von nodejs:
https://github.com/nodesource/distributions


curl -sL https://deb.nodesource.com/setup_13.x | sudo -E bash –
sudo apt-get install -y nodejs
sudo -H npm install -g npm@6
curl -sL https://iobroker.net/install.sh | bash -


3c: Information
Hier auch entsprechend auf aktuelle Version prüfen und ggf. anpassen:
https://nodejs.org/dist/latest



wget https://nodejs.org/dist/latest/node-v13.13.0-linux-armv7l.tar.gz
tar -xzf node-v13.13.0-linux-armv7l.tar.gz
cd node-v13.13.0-linux-armv7l/
sudo cp -R * /usr/local/

…und damit ist die Installation abgeschlossen 

3c: nicht vergessen
nicht vergessen... wenn dir dieses Tutorial geholfen hat, dann gib mir doch bitte ein Feedback in Form von Kommentar oder noch besser: ein YouTube Abo!


4. Hardware
Raspberry Pi4 (4GB): https://amzn.to/2YlmC40
Raspberry PI4 Gehäuse: https://amzn.to/2SlpVnW
Raspberry PI4 Komplettset: https://amzn.to/3aPBsCl

5. Links:
Adapter Liste: http://download.iobroker.net/list.html

6. Download:

Etcher: https://www.balena.io/etcher/
RaspbianBuster: https://www.raspberrypi.org/downloads…
Putty: https://putty.org/