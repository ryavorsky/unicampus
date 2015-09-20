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

// Обновляем лайки mpgu_edu
$packet = 0;
$limit = 1000;

$strSQL = "SET NAMES utf8;";
mysql_query($strSQL);

$strSQL = "DELETE FROM mpgu_edu_likes;";
mysql_query($strSQL);

do {
    $offset = $packet * $limit;
    $contents = curl('http://api.vk.com/method/wall.get?owner_id=-30321356&count=100&offset=' . $offset);
    $posts = json_decode($contents, true);
	$count_posts = $posts['response'][0];
	array_shift($posts['response']);
	
	foreach ($posts['response'] as $post) {
		
		$like_list = curl('http://api.vk.com/method/likes.getList?type=post&owner_id=-30321356&item_id=' . (string)$post['id']);
		$likes = json_decode($like_list, true);
		foreach ($likes['response']['users'] as $like) {
			
			$strSQL = "INSERT INTO mpgu_edu_likes(post_id,author_id) VALUES(" . '"wall-30321356_' . $post['id'] . '", ';
			$strSQL .= $like . ')';

			mysql_query($strSQL);
		}
	}
	$packet++;
} while ($count_posts > $offset + $limit);


// Обновляем лайки tsss_mpgu
$packet = 0;
$limit = 1000;

$strSQL = "DELETE FROM tsss_mpgu_likes;";
mysql_query($strSQL);

do {
    $offset = $packet * $limit;
    $contents = curl('http://api.vk.com/method/wall.get?owner_id=-58507951&count=100&offset=' . $offset);
    $posts = json_decode($contents, true);
	$count_posts = $posts['response'][0];
	array_shift($posts['response']);
	
	foreach ($posts['response'] as $post) {
		
		$like_list = curl('http://api.vk.com/method/likes.getList?type=post&owner_id=-58507951&item_id=' . (string)$post['id']);
		$likes = json_decode($like_list, true);
		foreach ($likes['response']['users'] as $like) {
			
			$strSQL = "INSERT INTO tsss_mpgu_likes(post_id,author_id) VALUES(" . '"wall-58507951_' . $post['id'] . '", ';
			$strSQL .= $like . ')';

			mysql_query($strSQL);
		}
	}
	$packet++;
} while ($count_posts > $offset + $limit);


// Обновляем лайки typical_mpgu
$packet = 0;
$limit = 1000;

$strSQL = "DELETE FROM typical_mpgu_likes;";
mysql_query($strSQL);

do {
    $offset = $packet * $limit;
    $contents = curl('http://api.vk.com/method/wall.get?owner_id=-30973505&count=100&offset=' . $offset);
    $posts = json_decode($contents, true);
	$count_posts = $posts['response'][0];
	array_shift($posts['response']);
	
	foreach ($posts['response'] as $post) {
		
		$like_list = curl('http://api.vk.com/method/likes.getList?type=post&owner_id=-30973505&item_id=' . (string)$post['id']);
		$likes = json_decode($like_list, true);
		foreach ($likes['response']['users'] as $like) {
			
			$strSQL = "INSERT INTO typical_mpgu_likes(post_id,author_id) VALUES(" . '"wall-30973505_' . $post['id'] . '", ';
			$strSQL .= $like . ')';

			mysql_query($strSQL);
		}
	}
	$packet++;
} while ($count_posts > $offset + $limit);

echo '<p>Completed</p><br>';
echo '<a href="index.html">Back</a><br>';

?>