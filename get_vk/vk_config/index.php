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

$list = file_get_contents('vk_config.json');
$list = json_decode($list, true);
$strSQL = "";
foreach ($list['publics'] as $public) {
	$strSQL .= "(SELECT * from " . $public . "_posts) UNION ";
}
$strSQL = substr($strSQL, 0, -6);
$strSQL .= " ORDER by count_likes DESC;";

$periods = array(7,30,365);
  
$format = 'Y-m-d H:i';
$curr_time = date("Y-m-d H:i");
$curr_time = DateTime::createFromFormat($format, $curr_time);
  
foreach ($periods as $period) {

  $ath = mysql_query($strSQL);
  if($ath)	{
  
		$posts = array();
		$count_posts = 0;
  
		while($user = mysql_fetch_array($ath) and $count_posts < 20)
		{
			$post_time = date($user['date']);
			$post_time = DateTime::createFromFormat($format, $post_time);
			$interval = date_diff($curr_time, $post_time);
			if ($interval->format('%a') < $period) {
				$this_post["likes"] = $user['count_likes'];
				$this_post["source"] = $user['id'];
				$this_post["date"] = $user['date'];
				$pics = explode(", ", $user['pics']);
				$text = '';
				if ($pics[0] != '') {
					foreach ($pics as $pic) {
						$text .= "<img src='" . $pic . "' alt='pic'> <br>";
					}
				}
				$text .= $user['text'];
				$this_post["text"] = $text;
				$count_posts++;
				$posts[] = $this_post;

			}
		}
		
		$filename = '../../json_data/vk_top_';
		if ($period == 7) $filename .= 'week';
		if ($period == 30) $filename .= 'month';
		if ($period == 365) $filename .= 'all';
		$filename .= '.json';
		
		$fp = fopen($filename, 'w');
		fwrite($fp, json_encode($posts, JSON_UNESCAPED_UNICODE));
	}	  
}
?>