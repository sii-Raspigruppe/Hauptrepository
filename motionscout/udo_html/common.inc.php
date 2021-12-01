<?php
	include_once("debugvar.php");

	if(0) header("Location:wartung.php");
	
	$_TEST = true;
	$b = "<br/>\n";

    foreach($_REQUEST as $wert=>$R) {
		$Rwert  = "REQ_".$wert;
		$REQ[$wert] = preventinjection($R);
		//debugecho (__LINE__,": ".debugvar($REQ[$wert]));
	}

	//SESSION prüfen
    if (0) {
        session_start();
        if (!$indexseite and !isset($_SESSION['person_id'])) header("Location:./index.php");
        //session_cache_limiter(240);
    }

	if (1 or isset($REQ['test']) or strpos($_SERVER['REQUEST_URI'],"_test") > 0 or strpos($_SERVER['REQUEST_URI'],"_int") > 0) {
		$_TEST = true;
        echo($b."Testmodus aktiv $b");
	} else {
		$_TEST = false;
	}

	//ERROR-Reporting
	error_reporting(~E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);
	ini_set('display_errors',0);

	if ($_TEST) {
		//
		echo "Testumgebung".$b;
		error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
		ini_set('display_errors', 1);
		ini_set('error_log', './php-errors.log');
	}
	
	if (0) {
		decho(__FILE__,__LINE__,$_SERVER['REQUEST_URI']." - ".strpos($_SERVER['REQUEST_URI'],"_test"));
		if ($_TEST) 
			decho(__FILE__,__LINE__,"Testumgebung");
		decho(__FILE__,__LINE__,debugvar($REQ,'$REQ').debugvar($_SESSION));
	}


	//Warten wenn Formulare zu oft geschickt werden
	if (!$_TEST) $warten_bei_accessdenied = 360; 	else $warten_bei_accessdenied = 20;
	if (!$_TEST) $anzahl_bis_accessdenied = 5;		else $anzahl_bis_accessdenied = 3;
	
	//Maximale Anzahl der möglichen Buchungen pro Team
	if (!$_TEST) $max_anzahl = 3;		else $max_anzahl = 3;
	
	//Standard-EMail-Adresse
	$infoemail = "sc2021@rangertools.de";
	$infosubject = "Sommercamp 2021 - Info";
	
	//Stichtag füe 18 Alter Berechnung
	$stichtag = "26.07.2021";

	// Felder zur Pflege der Aktionen
	$aktion_felder = array();
	$n = 0 ;
	$aktion_felder[$n]["text"]="ID";
	$aktion_felder[$n]["id"]  ="aktion_id";
	$aktion_felder[$n]["type"]="hidden";
	$n++;
	$aktion_felder[$n]["text"]="Kennung";
	$aktion_felder[$n]["id"]  ="aktion_kennung";
	$aktion_felder[$n]["type"]="text";
	$aktion_felder[$n]["must"]=1;
	$n++;
	$aktion_felder[$n]["text"]="Thema";
	$aktion_felder[$n]["id"]  ="aktion_thema";
	$aktion_felder[$n]["type"]="text";
	$aktion_felder[$n]["must"]=1;
	$n++;
	$aktion_felder[$n]["text"]="Aktion Name";
	$aktion_felder[$n]["id"]  ="aktion_name";
	$aktion_felder[$n]["type"]="text";
	$aktion_felder[$n]["must"]=1;
	$n++;
	$aktion_felder[$n]["text"]="verantw. Leiter";
	$aktion_felder[$n]["id"]  ="aktion_leiter";
	$aktion_felder[$n]["type"]="text";
	$aktion_felder[$n]["must"]=1;
	$n++;
	$aktion_felder[$n]["text"]="Stamm (Leiter)";
	$aktion_felder[$n]["id"]  ="aktion_leiter_stamm";
	$aktion_felder[$n]["type"]="text";
	$n++;
	$aktion_felder[$n]["text"]="E-Mail Leiter";
	$aktion_felder[$n]["id"]  ="aktion_leiter_email";
	$aktion_felder[$n]["type"]="email";
	$n++;
	$aktion_felder[$n]["text"]="Handy Leiter";
	$aktion_felder[$n]["id"]  ="aktion_leiter_handy";
	$aktion_felder[$n]["type"]="text";
	/*$n++;
	$aktion_felder[$n]["text"]="Wart";
	$aktion_felder[$n]["id"]  ="aktion_wart";
	$aktion_felder[$n]["type"]="text";
	$n++;
	$aktion_felder[$n]["text"]="Stamm (Wart)";
	$aktion_felder[$n]["id"]  ="aktion_wart_stamm";
	$aktion_felder[$n]["type"]="text";
	$n++;
	$aktion_felder[$n]["text"]="E-Mail Wart";
	$aktion_felder[$n]["id"]  ="aktion_wart_email";
	$aktion_felder[$n]["type"]="email";
	$n++;
	$aktion_felder[$n]["text"]="Handy Wart";
	$aktion_felder[$n]["id"]  ="aktion_wart_handy";
	$aktion_felder[$n]["type"]="text";
	*/$n++;
	$aktion_felder[$n]["text"]="Bild";
	$aktion_felder[$n]["id"]  ="aktion_bild";
	$aktion_felder[$n]["type"]="text";
	$n++;
	$aktion_felder[$n]["text"]="Karte (jpg)";
	$aktion_felder[$n]["id"]  ="aktion_karte_jpg";
	$aktion_felder[$n]["type"]="text";
	$n++;
	$aktion_felder[$n]["text"]="Karte (pdf)";
	$aktion_felder[$n]["id"]  ="aktion_karte_pdf";
	$aktion_felder[$n]["type"]="text";
	$n++;
	$aktion_felder[$n]["text"]="Beschreibung";
	$aktion_felder[$n]["id"]  ="aktion_beschreibung";
	$aktion_felder[$n]["type"]="textarea";
	$aktion_felder[$n]["must"]=1;
	/*$n++;
	$aktion_felder[$n]["text"]="Voraussetzungen";
	$aktion_felder[$n]["id"]  ="aktion_voraussetzung";
	$aktion_felder[$n]["type"]="textarea";
	$n++;
	$aktion_felder[$n]["text"]="Anfahrt";
	$aktion_felder[$n]["id"]  ="aktion_anfahrt";
	$aktion_felder[$n]["type"]="text";
	*/$n++;
	$aktion_felder[$n]["text"]="GPS-Koordinaten";
	$aktion_felder[$n]["id"]  ="aktion_gps";
	$aktion_felder[$n]["type"]="text";
	$n++;
	$aktion_felder[$n]["text"]="UTM-Koordinaten";
	$aktion_felder[$n]["id"]  ="aktion_utm";
	$aktion_felder[$n]["type"]="text";
	/*$n++;
	$aktion_felder[$n]["text"]="Liegt an Route";
	$aktion_felder[$n]["id"]  ="aktion_route";
	$aktion_felder[$n]["type"]="textarea";
	*/$n++;
	$aktion_felder[$n]["text"]="Kosten pro Person";
	$aktion_felder[$n]["id"]  ="aktion_kosten";
	$aktion_felder[$n]["type"]="kosten";
	/*$n++;
	$aktion_felder[$n]["text"]="Genehmigung";
	$aktion_felder[$n]["id"]  ="aktion_genehmigung";
	$aktion_felder[$n]["type"]="text";
	$n++;
	$aktion_felder[$n]["text"]="Personen n&ouml;tig";
	$aktion_felder[$n]["id"]  ="aktion_anzahl";
	$aktion_felder[$n]["type"]="text";
	*/$n++;
	$aktion_felder[$n]["text"]="personen ideal";
	$aktion_felder[$n]["id"]  ="aktion_ideal";
	$aktion_felder[$n]["type"]="text";
	$n++;
	$aktion_felder[$n]["text"]="Personen maximal";
	$aktion_felder[$n]["id"]  ="aktion_max";
	$aktion_felder[$n]["type"]="text";

	$aktion_felder_editierbar = array();
	$n=0; $aktion_felder_editierbar[]='aktion_leiter';
	$n++; $aktion_felder_editierbar[]='aktion_leiter_stamm';
	$n++; $aktion_felder_editierbar[]='aktion_leiter_email';
	$n++; $aktion_felder_editierbar[]='aktion_leiter_handy';

	$n=0; $aktion_days[$n]["tag"]="17.04.2021";
	$n++; $aktion_days[$n]["tag"]="18.04.2021";
	$n++; $aktion_days[$n]["tag"]="19.04.2021";
	//$n++; $aktion_days[$n]["tag"]="20.04.2021";
	//$n++; $aktion_days[$n]["tag"]="15.06.2017";

	$n=1; $aktion_slots[$n]["zeit"]="09:00";
	$n++; $aktion_slots[$n]["zeit"]="09:30";
	$n++; $aktion_slots[$n]["zeit"]="10:00";
	$n++; $aktion_slots[$n]["zeit"]="10:30";
	$n++; $aktion_slots[$n]["zeit"]="11:00";
	$n++; $aktion_slots[$n]["zeit"]="11:30";
	$n++; $aktion_slots[$n]["zeit"]="12:00";
	$n++; $aktion_slots[$n]["zeit"]="12:30";
	$n++; $aktion_slots[$n]["zeit"]="13:00";
	$n++; $aktion_slots[$n]["zeit"]="13:30";
	$n++; $aktion_slots[$n]["zeit"]="14:00";
	$n++; $aktion_slots[$n]["zeit"]="14:30";

	
	$stamm_funktionen = array(	0=>"-",
								"TN"=>"Teilnehmer",
								"JLU"=>"Juniorleiter (unter 18)",
								"JL"=>"Leiter (&uuml;ber 18)",
								"TLU"=>"Team-Leiter (unter 18)",
								"TL"=>"Team-Leiter",
								"HF"=>"Scout",
								"KÜ"=>"Küche",
								"SL"=>"Stammleiter",
								"SW"=>"Stammwart");
	
	$gebuehr = array(90=>"1. Kind: 90 &euro;",85=>"2. Kind: 85 &euro;", 80=>"3. Kind: 80 &euro;", 36=>"Mitarbeiter");
	
	// Felder zur Pflege der Personen
	$person_felder = array();
	$n = 0;
	$person_felder[$n]["text"]="Leiter/Teilnehmer";
	$person_felder[$n]["tipp"]="<super>+</super>Pflichtfelder für Minderjährige. &nbsp;&nbsp;<super>+*</super>Pflichtfelder für Erwachsene.";
	$person_felder[$n]["id"]  ="stamm_funktion";
	$person_felder[$n]["type"]="select";
	$person_felder[$n]["option"]=$stamm_funktionen;
	$person_felder[$n]["must"]=1;
	$person_felder[$n]["mamust"]=1;
	$n++;
	$person_felder[$n]["text"]="Stamm Nr.";
	$person_felder[$n]["id"]  ="stamm_nr";
	$person_felder[$n]["type"]="select";
	$person_felder[$n]["option"]=array(0=>"-","32"=>"&nbsp;&nbsp;32 - Asch","102"=>"102 - Ulm 1","221"=>"221 - Ulm 2");
	$person_felder[$n]["must"]=1;
	$person_felder[$n]["mamust"]=1;
	$n++;
	$person_felder[$n]["text"]="Team Name";
	$person_felder[$n]["id"]  ="team";
	$person_felder[$n]["type"]="text";
	$person_felder[$n]["must"]=1;
	$person_felder[$n]["mamust"]=1;
	$n++;
	$person_felder[$n]["text"]="m&auml;nnlich/weiblich";
	$person_felder[$n]["id"]  ="gender";
	$person_felder[$n]["type"]="select";
	$person_felder[$n]["option"]=array(0=>"-","m"=>"m&auml;nnlich","w"=>"weiblich","?"=>"unbestimmt");
	$person_felder[$n]["must"]=1;
	$person_felder[$n]["mamust"]=1;
	$n++;
	$person_felder[$n]["text"]="Vorname";
	$person_felder[$n]["id"]  ="vorname";
	$person_felder[$n]["type"]="text";
	$person_felder[$n]["must"]=1;
	$person_felder[$n]["mamust"]=1;
	$n++;
	$person_felder[$n]["text"]="Nachname";
	$person_felder[$n]["id"]  ="nachname";
	$person_felder[$n]["type"]="text";
	$person_felder[$n]["must"]=1;
	$person_felder[$n]["mamust"]=1;
	$n++;
	$person_felder[$n]["text"]="Geburtsdatum";
	$person_felder[$n]["id"]  ="geburtsdatum";
	$person_felder[$n]["type"]="datum";
	$person_felder[$n]["must"]=1;
	$person_felder[$n]["mamust"]=1;
	$n++;
	$person_felder[$n]["text"]="Strasse, Hausnummer";
	$person_felder[$n]["id"]  ="strasse";
	$person_felder[$n]["type"]="text";
	$person_felder[$n]["must"]=1;
	$n++;
	$person_felder[$n]["text"]="PLZ Ort";
	$person_felder[$n]["id"]  ="ort";
	$person_felder[$n]["type"]="text";
	$person_felder[$n]["must"]=1;
	$n++;
	$person_felder[$n]["text"]="Telefon (f&uuml;r Notf&auml;lle)";
	$person_felder[$n]["id"]  ="telefon";
	$person_felder[$n]["type"]="text";
	$person_felder[$n]["must"]=1;
	$n++;
	$person_felder[$n]["text"]="Handynummer";
	$person_felder[$n]["id"]  ="mobil";
	$person_felder[$n]["type"]="text";
	//$person_felder[$n]["must"]=1;
	$person_felder[$n]["mamust"]=1;
	$n++;
	$person_felder[$n]["text"]="E-Mail";
	$person_felder[$n]["id"]  ="email";
	$person_felder[$n]["type"]="email";
	$person_felder[$n]["must"]=1;
	$person_felder[$n]["mamust"]=1;
	$n++;
	$person_felder[$n]["text"]="T-Shirtgr&ouml;&szlig;e";
	$person_felder[$n]["id"]  ="tshirt";
	$person_felder[$n]["type"]="select";
	$person_felder[$n]["option"]=array(	0=>"-",
										"110"=>"110",
										"116"=>"116",
										"122"=>"122",
										"128"=>"128", 
										"134"=>"134",
										"140"=>"140", 
										"146"=>"146",
										"152"=>"152", 
										"158"=>"158",
										"164"=>"164", 
										"170"=>"170",
										"176"=>"176",
										"XXS"=>"XXS",
										"XS"=>"XS",
										"S"=>"S", 
										"M"=>"M",
										"L"=>"L", 
										"XL"=>"XL",
										"XXL"=>"XXL");
	$person_felder[$n]["must"]=1;
	$person_felder[$n]["mamust"]=1;
	$n++;
	$person_felder[$n]["text"]="Kind darf baden";
	$person_felder[$n]["id"]  ="darfbaden";
	$person_felder[$n]["type"]="select";
	$person_felder[$n]["option"]=array(0=>"-","J"=>"Ja","N"=>"Nein");
	$person_felder[$n]["must"]=1;
	$n++;
	$person_felder[$n]["text"]="Kind ist Schwimmer";
	$person_felder[$n]["id"]  ="schwimmer";
	$person_felder[$n]["type"]="select";
	$person_felder[$n]["option"]=array(0=>"-","J"=>"Ja","N"=>"Nein");
	$person_felder[$n]["must"]=1;
	$n++;
	$person_felder[$n]["text"]="Kind ben&ouml;tigt Medikamente";
	$person_felder[$n]["id"]  ="medikamente";
	$person_felder[$n]["type"]="textarea";
	$n++;
	$person_felder[$n]["text"]="Erlaubnis für med. Maßnahmen";
	$person_felder[$n]["tipp"]="Bei Verletzungen werden wir den Notarzt rufen, um bei kleinen Verletzungen, Kopfweh, Mückenstichen handlungsfähig zu bleiben, bitten wir um Zustimmung.";
	$person_felder[$n]["id"]  ="medmassnahmen";
	$person_felder[$n]["option"]=array( "vergabe" =>"ich erlaube den Teamleitern die regelmäßig nötigen Medikamente meinem Kind zu geben.",
										"erstvers"=>"ich erlaube dem Erste-Hilfe-Personal auf dem Camp mein Kind im Rahmen der Erstversorgung zu behandeln");
	$person_felder[$n]["type"]="check";
	$n++;
	$person_felder[$n]["text"]="Kind hat Allergien";
	$person_felder[$n]["id"]  ="allergien";
	$person_felder[$n]["type"]="textarea";
	$n++;
	$person_felder[$n]["text"]="Besondere Ern&auml;hrung";
	$person_felder[$n]["tipp"]="Wir bitten um Verständniss, dass unsere Möglichkeiten nicht unbegrenzt sind. Bei extremen Ernährungswünschen bitte die Lebensmittel selbst besorgen.";
	$person_felder[$n]["id"]  ="ernaehrung";
	$person_felder[$n]["option"]=array(	"Gl"=>"Glutenunvertr&auml;glichkeit",
										"Lac"=>"Laktoseintoleranz",
										"kS"=>"kein Schweinefleisch",
										"Veg"=>"Vegetarier",
										"Vga"=>"Veganer");
	$person_felder[$n]["type"]="check";
	$n++;
	$person_felder[$n]["text"]="Bemerkung";
	$person_felder[$n]["tipp"]="Zusätzliche Infos zur Ernährungs- oder Medizinthemen oder einfach Infos an die Campleitung.";
	$person_felder[$n]["id"]  ="bemerkung";
	$person_felder[$n]["type"]="textarea";
	$n++;
	$person_felder[$n]["text"]="Campgeb&uuml;hr";
	$person_felder[$n]["id"]  ="campgebuehr";
	$person_felder[$n]["type"]="select";
	$person_felder[$n]["option"]= $gebuehr;
	$person_felder[$n]["must"]=1;
	$n++;
	$person_felder[$n]["text"]="Einverständnis";
	$person_felder[$n]["tipp"]="Ohne Zustimmung ist eine Teilnahme am Camp leider nicht möglich. Aus rechtlicen Gründen müssen wir aber abfragen.";
	$person_felder[$n]["id"]  ="einverstanden";
	$person_felder[$n]["option"]=array( "daten" =>"ich Ich bin damit eiverstanden, dass die Daten gemäß BFP-DSVO gespeichert und verarbeitet werden.",
										"foto"  =>"Ich bin damit einverstanden, dass mein Kind fotografiert wird und ggf. das Foto im Rahmen der Rangeraktion veröffentlicht wird.");
	$person_felder[$n]["type"]="check";
	$person_felder[$n]["must"]=1;
	$person_felder[$n]["mamust"]=1;
	$n++;
	$person_felder[$n]["text"]="Teilnahme am Vorcamp";
	$person_felder[$n]["tipp"]="Für Mitarbeiter: bitte tragt ein, ab wann ihr auf dem Vorcamp seit und ob ihr ggf. Auto, anhänger, Material zur Verfügung stellt.";
	$person_felder[$n]["id"]  ="vorcamp";
	$person_felder[$n]["type"]="textarea";
	$n++;
	$person_felder[$n]["text"]="Username";
	$person_felder[$n]["tipp"]="Um die Infos auch für die nächsten Events nutzen zu können, ist es sinnvoll einen Usernamen und Passwort zu vergeben.";
	$person_felder[$n]["id"]  ="persusername";
	$person_felder[$n]["type"]="text";
	$n++;
	$person_felder[$n]["text"]="Passwort";
	$person_felder[$n]["tipp"]="Überschreibe die Punkte mit deinem Passwort. Punkte geben keinen Hinweis auf die Länges des Passworts.";
	$person_felder[$n]["id"]  ="perspassword";
	$person_felder[$n]["type"]="password";
	
	
	$person_felder_editierbar = array();
	$n=0; $person_felder_editierbar[]='leiter_vorname';
	$n++; $person_felder_editierbar[]='leiter_nachname';
	$n++; $person_felder_editierbar[]='mobil';
	$n++; $person_felder_editierbar[]='leiter_nachname';
	$n++; $person_felder_editierbar[]='leiter_nachname';
	$n++; $person_felder_editierbar[]='leiter_nachname';

	$tag2route = array();
	$tag2route[0] = "Route_1";
	$tag2route[1] = "Route_2";
	$tag2route[2] = "Route_3";

	$tag2start = array();
	$tag2start[0] = "Ziel_1";
	$tag2start[1] = "Ziel_2";
	$tag2start[2] = "Ziel_3";

	$tag2ziel = array();
	$tag2ziel[0] = "Ziel_2";
	$tag2ziel[1] = "Ziel_3";
	$tag2ziel[2] = "Ziel_4";


function level_check($element) {
	//Prüfen ob Nutzer die richtigen Rechte hat
	$musthave['admin_menue']['admin_level'] = 50;
	$musthave['listaction']['admin_level'] = 80;
	$musthave['addaction']['admin_level'] = 90;
	$musthave['adminperson']['admin_level'] = 95;
	$musthave['adminadmin']['admin_level'] = 99;
	$musthave['darf_buchen']['darf_buchen'] = 1;
	$musthave['person_id']['person_id'] = 1;
	$musthave['hashlinkzeigen']['admin_level']	= 95;
	
	if ($musthave[$element]['person_id'] > 0 and isset($_SESSION['person_id'])) {
		$return = true;
	}
	
	switch ($element) {
		case ('darf_buchen'):
			if (isset($_SESSION['person_id']) and $_SESSION['person2']['darf_buchen']) $return = true; else $return = false;
			break;
		case ('admin_menue'):
		case ('listaction'):
		case ('addaction'):
		case ('adminperson'):
		case ('adminadmin'):
			if (isset($_SESSION['person_id']) and 
				(isset($_SESSION['admin_level']) and $_SESSION['admin_level'] >= $musthave[$element]['admin_level'])) 
			{
				$return = true; 
			} else {
				$return = false;
			}
			break;
	}

	return ($return);
}


function echoif($var,$s) {
	settype($var,'string');
	if (!empty($var)) {
		$s = str_replace('###',$var,$s);
	} else $s = "";
	return $s;
}



function preventinjection($s) {
	$sori = $s;
	$s = str_replace(';', ',', $s);
	$s = htmlspecialchars($s); // decodieren mit: htmlspecialchars_decode($s)
	$s = str_replace('<', '[', $s);
	$s = str_replace('>', ']', $s);
	$s = str_replace('"', '', $s);
	$s = str_replace('=', '-', $s);
	$s = str_replace('\'', '-', $s);
	$s = str_ireplace('script ', 'scr', $s);
	$s = str_ireplace('select ', 'sel', $s);
	$s = str_ireplace('insert ', 'ins', $s);
	$s = str_ireplace('update ', 'upd', $s);
	$s = str_ireplace('delete ', 'del', $s);
	$s = str_ireplace('script ', 'scr', $s);
	$s = str_ireplace('include ', 'inc', $s);
	if ($sori != $s) {
		$fperr = fopen(preventinjection.log,"a");
		fputs($fperr,date("Y.m.d H:i:s").' - eingabe: '.$sori."\n                       ausgabe: ".$s."\n");
		fclose($fperr);
	}
	return $s;
}

//Erst mal alle Hacker verprellen
foreach($_REQUEST as $wert=>$R) {
	$Rwert  = "REQ_".$wert;
	$REQ[$wert] = preventinjection($R);
	//debugecho (__LINE__,": $wert = ".$REQ[$wert]);
}

$padd = '.unlimited.2017$$';
$salt = "$irgendwasblödes#$";


function decho($file="",$line="",$str) {
	debugecho($line,$str,$file);
}
function debugecho($line="",$str,$file="") {
	$f = basename($file);
	echo "<br/>\n$f-$line: ".$str;
}


function format_datum($datum,$form = '.') {
	if (strpos($datum,'.')>0) {
		list($t,$m,$j) = explode(".",$datum);
	} elseif (strpos($datum,'-')>0) {
		list($j,$m,$t) = explode("-",$datum);
	}
	if ($j <100) {
		$jahr = " ".date("Y");
		$grenze = substr($jahr,-2)*1;
		if ($j <= $grenze) {
			$jh ="20";
		} else {
			$jh ="19";
		}
		$j = $jh.$j;
	}
	if ($form=='.') {
		return substr("00".$t,-2).'.'.substr("00".$m,-2).'.'.$j;
	} else {
		return $j.'-'.substr("00".$m,-2).'-'.substr("00".$t,-2);
	}
}

function format_zeit($zeit) {
	$zeit = substr('0'.$zeit,-4);
	$zeit = substr($zeit,0,2).":".substr($zeit,2,2);
	return $zeit;
}

function signmustfeld($feld) {
	//decho(__FILE__,__LINE__,$feld['must']);
	if (isset($feld['must']) and $feld['must']==1 and isset($feld['mamust']) and $feld['mamust']==1) {
		$s = '<sup>+*</sup>';
	} elseif (isset($feld['must']) and $feld['must']==1) {
		$s = '<sup>+</sup>';
	} elseif (isset($feld['mamust']) and $feld['mamust']==1) {
		$s = '<sup>&nbsp;*</sup>';
	} else {
		$s = '';
	}
	return $s;
}

/*/ Aktion speichern
function aktion_schreiben($sql) {
	$sql = substr($sql.$s,0,-2).$where.";";
	//
	debugecho(__LINE__,$sql);
	mysql_query($sql);
	if (mysql_error()) echo mysql_errno().": ".mysql_error().$b.$sql.$b;
	return;
}*/

/*/ DB öffnen
$db_link = db_connect();


//Stämme-Array einlesen
$sql = "SELECT * FROM ul2017_staemme ";
$res = mysql_query($sql);
while ($stamm = mysql_fetch_assoc($res)) {
	$staemme[] = $stamm;
}
//var_dump($staemme); exit;
*/
