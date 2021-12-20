<?php

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
        //echo $sql." - Anz: $anz";

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
    
?>
