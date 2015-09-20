<?php

header('content-type:text/html;charset=utf-8');

set_time_limit(0);

$dblocation = "mysql92.1gb.ru";
$dbname = "gb_cafe57db";
$dbuser = "gb_cafe57db";
$dbpasswd = "c7d445d8psg";
$dbcnx = @mysql_connect($dblocation,$dbuser,$dbpasswd) or die("Could not connect: ".mysql_error());
@mysql_select_db($dbname, $dbcnx) or die("Could not connect: ".mysql_error());

$strSQL = "SET NAMES utf8;";
mysql_query($strSQL);

$list = array('mpgu_edu_users', 'tsss_mpgu_users', 'typical_mpgu_users');
$users = array();

foreach ($list as $table) {
	$strSQL = "SELECT * from " . $table;
	$ath = mysql_query($strSQL);
	if($ath) {
		while($user = mysql_fetch_array($ath)) {
			$curr_user = array();
			$curr_user['id'] = $user['id'];
			$curr_user['domain'] = $user['domain'];
			$curr_user['fname'] = $user['fname'];
			$curr_user['lname'] = $user['lname'];
			$users[] = $curr_user;
		}
	}
}

$fp = fopen('lol.json', 'w');
fwrite($fp, json_encode($users, JSON_UNESCAPED_UNICODE));
?>
	