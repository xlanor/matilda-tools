<?php
/*
It remains to be seen
whether this script can be
run on TodayOnline's weekend site

*/
include('staging_connection.php');
include('simple_html_dom.php');
function insertDB($target){
	include('staging_connection.php');
	$targetpage = $target;
	//uses cURL to pull the feed
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, $targetpage);
	$result = curl_exec($ch);
	curl_close($ch);
	//places it into a DOM Document and loads XML for parsing
	$x = new DOMDocument;
	$x->loadXML($result);
	$grabelementarray = array();
	foreach($x->getElementsByTagName('item') as $itemelement) //foreach <item> group,
	{
		foreach($itemelement->getElementsByTagName('title') as $title) //retrieve title, decode to remove special char.
		{
			$t = $title->nodeValue;
			$decodedt = html_entity_decode($t, ENT_QUOTES | ENT_XML1);
		}
		foreach($itemelement->getElementsByTagName('link') as $link) //retrieve url
		{
			$url = $link->nodeValue;
		}
		foreach($itemelement->getElementsByTagName('pubDate') as $date) //retrieve date, convert for mySQL insertion
		{
			$pdate = $date->nodeValue;
			date_default_timezone_set ('Asia/Singapore');
			$timestamp = strtotime($pdate);
			$d = date("Y-m-d H:i:s", $timestamp);
		}
		//checks for duplicates
		$checkq = "SELECT url_title FROM combinedarticle WHERE url_link = :link AND url_site = 3";
		$checkx = $dbh->prepare($checkq);
		$checkx->bindValue(':link',$url);
		$checkx->execute();
		$row = $checkx->fetchAll();
		//if no dupes, insert to db.
		if (!$row)
			{
				//extracts the title from the url, then replaces the first word with 

				//extracts category from the url, then replaces the first word with a capitalized first word.
				$arr = explode("/", $url, 4);
				$exploded = $arr[3];
				$secondarr = explode("/",$exploded,2);
				$category = $secondarr[0];
				$capcategory = ucfirst(strtolower($category));
				$insertstq = "INSERT INTO combinedarticle VALUES(NULL,:title,:link,:pubtime,:cat,3)";
				$insertstx = $dbh->prepare($insertstq);
				$insertstx->bindParam(':title',$decodedt);
				$insertstx->bindParam(':link',$url);
				$insertstx->bindParam(':pubtime', $d);
				$insertstx->bindParam(':cat',$capcategory);
				$insertstx->execute();
				//print($url.' inserted into database.'.PHP_EOL); 
			}
			else
			{
				print($url.' exists in database.'.PHP_EOL);
			}
		
	}
	print($target.' has been sucessfully scraped'); //this is to make debugging easier.
}
function delVid(){
	include('staging_connection.php');
	$deleteq = "DELETE FROM combinedarticle WHERE url_cat = 'Videos' AND url_site = 3"; 
	$deletex = $dbh->prepare($deleteq);
	$deletex->execute();
}

$rsspage = 'http://www.todayonline.com/rss-feeds';
$rsslist = array();
$html = file_get_html($rsspage);
$rssrow = $html->find('ul[class=rss]');
//html dom element does not support nested find by class, so we used a simple_html_dom to speed thing sup
foreach ($rssrow as $row)
{
	//gets title
	$title = $row->find('a');
	foreach($title as $t)
	{
		$tit = $t->href;
		$rsslist[] = array('title'=>$tit);
	}
	
	//places into array
}

//now that we have a list of all rss urls, time to pump through this array
foreach($rsslist as $rss)
{
	insertDB($rss['title']);
}
delVid(); //removes all videos, because Matilda does not support videos.

?>
