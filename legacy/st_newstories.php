<?php
function insertdb($url, $category){
	include('connection.php');
	$targetPage = $url;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, $targetPage);
	$result = curl_exec($ch);
	curl_close($ch);
	$x = new DOMDocument;
	$x->loadXML($result);
	$grabelearr = array();
	foreach($x->getElementsByTagName('item') as $item) 
	{
		foreach($item->getElementsByTagName('title') as $x)
		{
			$title = $x->nodeValue;
		}
		foreach($item->getElementsByTagName('link') as $y)
		{
			$link = $y->nodeValue;
		}
		foreach($item->getElementsByTagName('pubDate') as $d)
		{
			$date = $d->nodeValue;
		}
		$grabelearr[] = array('title'=>html_entity_decode($title, ENT_QUOTES | ENT_XML1, 'UTF-8'),'link'=>$link, 'date'=>$date, 'category'=>$category);
	}
	foreach($grabelearr as $grabbed)
	{
		$checkq = "SELECT st_link FROM StraitsTimes WHERE st_link = :link";
		$checkx = $dbh->prepare($checkq);
		$checkx->bindValue(':link',$grabbed['link']);
		$checkx->execute();
		$row = $checkx->fetchAll();
		if (!$row)
		{
			date_default_timezone_set ('Asia/Singapore');
			$timestamp = strtotime($grabbed['date']);
			$date = date("Y-m-d H:i:s", $timestamp);
			$insertstq = "INSERT INTO StraitsTimes VALUES(NULL,:title,:link,:pubtime,:cat)";
			$insertstx = $dbh->prepare($insertstq);
			$insertstx->bindParam(':title',$grabbed['title']);
			$insertstx->bindParam(':link',$grabbed['link']);
			$insertstx->bindParam(':pubtime', $date);
			$insertstx->bindParam(':cat',$grabbed['category']);
			$insertstx->execute();
		}
	}
}


$mainrss = 'http://www.straitstimes.com/sites/default/files/rss_breaking_news.opml';
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $mainrss);
$result = curl_exec($ch);
curl_close($ch);
$x = new DOMDocument;
$x->loadXML($result);
$stval = 'http://www.straitstimes.com/';
$feedlist = array();
foreach($x->getElementsByTagName('outline') as $item) 
{
	$rssurl = $item->getAttribute('xmlUrl');
	$combinedurl = $stval.$rssurl;
	$category = $item->getAttribute('title');
	$feedlist[] = array('url'=>$combinedurl,'cat'=>$category);
}
foreach($feedlist as $feed)
{
	insertdb($feed['url'],$feed['cat']);
}

?>
