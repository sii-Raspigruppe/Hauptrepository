<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8"/>
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
		$REQ[$feld] = $wert;
	}

    // Anzahl der Events pro Zeiteinheit
    function count_events($device, $tstarttime, $tduration) {
        GLOBAL $pdo, $tabs;
        
        $where  = "  and wert1 LIKE '$device' ";
        $where .= "  and time <  '".date("Y-m-d H:i:s", $tstarttime)."'";
        $where .= "  and time >= '".date("Y-m-d H:i:s", ($tstarttime-$tduration))."'";
        $sql = 	"SELECT count(id) FROM ".$tabs['motion']." WHERE 1 ".$where.";";
        
        //echo $device." ".$sql.$b;
        $rows = dbi_query($pdo,$sql);
        //echo debugvar($rows);

        $anz = $rows[0][0];
        //print "\n<br>SELECT count(id) FROM ".$tabs['motion']." WHERE 1 $where ORDER BY time DESC; - Anz: $anz";

        return $rows[0][0];
    
    }

    // Sensoren aus der DB suchen
    function device_search() {
        GLOBAL $pdo, $tabs;

        $sql = 	"SELECT distinct(wert1) FROM ".$tabs['motion']." ORDER BY wert1;";
        //echo $sql.$b;
        $rows = dbi_query($pdo,$sql);
        //echo debugvar($rows);

        return $rows;
    }
    
    // Sensoren aus der DB suchen
    function device_last($device) {
        GLOBAL $pdo, $tabs;

        $sql = 	"SELECT time FROM ".$tabs['motion']." WHERE wert1 LIKE '$device' ORDER BY time DESC;";
        //echo $sql.$b;
        $rows = dbi_query($pdo,$sql);
        //echo debugvar($rows);

        return array('year' => substr($rows[0][time],0,4), 'mon' => substr($rows[0][time],5,2), 'day' => substr($rows[0][time],8,2));
    }
    
    echo "<h1>Bewegungsampel - ".date("d.m.Y H:i")."</h1>";
    
    /*
     * Bewegungsmelder suchen und auswählen
     */
    $devices = device_search();
    //echo debugvar($devices);

    if (isset($REQ['devices'])) $device = $REQ['devices']; else  $device = '%';

    echo "<form name='device-select' id='device-select' action='#'>";
    echo "<label for='device-select'>Bewegungsmelder wählen:</label>";

    echo "<select name='devices' id='device-select' onclick=submit()>\n";
    echo "    <option value='%'>-- Alle Geräte anzeigen --</option>\n";
    foreach ($devices as $dev) {
        if ($device == $dev['wert1']) $select = ' selected '; else $select ='';
        echo "<option value='".$dev['wert1']."' $select>".$dev['wert1']."</option>\n";
    }
    echo "</select> &nbsp;- &nbsp;";

    if (isset($REQ['anzTage'])) $anzTage = $REQ['anzTage']; else  $anzTage = '3';

    $aTage = array('1','3','7','14','21','35','70','105','140','175','210');
    echo "<label for='Anzahl-Tage'>Anzahl wählen:</label>";
    echo "<select name='anzTage' id='AnzahlTage' onclick=submit()>\n";
    foreach ($aTage as $anz) {
        if ($anz == $anzTage) $select = ' selected '; else $select ='';
        echo "<option value='".$anz."' $select>".$anz."</option>\n";
    }
    echo "</select>";
    echo "</form>";

    //echo debugvar($REQ,'$REQ');
    
    /*
     * Bewegungen filtern und anzeigen
     */
    
    if (isset($REQ['devices'])) {
        $deviceStart = device_last($device);
        $start = mktime(23,00,0,$deviceStart['mon'],$deviceStart['day'],$deviceStart['year']);
    } else {
        $start = mktime(23,00,0,date("m"),date("d"),date("Y"));
    }
    $duration = 4*3600; // 4 Stunden
    $ende = $start - $anzTage*24*3600;
    
    //decho(__FILE__,__LINE__," Test ");
    $jahr = $altjahr = 0;
    $mon  = $altmon  = 0;
    $tag  = $alttag  = 0;
    $std  = $altstd  = 0;
    $status = array();
    $stat = 0;
    
    echo "<p>\n</p><table><tr>";
    for ($tim = $start; $tim > $ende; $tim -= $duration) {
        if ($tim == $start) {
            echo "<td>Uhrzeit:</td>";
            $nanz = floor(24*3600/$duration);
            $ntim = $start;
            for ($n = 0; $n < $nanz ; $n++) {
                $nvon = date("H", $ntim);
                $nbis = date("H", ($ntim-$duration));
                echo "<td width=55px align=center>$nvon-$nbis</td>";
                $ntim -= $duration;
            }
        }
    	$anz = count_events( $device, $tim, $duration );
        //echo "\n start: ".date("Y.m.d H:m",$start)." - time: ".date("Y.m.d H:m",$tim)." - anz: ".$anz."  ";
        $jahr = date("Y",$tim);
        $mon  = date("m",$tim);
        $tag  = date("d",$tim);
        $std  = date("H",$tim);
        //decho(__FILE__,__LINE__,date("Y-m-d H:i:s ", $tim)." Anzahl: ".$anz);
        if (($jahr*10000 + $mon*100 + $tag) <> ($altjahr*10000+$altmon*100+$alttag)) {
            echo "</tr><tr>\n<td>$tag.$mon.$jahr</td>";
            $altjahr = $jahr;
            $altmon  = $mon;
            $alttag  = $tag;
            $stat++;
        }
        if ($std <> $altstd) {
            /*
             * echo "\nif (time() > $tim and $anz == 0 and $std >= 7 and $std <= 19 ) $color = '#ffdddd'; else $color = ''\n";
            echo (time() > $tim);
            echo " $tim\n";
            echo $anz == 0;
            echo " anz: $anz \n";
            echo $std >= 7;
            echo " std: $std \n";
            echo $std <= 19;
            echo " std: $std \n";
            */
            
            $cred   = '#ffdddd';
            $cgreen = '#00ffdd';
            $cgrey  = '#eeeeee';
            
            if ($anz == 0) $anz = "-";
            if (($tim-$duration) > time()) $anz = "";
            if ($anz > 5 ) {
                $color = $cgreen; 
            } elseif (time() > $tim and $anz == 0 and $std > 7 and $std <= 19 ) {
                $color = $cred ; 
            } else $color = '';
            $status[$stat][$std] = $anz;
            echo "\n<td width=25px align=center style='background-color:$color'>$anz</td>";
            $altstd = $std;
        }
    }
    
    echo "</tr></table>";
    //echo debugvar($status);
    
    
    echo "\n<table border=1><tr>";
    $nanz = floor(24*3600/$duration);
    $ntim = $start;
    for ($n = $nanz; $n > 0 ; $n--) {
        $nvon = date("H", ($start- $n*$duration));
        $nbis = date("H", ($start-($n-1)*$duration));
        if (!isset($status[1][$nbis])) {
            $color = $cgrey;
        } elseif ($status[1][$nbis] > 0 ) {
            $color = $cgreen;
        } elseif ($status[1][$nbis] == '-' ) {
            $color = $cred;
        }
        
        echo "\n<td width=200px height=200px align=center bgcolor=$color >$nvon-$nbis<br>$nbis ".$status[1][$nbis]."</td>";
        $ntim -= $duration;
    }
    echo "\n</tr></table>";
    
    echo "<style>";
    echo "   #t03 { background-color:#ffbbbb;}";
    echo "</style>";
    
    exit;
