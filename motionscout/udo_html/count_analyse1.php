<HTML>
    <head>
        <!--meta http-equiv="refresh" content="30"-->    
    </head>
    <body>

<?php

	include("debugvar.php");
	include("database.inc.php");	
	include("common.inc.php");

    $_TEST = 1;

	$s = "?";
	foreach($_REQUEST as $feld => $wert) {
		$s .= "$feld=$wert, ";
	}
    decho(__FILE__,__LINE__," Test ");
	
	dbi_connect();

	$sql = 	"SELECT * FROM ".$tabs['motion']." WHERE 'time' > '2021-09-18' ORDER BY time DESC LIMIT 4000;";
    //$sql = 	"SELECT * FROM ".$tabs['motion']." ORDER BY time DESC ;";
		$rows = dbi_query($pdo,$sql);
	echo $sql.$b;

	echo "Zeilen: ".dbi_num_rows().$b;
    
    $event_alt = 0;
    $event = array();
    foreach($rows as $row) {
        $tt = substr($row['time'],0,13);
        if (isset($event[$tt])) {
            $event[$tt] += 1 ;
        } else {
            $event[$tt] =1;
        }
        if ($alt_event <> $tt) {
            //echo $row['id']." ".$row['time']." ".$row['user']." ".$row['wert1']." ".$row['wert2'].$b;        
            echo $alt_event.":00; ".$event[$alt_event].$b;        
            $alt_event = $tt;
        }
    }
    
    echo "mktime = ".date("H").",0,0,".date("m").",".date("d").",".date("Y").$b;
    $zeitpkt = mktime(date("H"),0,0,date("m"),date("d"),date("Y"));
    
    //echo debugvar($event);
    for ($n=1; $n<100; $n++) {
        echo $zeitpkt." - ".date("Y-m-d H",$zeitpkt).": ";
        
        if (in_array(date("Y.m.d H",$zeitpkt),$event)) {
            echo $alt_event.":00; ".$event[$alt_event].$event[date("Y.m.d H",$zeitpkt)];        
        }
        echo $b;
        $zeitpkt -= 3600;
    }
