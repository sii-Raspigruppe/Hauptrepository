<HTML>
    <head>
        <!--meta http-equiv="refresh" content="30"-->    
    </head>
    <body>

<?php

	include("debugvar.php");
	include("include.database.php");	
	include("include.common.php");

    $_TEST = 1;

	$s = "?";
	foreach($_REQUEST as $feld => $wert) {
		$REQ[$feld] = $wert;
	}

    // Anzahl der Events pro Zeiteinheit
    function count_events($device, $tstarttime, $tduration) {
        GLOBAL $pdo, $tabs;
        
        $where  = " and wert1 LIKE '$device' ";
        $where .= " and time >= '".date("Y-m-d H:i:s", $tstarttime)."'";
        $where .= " and time <= '".date("Y-m-d H:i:s", ($tstarttime+$tduration))."'";
        $sql = 	"SELECT count(id) FROM ".$tabs['motion']." WHERE 1 ".$where.";";
        //$sql = 	"SELECT * FROM ".$tabs['motion']." ORDER BY time DESC ;";
        //echo $device." ".$sql.$b;
        $rows = dbi_query($pdo,$sql);
        //echo debugvar($rows);

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

    if (isset($REQ['anzTage'])) $anzTage = $REQ['anzTage']; else  $anzTage = '20';

    $aTage = array('20','30','60','90','120','150','180');
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
        $start = mktime(23,59,0,$deviceStart['mon'],$deviceStart['day'],$deviceStart['year']);
    } else {
        $start = mktime(23,59,0,date("m"),date("d"),date("Y"));
    }
    $duration = 3600;
    $ende = $start - $anzTage*24*3600;
    
    //decho(__FILE__,__LINE__," Test ");
    $jahr = $altjahr = 0;
    $mon  = $altmon  = 0;
    $tag  = $alttag  = 0;
    $std  = $altstd  = 0;
    echo "<table><tr>";
    for ($tim = $start; $tim > $ende; $tim -= $duration) {
        if ($tim == $start) {
            echo "<td>Uhrzeit:</td>";
            for ($n =24; $n > 0; $n--) {
                echo "<td width=25px align=center>$n</td>";
            }
        }
    	$anz = count_events($device, $tim, $duration );
        $jahr = date("Y",$tim);
        $mon  = date("m",$tim);
        $tag  = date("d",$tim);
        $std  = date("H",$tim);
        //decho(__FILE__,__LINE__,date("Y-m-d H:i:s ", $tim)." Anzahl: ".$anz);
        if (($jahr*10000 + $mon*100 + $tag) <> ($altjahr*10000+$altmon*100+$alttag)) {
            echo "</tr><tr><td>$tag.$mon.$jahr</td>";
            $altjahr = $jahr;
            $altmon  = $mon;
            $alttag  = $tag;
        }
        if ($std <> $altstd) {
            if ($anz == 0) $anz = "-";
            if ($tim > time()) $anz = "";
            if ($anz > 5 ) $color = '#00ffdd'; else $color = '';
            echo "<td width=25px align=center style='background-color:$color'>$anz</td>";
            $altstd = $std;
        }
    }
    echo "</tr></table>";
    exit;
