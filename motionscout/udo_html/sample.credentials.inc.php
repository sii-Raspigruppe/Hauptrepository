<?php

	/*
	 * Konfiguration funktioniert sowohl lokal, als auch auf dem Webserver.
	 * Im ersten if-Zweig die Userdaten der lokalen Datenbank eintragen,
	 * im zweiten if-Zweig den Zugang zur Datenbank auf dem Webserver eintragen.
	 * 
	 * Udo Besenreuther, Dez 2021
	 * 
	 */

	//echo $_SERVER[HTTP_HOST]." - ".strpos("-".$_SERVER[HTTP_HOST],"localhost"); exit;
	if (strpos("-".$_SERVER['HTTP_HOST'],"localhost") > 0) {
		$db_server   = "localhost";
		$db_database = "motionscout";
		$db_user     = "db_user";
		$db_passw    = "db_passwort";
	} elseif (strpos("-".$_SERVER['HTTP_HOST'],"webseite.de") > 0) {
		$db_server   = "localhost";
		$db_database = "dbweb_name";
		$db_user     = "dbweb_user";
		$db_passw    = "dbweb_password";
	}
