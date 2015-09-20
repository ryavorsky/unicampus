<?php

set_time_limit(0);

header('Content-Type: text/html; charset=utf-8');

function curl($url){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
 }

$dblocation = "mysql92.1gb.ru";
$dbname = "gb_cafe57db";
$dbuser = "gb_cafe57db";
$dbpasswd = "c7d445d8psg";
$dbcnx = @mysql_connect($dblocation,$dbuser,$dbpasswd) or die("Could not connect: ".mysql_error());
@mysql_select_db($dbname, $dbcnx) or die("Could not connect: ".mysql_error());

// ќбновл€ем пользователей mpgu_edu
$packet = 0;
$limit = 1000;

$strSQL = "DELETE FROM mpgu_edu_users;";
mysql_query($strSQL);

$strSQL = "SET NAMES utf8;";
mysql_query($strSQL);

do {
    $offset = $packet * $limit;
    $contents = curl('http://api.vk.com/method/groups.getMembers?group_id=30321356&fields=domain,sex,bdate,city,photo_max_orig,activities,interests,music,movies,tv,books,games,about,quotes,connections,contacts,site,relation,personal&offset=' . $offset);
    $members = json_decode($contents, true);
	
	foreach ($members['response']['users'] as $user_array) {
		$strSQL = "INSERT INTO mpgu_edu_users(id,domain,fname,lname,sex,bdate,friends,city,photo_max_orig,activities,interests,music,movies,tv,books,games,about,quotes,skype,facebook,twitter,instagram,home_phone,site,relation,relation_partner_id,political_views,langs,religion,inspired_by,people_main,life_main,smoking,alcohol) VALUES(" . $user_array['uid'] . ", '" . $user_array['domain'] . "', '" . (string)$user_array['first_name'] . "', '" . (string)$user_array['last_name'] . "', '";
		if ($user_array['sex'] == 1) $strSQL .= "F', '";
		if ($user_array['sex'] == 2) $strSQL .= "M', '";
		$strSQL .= (string)$user_array['bdate'] . "', '";
		
		$friends = curl('http://api.vk.com/method/friends.get?user_id=' . $user_array['uid']);
		$friends = json_decode($friends, true);
		$friendlist = '';
		foreach ($friends['response'] as $friend) {
			$friendlist .= $friend . ', ';
		}
		$friendlist = substr($friendlist, 0, -2);
		$strSQL .= $friendlist . "', '";
		
		if ($user_array['city'] == 1) $strSQL .= "Москва', '";
		if ($user_array['city'] == 0) $strSQL .= "', '";
		if ($user_array['city'] != 1 && $user_array['city'] != 0) {
			$city = curl('http://api.vk.com/method/database.getCitiesById?city_ids=' . $user_array['city']);
			$city = json_decode($city, true);
			$strSQL .= $city['response'][0]['name'] . "', '";
		}
		$strSQL .= $user_array['photo_max_orig'] . "', '";
		$strSQL .= $user_array['activities'] . "', '";
		$strSQL .= $user_array['interests'] . "', '";
		$strSQL .= $user_array['music'] . "', '";
		$strSQL .= $user_array['movies'] . "', '";
		$strSQL .= $user_array['tv'] . "', '";
		$strSQL .= $user_array['books'] . "', '";
		$strSQL .= $user_array['games'] . "', '";
		$strSQL .= $user_array['about'] . "', '";
		$strSQL .= $user_array['quotes'] . "', '";
		$strSQL .= $user_array['skype'] . "', '";
		$strSQL .= $user_array['facebook'] . "', '";
		$strSQL .= $user_array['twitter'] . "', '";
		$strSQL .= $user_array['instagram'] . "', '";
		$strSQL .= $user_array['home_phone'] . "', '";
		$strSQL .= $user_array['site'] . "', '";
		
		$relations = array('не указано', 'не женат/не замужем', 'есть друг/есть подруга', 'помолвлен/помолвлена', 'женат/замужем', 'всё сложно' ,'в активном поиске', 'влюблён/влюблена');
		$strSQL .= $relations[$user_array['relation']] . "', '";
		$strSQL .= $user_array['relation_partner']['id'] . "', '";
		
		$politicals = array('коммунистические', 'социалистические', 'умеренные', 'либеральные', 'консервативные', 'монархические', 'ультраконсервативные', ' индифферентные', 'либертарианские');
		$strSQL .= $politicals[$user_array['personal']['political'] - 1] . "', '";
		$langs = '';
		foreach ($user_array['personal']['langs'] as $lang) {
			$langs .= $lang . ', ';
		}
		$langs = substr($langs, 0, -2);
		$strSQL .= $langs . "', '";
		$strSQL .= $user_array['personal']['religion'] . "', '";
		$strSQL .= $user_array['personal']['inspired_by'] . "', '";
		$pplmain = array('ум и креативность', 'доброта и честность', 'красота и здоровье', 'власть и богатство', 'смелость и упорство', 'юмор и жизнелюбие');
		$strSQL .= $pplmain[$user_array['personal']['people_main'] - 1] . "', '";
		$lifemain = array('семья и дети', 'карьера и деньги', 'развлечения и отдых', 'наука и исследования', 'совершенствование мира', 'саморазвитие', 'красота и искусство', 'слава и влияние');
		$strSQL .= $lifemain[$user_array['personal']['life_main'] - 1] . "', '";
		$smok_alc = array('резко негативное', 'негативное', 'нейтральное', 'компромиссное', 'положительное');
		$strSQL .= $smok_alc[$user_array['personal']['smoking'] - 1] . "', '";
		$strSQL .= $smok_alc[$user_array['personal']['alcohol'] - 1] . "')";
		mysql_query($strSQL);
	}
	$packet++;
} while ($members['response']['count'] > $offset + $limit);


// ќбновл€ем пользователей tsss_mpsu
$packet = 0;
$limit = 1000;

$strSQL = "DELETE FROM tsss_mpgu_users;";
mysql_query($strSQL);

$strSQL = "SET NAMES utf8;";
mysql_query($strSQL);

do {
    $offset = $packet * $limit;
    $contents = curl('http://api.vk.com/method/groups.getMembers?group_id=58507951&fields=domain,sex,bdate,city,photo_max_orig,activities,interests,music,movies,tv,books,games,about,quotes,connections,contacts,site,relation,personal&offset=' . $offset);
    $members = json_decode($contents, true);
	
	foreach ($members['response']['users'] as $user_array) {
		$strSQL = "INSERT INTO tsss_mpgu_users(id,domain,fname,lname,sex,bdate,friends,city,photo_max_orig,activities,interests,music,movies,tv,books,games,about,quotes,skype,facebook,twitter,instagram,home_phone,site,relation,relation_partner_id,political_views,langs,religion,inspired_by,people_main,life_main,smoking,alcohol) VALUES(" . $user_array['uid'] . ", '" . $user_array['domain'] . "', '" . (string)$user_array['first_name'] . "', '" . (string)$user_array['last_name'] . "', '";
		if ($user_array['sex'] == 1) $strSQL .= "F', '";
		if ($user_array['sex'] == 2) $strSQL .= "M', '";
		$strSQL .= (string)$user_array['bdate'] . "', '";
		
		$friends = curl('http://api.vk.com/method/friends.get?user_id=' . $user_array['uid']);
		$friends = json_decode($friends, true);
		$friendlist = '';
		foreach ($friends['response'] as $friend) {
			$friendlist .= $friend . ', ';
		}
		$friendlist = substr($friendlist, 0, -2);
		$strSQL .= $friendlist . "', '";
		
		if ($user_array['city'] == 1) $strSQL .= "Москва', '";
		if ($user_array['city'] == 0) $strSQL .= "', '";
		if ($user_array['city'] != 1 && $user_array['city'] != 0) {
			$city = curl('http://api.vk.com/method/database.getCitiesById?city_ids=' . $user_array['city']);
			$city = json_decode($city, true);
			$strSQL .= $city['response'][0]['name'] . "', '";
		}
		$strSQL .= $user_array['photo_max_orig'] . "', '";
		$strSQL .= $user_array['activities'] . "', '";
		$strSQL .= $user_array['interests'] . "', '";
		$strSQL .= $user_array['music'] . "', '";
		$strSQL .= $user_array['movies'] . "', '";
		$strSQL .= $user_array['tv'] . "', '";
		$strSQL .= $user_array['books'] . "', '";
		$strSQL .= $user_array['games'] . "', '";
		$strSQL .= $user_array['about'] . "', '";
		$strSQL .= $user_array['quotes'] . "', '";
		$strSQL .= $user_array['skype'] . "', '";
		$strSQL .= $user_array['facebook'] . "', '";
		$strSQL .= $user_array['twitter'] . "', '";
		$strSQL .= $user_array['instagram'] . "', '";
		$strSQL .= $user_array['home_phone'] . "', '";
		$strSQL .= $user_array['site'] . "', '";
		
		$strSQL .= $relations[$user_array['relation']] . "', '";
		$strSQL .= $user_array['relation_partner']['id'] . "', '";
		
		$strSQL .= $politicals[$user_array['personal']['political'] - 1] . "', '";
		$langs = '';
		foreach ($user_array['personal']['langs'] as $lang) {
			$langs .= $lang . ', ';
		}
		$langs = substr($langs, 0, -2);
		$strSQL .= $langs . "', '";
		$strSQL .= $user_array['personal']['religion'] . "', '";
		$strSQL .= $user_array['personal']['inspired_by'] . "', '";
		$strSQL .= $pplmain[$user_array['personal']['people_main'] - 1] . "', '";
		$strSQL .= $lifemain[$user_array['personal']['life_main'] - 1] . "', '";
		$strSQL .= $smok_alc[$user_array['personal']['smoking'] - 1] . "', '";
		$strSQL .= $smok_alc[$user_array['personal']['alcohol'] - 1] . "')";
		mysql_query($strSQL);
	}
	$packet++;
} while ($members['response']['count'] > $offset + $limit);



// ќбновл€ем пользователей typical_mpgu
$packet = 0;
$limit = 1000;

$strSQL = "DELETE FROM typical_mpgu_users;";
mysql_query($strSQL);

$strSQL = "SET NAMES utf8;";
mysql_query($strSQL);

do {
    $offset = $packet * $limit;
    $contents = curl('http://api.vk.com/method/groups.getMembers?group_id=30973505&fields=domain,sex,bdate,city,photo_max_orig,activities,interests,music,movies,tv,books,games,about,quotes,connections,contacts,site,relation,personal&offset=' . $offset);
    $members = json_decode($contents, true);
	
	foreach ($members['response']['users'] as $user_array) {
		$strSQL = "INSERT INTO typical_mpgu_users(id,domain,fname,lname,sex,bdate,friends,city,photo_max_orig,activities,interests,music,movies,tv,books,games,about,quotes,skype,facebook,twitter,instagram,home_phone,site,relation,relation_partner_id,political_views,langs,religion,inspired_by,people_main,life_main,smoking,alcohol) VALUES(" . $user_array['uid'] . ", '" . $user_array['domain'] . "', '" . (string)$user_array['first_name'] . "', '" . (string)$user_array['last_name'] . "', '";
		if ($user_array['sex'] == 1) $strSQL .= "F', '";
		if ($user_array['sex'] == 2) $strSQL .= "M', '";
		$strSQL .= (string)$user_array['bdate'] . "', '";
		
		$friends = curl('http://api.vk.com/method/friends.get?user_id=' . $user_array['uid']);
		$friends = json_decode($friends, true);
		$friendlist = '';
		foreach ($friends['response'] as $friend) {
			$friendlist .= $friend . ', ';
		}
		$friendlist = substr($friendlist, 0, -2);
		$strSQL .= $friendlist . "', '";
		
		if ($user_array['city'] == 1) $strSQL .= "Москва', '";
		if ($user_array['city'] == 0) $strSQL .= "', '";
		if ($user_array['city'] != 1 && $user_array['city'] != 0) {
			$city = curl('http://api.vk.com/method/database.getCitiesById?city_ids=' . $user_array['city']);
			$city = json_decode($city, true);
			$strSQL .= $city['response'][0]['name'] . "', '";
		}
		$strSQL .= $user_array['photo_max_orig'] . "', '";
		$strSQL .= $user_array['activities'] . "', '";
		$strSQL .= $user_array['interests'] . "', '";
		$strSQL .= $user_array['music'] . "', '";
		$strSQL .= $user_array['movies'] . "', '";
		$strSQL .= $user_array['tv'] . "', '";
		$strSQL .= $user_array['books'] . "', '";
		$strSQL .= $user_array['games'] . "', '";
		$strSQL .= $user_array['about'] . "', '";
		$strSQL .= $user_array['quotes'] . "', '";
		$strSQL .= $user_array['skype'] . "', '";
		$strSQL .= $user_array['facebook'] . "', '";
		$strSQL .= $user_array['twitter'] . "', '";
		$strSQL .= $user_array['instagram'] . "', '";
		$strSQL .= $user_array['home_phone'] . "', '";
		$strSQL .= $user_array['site'] . "', '";
		
		$strSQL .= $relations[$user_array['relation']] . "', '";
		$strSQL .= $user_array['relation_partner']['id'] . "', '";
		
		$strSQL .= $politicals[$user_array['personal']['political'] - 1] . "', '";
		$langs = '';
		foreach ($user_array['personal']['langs'] as $lang) {
			$langs .= $lang . ', ';
		}
		$langs = substr($langs, 0, -2);
		$strSQL .= $langs . "', '";
		$strSQL .= $user_array['personal']['religion'] . "', '";
		$strSQL .= $user_array['personal']['inspired_by'] . "', '";
		$strSQL .= $pplmain[$user_array['personal']['people_main'] - 1] . "', '";
		$strSQL .= $lifemain[$user_array['personal']['life_main'] - 1] . "', '";
		$strSQL .= $smok_alc[$user_array['personal']['smoking'] - 1] . "', '";
		$strSQL .= $smok_alc[$user_array['personal']['alcohol'] - 1] . "')";
		mysql_query($strSQL);
	}
	$packet++;
} while ($members['response']['count'] > $offset + $limit);

echo '<p>Completed</p><br>';
echo '<a href="index.html">Back</a><br>';


?>