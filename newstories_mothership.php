<?php
/*
Mothership scrape new articles to database script.
Run with cronjob
Written by xlanor.
*/

include('staging_connection.php');
include('simple_html_dom.php');
$targetPage = 'https://mothership.sg/wp-admin/admin-ajax.php?action=alm_query_posts&query_type=standard&nonce=c218689620&repeater=default&theme_repeater=null&acf=&nextpage=&cta=&comments=&post_type%5B%5D=post&sticky_posts=&post_format=&category=&category__not_in=&tag=&tag__not_in=&taxonomy=&taxonomy_terms=&taxonomy_operator=&taxonomy_relation=&meta_key=&meta_value=&meta_compare=&meta_relation=&meta_type=&author=&year=&month=&day=&post_status=&order=DESC&orderby=date&post__in=&post__not_in=&exclude=&search=&custom_args=&posts_per_page=10&page=0&offset=0&preloaded=false&seo_start_page=1&paging=false&lang=&slug=home&canonical_url=https%3A%2F%2Fmothership.sg%2F';
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $targetPage);
$result = curl_exec($ch);
curl_close($ch);
$obj = json_decode($result,true);
//strips the string that we want from the jsonstring.
$html = $obj['html'];
$results = print_r($obj['html'],true);
$x = new DOMDocument;
$x->loadHTML($results);
$clean = $x->saveXML();
//cleans up the string and save it as an XML string so that we can access it later.
$xml = new DOMDocument();
$xml->loadXML($clean);
$links = array(); //creates an array to place everything that we scraped out in
$date = date('Y-m-d G:i:s');
echo $date.PHP_EOL;
foreach($xml->getElementsByTagName('a') as $link) 
{
	foreach ($link->getElementsByTagName('h1') as $title)
	{
		$links[] = array('url' => $link->getAttribute('href'), 'title' => $title->nodeValue, 'scrapetime' => $date);
	}
	
}
//loops through the array that we created previously to insert into database
foreach ($links as $link)
{
	$checkq = "SELECT url_title FROM combinedarticle WHERE url_link = :msurl AND url_site = 4";
	$checkx = $dbh->prepare($checkq);
	$checkx->bindValue(':msurl',$link['url']);
	$checkx->execute();
	$row = $checkx->fetchAll();
	if (!$row)
	{
	    $insertms = "INSERT INTO combinedarticle VALUES(NULL,:mstitle,:msurl,:mstime,:cat,4)"; 
	    $insertx = $dbh->prepare($insertms);
	    $insertx->bindValue(':mstitle',html_entity_decode($link['title'], ENT_QUOTES | ENT_XML1, 'UTF-8'));
	    $insertx->bindValue(':mstime',$link['scrapetime']);
	    $insertx->bindValue('cat',"mothershit");
	    $insertx->bindValue(':msurl',$link['url']);
	    $insertx->execute();
	}
	
}
?>