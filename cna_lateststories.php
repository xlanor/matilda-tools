<?php
include('simple_html_dom.php');
//begin insert to database function
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
	//finds the <item> tag in the XML
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
		foreach($itemelement->getElementsByTagName('category') as $cat) //retrieve cat
		{
			$category = $cat->nodeValue;
		}
		foreach($itemelement->getElementsByTagName('pubDate') as $date) //retrieve date, convert for mySQL insertion
		{
			$pdate = $date->nodeValue;
			date_default_timezone_set ('Asia/Singapore');
			$timestamp = strtotime($pdate);
			$d = date("Y-m-d H:i:s", $timestamp);
		}
		//checks for duplicates
		$checkq = "SELECT url_link FROM combinedarticle WHERE url_link = :link AND url_site = 2";
		$checkx = $dbh->prepare($checkq);
		$checkx->bindValue(':link',$url);
		$checkx->execute();
		$row = $checkx->fetchAll();
		//if no dupes, insert to db.
		if (!$row)
		{
			$insertstq = "INSERT INTO combinedarticle VALUES(NULL,:title,:link,:pubtime,:cat,2)";
			$insertstx = $dbh->prepare($insertstq);
			$insertstx->bindParam(':title',$decodedt);
			$insertstx->bindParam(':link',$url);
			$insertstx->bindParam(':pubtime', $d);
			$insertstx->bindParam(':cat',$category);
			$insertstx->execute();
		}
		
	}
	print($target.' has been sucessfully scraped'); //this is to make debugging easier.
}

$mainpage = 'http://www.channelnewsasia.com/news/rss';
$rsslist = array();
$html = file_get_html($mainpage);
$rssrow = $html->find('div[class=rss-table-row]');
//html dom element does not support nested find by class, so we used a simple_html_dom to speed thing sup
foreach ($rssrow as $row)
{
	//gets title
	$title = $row->find('div[class=rss-table-col1]');
	$url = $row->find('a');
	foreach($title as $t)
	{
		$tit = $t->plaintext;
	}
	//gets url.
	foreach($url as $u)
	{
		$ur = $u->plaintext;
	}
	//places into array
	$rsslist[] = array('title'=>$tit,'url'=>$ur);
}

//now that we have a list of all rss urls, time to pump through this array
foreach($rsslist as $rss)
{
	insertDB($rss['url']);
}
?>