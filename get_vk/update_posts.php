<?php

function curl($url){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
 }

set_time_limit(0);

header('Content-Type: text/html; charset=utf-8');

$dblocation = "mysql92.1gb.ru";
$dbname = "gb_cafe57db";
$dbuser = "gb_cafe57db";
$dbpasswd = "c7d445d8psg";
$dbcnx = @mysql_connect($dblocation,$dbuser,$dbpasswd) or die("Could not connect: ".mysql_error());
@mysql_select_db($dbname, $dbcnx) or die("Could not connect: ".mysql_error());

$strSQL = "SET NAMES utf8;";
mysql_query($strSQL);

$strSQL = "DELETE FROM mpgu_edu_posts;";
mysql_query($strSQL);

//Обновляем посты mpgu_edu

$packet = 0;
$limit = 100;

do {
	$offset = $packet * $limit;
	$contents = curl('http://api.vk.com/method/wall.get?owner_id=-30321356&count=100&offset=' . $offset);
	$posts = json_decode($contents, true);
	$count_posts = $posts['response'][0];
	array_shift($posts['response']);

	foreach ($posts['response'] as $post) {
		
		$post['copy_text'] = str_replace('"', "'", $post['copy_text']);
		$post['text'] = str_replace('"', "'", $post['text']);
		$post['copy_text'] = str_replace('\\', '/', $post['copy_text']);
		$post['text'] = str_replace("\\", '/', $post['text']);

		$strSQL = "INSERT INTO mpgu_edu_posts(date,id,text,count_likes,count_comments, pics) VALUES(";
		$strSQL .= '"' . date('Y-m-d H:i', $post['date'] - 3600) . '", ';
		$strSQL .= '"wall-30321356_' . $post['id'] . '", ';
		$strSQL .= '"' . $post['copy_text'] . ' ';		
		$strSQL .= $post['text'] . '", ';
		$strSQL .= $post['likes']['count'] . ', ';
		$strSQL .= $post['comments']['count'] . ', "';
		
		$pics = '';
		if (count($post['attachments']) != 0) {
			foreach ($post['attachments'] as $pic) {
				if ($pic['type'] == 'photo') {
					$pics .= $pic['photo']['src_big'] . ', ';
				}
			}
			$pics = substr($pics,0,-2);
		}
		$strSQL .= $pics . '")';
		mysql_query($strSQL) or die("Failed: ".mysql_error());
	}
	$packet++;
	usleep(200000);
} while ($count_posts > $offset + $limit);


//Ћбновляем посты tsss_mpgu

$strSQL = "DELETE FROM tsss_mpgu_posts;";
mysql_query($strSQL);

$packet = 0;
$limit = 100;

do {
	$offset = $packet * $limit;
	$contents = curl('http://api.vk.com/method/wall.get?owner_id=-58507951&count=100&offset=' . $offset);
	$posts = json_decode($contents, true);
	$count_posts = $posts['response'][0];
	array_shift($posts['response']);

	foreach ($posts['response'] as $post) {
		
		$post['copy_text'] = str_replace('"', "'", $post['copy_text']);
		$post['text'] = str_replace('"', "'", $post['text']);
		$post['copy_text'] = str_replace('\\', '/', $post['copy_text']);
		$post['text'] = str_replace("\\", '/', $post['text']);

		$strSQL = "INSERT INTO tsss_mpgu_posts(date,id,text,count_likes,count_comments, pics) VALUES(";
		$strSQL .= '"' . date('Y-m-d H:i', $post['date'] - 3600) . '", ';
		$strSQL .= '"wall-58507951_' . $post['id'] . '", ';
		$strSQL .= '"' . $post['copy_text'] . ' ';		
		$strSQL .= $post['text'] . '", ';
		$strSQL .= $post['likes']['count'] . ', ';
		$strSQL .= $post['comments']['count'] . ', "';
		
		$pics = '';
		if (count($post['attachments']) != 0) {
			foreach ($post['attachments'] as $pic) {
				if ($pic['type'] == 'photo') {
					$pics .= $pic['photo']['src_big'] . ', ';
				}
			}
		}
		$pics = substr($pics,0,-2);
		$strSQL .= $pics . '")';
		
		mysql_query($strSQL) or die("Failed: ".mysql_error());
	}
	$packet++;
	usleep(200000);
} while ($count_posts > $offset + $limit);


//Ћбновляем посты typical_mpgu

$strSQL = "DELETE FROM typical_mpgu_posts;";
mysql_query($strSQL);

$packet = 0;
$limit = 100;

do {
	$offset = $packet * $limit;
	$contents = curl('http://api.vk.com/method/wall.get?owner_id=-30973505&count=100&offset=' . $offset);
	$posts = json_decode($contents, true);
	$count_posts = $posts['response'][0];
	array_shift($posts['response']);

	foreach ($posts['response'] as $post) {
		
		$post['copy_text'] = str_replace('"', "'", $post['copy_text']);
		$post['text'] = str_replace('"', "'", $post['text']);
		$post['copy_text'] = str_replace('\\', '/', $post['copy_text']);
		$post['text'] = str_replace("\\", '/', $post['text']);

		$strSQL = "INSERT INTO typical_mpgu_posts(date,id,text,count_likes,count_comments, pics) VALUES(";
		$strSQL .= '"' . date('Y-m-d H:i', $post['date'] - 3600) . '", ';
		$strSQL .= '"wall-30973505_' . $post['id'] . '", ';
		$strSQL .= '"' . $post['copy_text'] . ' ';		
		$strSQL .= $post['text'] . '", ';
		$strSQL .= $post['likes']['count'] . ', ';
		$strSQL .= $post['comments']['count'] . ', "';
		
		$pics = '';
		if (count($post['attachments']) != 0) {
			foreach ($post['attachments'] as $pic) {
				if ($pic['type'] == 'photo') {
					$pics .= $pic['photo']['src_big'] . ', ';
				}
			}
		}
		$pics = substr($pics,0,-2);
		$strSQL .= $pics . '")';
		
		mysql_query($strSQL) or die("Failed: ".mysql_error());
	}
	$packet++;
	usleep(200000);
} while ($count_posts > $offset + $limit);

echo '<p>Completed</p><br>';
echo '<a href="index.html">Back</a><br>';

?>