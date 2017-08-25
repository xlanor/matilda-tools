<?php
include('connection.php');
include('simple_html_dom.php');
$html = file_get_contents('https://mothership.sg/');
$targetPage = 'https://mothership.sg/wp-admin/admin-ajax.php?action=alm_query_posts&query_type=standard&nonce=c218689620&repeater=default&theme_repeater=null&acf=&nextpage=&cta=&comments=&post_type%5B%5D=post&sticky_posts=&post_format=&category=&category__not_in=&tag=&tag__not_in=&taxonomy=&taxonomy_terms=&taxonomy_operator=&taxonomy_relation=&meta_key=&meta_value=&meta_compare=&meta_relation=&meta_type=&author=&year=&month=&day=&post_status=&order=DESC&orderby=date&post__in=&post__not_in=&exclude=&search=&custom_args=&posts_per_page=10&page=0&offset=0&preloaded=false&seo_start_page=1&paging=false&lang=&slug=home&canonical_url=https%3A%2F%2Fmothership.sg%2F';
// We use @ here to negotiate any html errors and make code sample shorter in demo purposes.
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $targetPage);
$result = curl_exec($ch);
curl_close($ch);

$obj = json_decode($result,true);
$html = $obj['html'];
$results = print_r($obj['html'],true);
$x = new DOMDocument;
$x->loadHTML($results);
$clean = $x->saveXML();

$xml = new DOMDocument();

$xml->loadXML($clean);

$links = array();

$counter = 0;
foreach($xml->getElementsByTagName('a') as $link) 
{
	foreach ($link->getElementsByTagName('h1') as $title)
	{
		$links[] = array('url' => $link->getAttribute('href'), 'title' => $title->nodeValue);
	}
	foreach ($link->getElementsByTagName('span') as $pubtime)
	{
		$links[$counter]['pub_time'] = $pubtime->nodeValue;
	}
	$counter++;
}

foreach ($links as $link)
{
	$checkq = "SELECT ms_url FROM Mothership WHERE ms_url = :msurl";
	$checkx = $dbh->prepare($checkq);
	$checkx->bindValue(':msurl',$link['url']);
	$checkx->execute();
	$row = $checkx->fetchAll();
	if (!$row)
	{
		$insertms = "INSERT INTO Mothership VALUES(NULL,:mstitle,:mstime,:msurl)"; //use lower to help w/searching.
	    $insertx = $dbh->prepare($insertms);
	    $insertx->bindValue(':mstitle',$link['title']);
	    $insertx->bindValue(':mstime',$link['pub_time']);
	    $insertx->bindValue(':msurl',$link['url']);
	    $insertx->execute();
	}
	
}

?>