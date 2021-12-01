<?php
	$b = "<br/>\n";
	include_once("debugvar.php");
	include_once("credentials.inc.php");

	$tabs['motion']		= "motions";

    if (0) {
        if (strpos($_SERVER['REQUEST_URI'],"_test") > 0 or strpos($_SERVER['REQUEST_URI'],"_int2") > 0) {
            $tab_person         = "sc2021_test_person";
            $tab_person_admin   = "sc2021_test_person_admin";
            $tab_aktion			= "sc2021_test_aktion";
            $tab_aktion_slots	= "sc2021_test_aktion_slots";
            $tab_aktion_booking	= "sc2021_test_aktion_booking";
            // $tab_staemme			= "sc2021_test_staemme";
            //echo __FILE__.__LINE__."Testdaten: ".$tab_person ;
        }
    }

	$pdo = dbi_connect();
	
	$sqli =sqli_connect();

	if (0) {
		echo "$db_server - $db_database - $db_user - $db_pass  => Tables: ";
        foreach($tabs as $tab) echo "$tab - ";
		echo debugvar($pdo,'$pdo');
		echo debugvar($sqli,'$sqli');
		exit;
	}

    function sqli_connect() {
		GLOBAL $sqli, $db_server, $db_database, $db_user, $db_passw, $tab_person;
		//echo " $db_server, $db_database, $db_user, $db_passw, $tab_person "; exit;
		$sqli = new mysqli($db_server, $db_user, $db_passw, $db_database, 3306);
		if ($sqli->connect_errno) {
			echo "Failed to connect to MySQL: (" . $sqli->connect_errno . ") " . $sqli->connect_error;
		}
		sqli_query("SET NAMES 'utf8';");
		//echo "MySQLi-Host-Info: ".$sqli->host_info . "\n"; exit;
		return $sqli;
	}
	
	function sqli_query($sql) {
		GLOBAL $sqli;
		
		//echo $sql."<br>\n";
		$statement = $sqli->prepare($sql);
		//$statement->bind_param();
		//echo "<br>".$sql.debugvar($res);
		if(!$statement->execute()) {
			echo "<br>Query fehlgeschlagen: ".$statement->error;
		}
		
		if (substr($sql,0,6) == "INSERT") { 
			$res = $statement->insert_id;
		} else {
			$res = $statement->get_result();
		}
		return $res;
	}
	
	function sqli_assoc($sql) {
		GLOBAL $sqli;
		
		$rows = array();
		$res = sqli($sql);
	

		$anzahl = $res->affected_rows;
		if ($anzahl > 1 ) {
			while ($row = $res->fetch_assoc()) {
				$rows[] = $row;
			}
		} else {
			$rows = $res->fetch_assoc();
		}
		return $rows;
	}
	
	function dbi_connect() {
		GLOBAL $b, $db_server, $db_database, $db_user, $db_passw, $tab_person;
		//echo $b.__FILE__." ".__LINE__." $db_server, $db_database, $db_user, $db_passw, $tab_person "; 
		//exit;

		$pdo = new PDO("mysql:host=$db_server;dbname=$db_database;charset=utf8", $db_user, $db_passw);
		//echo $b.__FILE__." ".__LINE__." $db_server, $db_database, $db_user, $db_passw, $tab_person "; 
		return $pdo;	
	}


	function dbi_query($pdo, $sql) {
		GLOBAL $pdo;
		
		$rows = array();
		$statement = $pdo->prepare($sql);
		
		try {
			$pdo->beginTransaction(); 
			$statement->execute();
			$pdo->commit();
		} 
		catch(PDOException $e) {
			echo $sql."<br>".$e->getMessage();
			exit;
		}
		$anz = dbi_num_rows();
		//echo "<br>Anzahl: ".$anz;

		if (substr($sql,0,6) == "INSERT") {
			$nid = $pdo->lastInsertId();
			//echo __LINE__.": anz $anz - nid: ".$nid; 
			return $nid;
		} else {
			while($row = $statement->fetch()) {
				//echo debugvar($row,'dbi_query-$row');
				 if ($anz == 1) {
				   $rows[0] = $row;
			   } else {
				   $rows[] = $row;
			   }
			}
		}
		//echo debugvar($rows,'dbi_query-$rows');
		return $rows;
	}

	function dbi_num_rows($line="") {
		GLOBAL $pdo;
		$rs1 = $pdo->query('SELECT FOUND_ROWS()');
		$rowCount = (int) $rs1->fetchColumn();
		//echo "<br>Line: $line - rsl: $rsl - rowCount: $rowCount";
		return $rowCount;
	}

	function dbi_query_teams() {
		GLOBAL $pdo;
		
		//Gültige Teams
		$sql = "SELECT rid1 FROM `record_links`  where aname='Teamleiter_Auswahl' ORDER BY `record_links`.`rid1` ASC";
		$teams_active = dbi_query($pdo,$sql);
		$tas = "(";
		foreach ($teams_active as $ta) $tas .= "'".$ta['rid1']."',";
		$tas = substr($tas,0,-1).")";
		
		$sql = "SELECT *
				FROM saved_records as sr, 
					 saved_data as sd, 
					 layer_attribute_links as al
				WHERE sr.lid = 1 and 
					  sr.rid = sd.rid and 
					  sd.aname = al.aname and 
					  sr.rid IN $tas 
				ORDER BY sr.rid, al.sorting";
		$rows = dbi_query($pdo,$sql);
		//echo $sql.debugvar($rows,'dbi_query_teams: $rows');

		$oldrid = "";
		$teams = array();
		foreach($rows as $row) {
				if ($row['rid'] == $oldrid) {
					$teams['rid']['oid'] = $row['oid'];
					$oldrid = $row['rid'];
				}
				$teams[$row['rid']][$row['aname']] = $row['data'];
		}
		//echo debugvar($teams);
		return $teams;

	}

	function dbi_query_personen($where="", $utf8=0, $debug=0) {
		GLOBAL $pdo;
		$sql = "SELECT *
				FROM saved_records as sr, saved_data as sd, layer_attribute_links as al
				WHERE sr.lid=2 and sr.rid=sd.rid and sd.aname=al.aname $where
				ORDER BY sr.rid, al.sorting";
		$rows = dbi_query($pdo,$sql);
		if ($debug) {
			echo "<p>where: ".$where."<br>sql: ".$sql;
			echo debugvar($rows,'dbi_query_personen: $rows'); exit;
		}
		
		$oldrid = "";
		$personen = array();
		foreach($rows as $row) {
				if ($row['rid'] == $oldrid) {
					$personen['rid']['oid'] = $row['oid'];
					$oldrid = $row['rid'];
				}
				$personen[$row['rid']][$row['aname']] = $row['data'];
		}
		//echo debugvar($teams);
		return $personen;

	}
	
	function dbi_query_personen_altersstufen() {
		GLOBAL $pdo;
		$sql = "SELECT distinct(data) FROM saved_data WHERE aname='Altersstufe' ORDER BY data";
		$rows = dbi_query($pdo,$sql);
		return $rows;	
	}
	
	function dbi_query_personen_funktion() {
		GLOBAL $pdo;
		$sql = "SELECT distinct(data) FROM saved_data WHERE aname='Funktion_Bundeshajk' ORDER BY data";
		$rows = dbi_query($pdo,$sql);
		return $rows;				
	}

	
