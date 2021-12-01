<?php
	include("debugvar.php");
	include("database.inc.php");	
	include("common.inc.php");

	$s = "?";
	foreach($_REQUEST as $feld => $wert) {
		$s .= "$feld=$wert, ";
	}
	
	$fp = fopen("count_log.php","a");

	fputs($fp,'echo "'.date("Y.m.d H:i:s").' '.$s.' Meldung eingegangen <br>";'."\n");
	fclose($fp);

	echo date("Y.m.d H:i:s")." $s - <a href=count_log.php>Logdatei</a>";

	dbi_connect();

	$sql = 	"INSERT INTO ".$tabs['motion'].
			" SET user='".$REQ['user']."', wert1='".$REQ['wert1']."', wert2='".$REQ['wert2']."';";
		$res = dbi_query($pdo,$sql);
	echo $sql;

	echo dbi_num_rows();
?>
