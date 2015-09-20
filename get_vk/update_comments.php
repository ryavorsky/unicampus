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

// Обновляем комментарии mpgu_edu
$packet = 0;
$limit = 1000;

$strSQL = "SET NAMES utf8;";
mysql_query($strSQL);

$strSQL = "DELETE FROM mpgu_edu_comments;";
mysql_query($strSQL);

do {
    $offset = $packet * $limit;
    $contents = curl('http://api.vk.com/method/wall.get?owner_id=-30321356&count=100&offset=' . $offset);
    $posts = json_decode($contents, true);
	$count_posts = $posts['response'][0];
	array_shift($posts['response']);
	
	foreach ($posts['response'] as $post) {
		
		$comm_packet = 0;
		$comm_limit = 100;
		
		do {
			$comm_offset = $comm_packet * $comm_limit;
			$comments_list = curl('http://api.vk.com/method/wall.getComments?owner_id=-30321356&post_id=' . (string)$post['id'] . "&count=100&offset=" . $comm_offset);
			$comments = json_decode($comments_list, true);
			$count_comments = $comments['response'][0];
			array_shift($comments['response']);
			foreach ($comments['response'] as $comment) {
			
				$strSQL = "INSERT INTO mpgu_edu_comments(post_id,date,author_id,text) VALUES(" . '"wall-30321356_' . $post['id'] . '", ';
				$strSQL .= '"' . date('Y-m-d H:i', $comment['date'] - 3600) . '", ';
				$strSQL .= $comment['uid'] . ", '" . $comment['text'] . "')";

				mysql_query($strSQL);
				
			}
			$comm_packet++;
		} while ($count_comments > $comm_offset + $comm_limit);
	}
	$packet++;
} while ($count_posts > $offset + $limit);




// Обновляем комментарии tsss_mpgu
$packet = 0;
$limit = 1000;

$strSQL = "DELETE FROM tsss_mpgu_comments;";
mysql_query($strSQL);

do {
    $offset = $packet * $limit;
    $contents = curl('http://api.vk.com/method/wall.get?owner_id=-58507951&count=100&offset=' . $offset);
    $posts = json_decode($contents, true);
	$count_posts = $posts['response'][0];
	array_shift($posts['response']);
	
	foreach ($posts['response'] as $post) {
		
		$comm_packet = 0;
		$comm_limit = 100;
		
		do {
			$comm_offset = $comm_packet * $comm_limit;
			$comments_list = curl('http://api.vk.com/method/wall.getComments?owner_id=-58507951&post_id=' . (string)$post['id'] . "&count=100&offset=" . $comm_offset);
			$comments = json_decode($comments_list, true);
			$count_comments = $comments['response'][0];
			array_shift($comments['response']);
			foreach ($comments['response'] as $comment) {
			
				$strSQL = "INSERT INTO tsss_mpgu_comments(post_id,date,author_id,text) VALUES(" . '"wall-58507951_' . $post['id'] . '", ';
				$strSQL .= '"' . date('Y-m-d H:i', $comment['date'] - 3600) . '", ';
				$strSQL .= $comment['uid'] . ", '" . $comment['text'] . "')";

				mysql_query($strSQL);
				
			}
			$comm_packet++;
		} while ($count_comments > $comm_offset + $comm_limit);
	}
	$packet++;
} while ($count_posts > $offset + $limit);



// Обновляем комментарии typical_mpgu
$packet = 0;
$limit = 1000;

$strSQL = "DELETE FROM typical_mpgu_comments;";
mysql_query($strSQL);

do {
    $offset = $packet * $limit;
    $contents = curl('http://api.vk.com/method/wall.get?owner_id=-30973505&count=100&offset=' . $offset);
    $posts = json_decode($contents, true);
	$count_posts = $posts['response'][0];
	array_shift($posts['response']);
	
	foreach ($posts['response'] as $post) {
		
		$comm_packet = 0;
		$comm_limit = 100;
		
		do {
			$comm_offset = $comm_packet * $comm_limit;
			$comments_list = curl('http://api.vk.com/method/wall.getComments?owner_id=-30973505&post_id=' . (string)$post['id'] . "&count=100&offset=" . $comm_offset);
			$comments = json_decode($comments_list, true);
			$count_comments = $comments['response'][0];
			array_shift($comments['response']);
			foreach ($comments['response'] as $comment) {
			
				$strSQL = "INSERT INTO typical_mpgu_comments(post_id,date,author_id,text) VALUES(" . '"wall-30973505_' . $post['id'] . '", ';
				$strSQL .= '"' . date('Y-m-d H:i', $comment['date'] - 3600) . '", ';
				$strSQL .= $comment['uid'] . ", '" . $comment['text'] . "')";

				mysql_query($strSQL);
				
			}
			$comm_packet++;
		} while ($count_comments > $comm_offset + $comm_limit);
	}
	$packet++;
} while ($count_posts > $offset + $limit);

echo '<p>Completed</p><br>';
echo '<a href="index.html">Back</a><br>';

?>