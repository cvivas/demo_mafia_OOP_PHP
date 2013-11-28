<?php
require_once ('mafia.php');
$action = (isset($_POST['actions'])) ? $_POST['actions'] : "";
$name = (isset($_POST['name'])) ? $_POST['name'] : "";
$age = (isset($_POST['age'])) ? $_POST['age'] : "";
$boss = (isset($_POST['boss'])) ? $_POST['boss'] : null;
//storage
$s = implode("", @file("store"));
$mafias = unserialize($s);
if ($action != "dump") {
	echo "BEFORE  <br />";
	$mafias -> dump_all();
}
switch ($action) {
	case 'insert' :
		if (isset($boss) && $boss !== "") {
			$mafias -> insert_new_gangster($name, $age, 0, "organization", $boss, null);
		} else {
			$mafias -> insert_new_gangster($name, $age, 0, "organization", null, null);
		}
		break;
	case 'kill' :
		$mafias -> kill_gangster($name);
		break;
	case 'jail' :
		$mafias -> jail_gangster($name);
		break;
	case 'release' :
		$mafias -> release_gangster($name);
		break;
	case 'dump' :
		//no need to do anything
		break;
	default :
		break;
}
//saving the changes through serialize
$s = serialize($mafias);
$fp = fopen("store", "w");
fwrite($fp, $s);
fclose($fp);

$state_message = ($action == "dump") ?  "<br />SHOWING CURRENT STATE <br />" :  "<br />AFTER <br />" ;
echo $state_message;
$mafias -> dump_all();
?>