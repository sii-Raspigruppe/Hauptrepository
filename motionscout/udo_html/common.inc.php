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

	if (0 and isset($REQ['test']) or strpos($_SERVER['REQUEST_URI'],"_test") > 0 or strpos($_SERVER['REQUEST_URI'],"_int") > 0) {
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

