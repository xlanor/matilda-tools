<?php
/*
*PHP Script to consolidate databases
*Only use this if you were on the old db structure (4 seperate tables for news)
*Written by xlanor
*For Matilda
*The purpose of this script is to place all previous tables under one table.
*/

include('staging_connection.php');
$createq = "CREATE TABLE IF NOT EXISTS combinedarticle
			(url_id int(11) NULL AUTO_INCREMENT UNIQUE,
			url_title varchar(200), 
			url_link varchar(200), 
			url_dt datetime, 
			url_cat varchar(150),
			url_site int(11))";
$createx = $dbh->prepare($createq);
$createx->execute();
echo "Created new table".PHP_EOL;

$altertableq = "ALTER TABLE combinedarticle AUTO_INCREMENT = 1";
$altertablex = $dbh->prepare($altertableq);
$altertablex->execute();
echo "Reset autoincre of table".PHP_EOL;

$createsitelistq = "CREATE TABLE IF NOT EXISTS sitelist(site_id int(11) NULL AUTO_INCREMENT UNIQUE, site_name varchar(150))";
$createsitelistx = $dbh->prepare($createsitelistq);
$createsitelistx->execute();

$sitelist = array('StraitsTimes','ChannelNewsAsia','TodayOnline','Mothership');
$counter = 1;
foreach($sitelist as $site)
{
	$insertq = "INSERT INTO sitelist VALUES(:counter,:site)";
 	$insertx = $dbh->prepare($insertq);
	$insertx->bindValue(':counter',$counter);
	$insertx->bindValue(':site',$site);
	$insertx->execute();
	$counter++;
}

$altertableq = "ALTER TABLE sitelist AUTO_INCREMENT = 1";
$altertablex = $dbh->prepare($altertableq);
$altertablex->execute();
echo "Reset autoincre of table".PHP_EOL;

$selectsitelist = "SELECT * FROM sitelist";
$selectx = $dbh->prepare($selectsitelist);
$selectx->execute();
$fetch = $selectx->fetchall();

foreach ($fetch as $f)
{
	migratedb($f['site_id'],$f['site_name']);
}

function migratedb($siteid,$sitename)
{
	include('staging_connection.php');
	if ($sitename == "ChannelNewsAsia")
	{
		$insertfromcnaq = "INSERT INTO combinedarticle(url_title,url_link,url_dt,url_cat,url_site) SELECT cna_title,cna_link,cna_dt,cna_cat,:site FROM ChannelNewsAsia";
		$insertfromcnax = $dbh->prepare($insertfromcnaq);
		$insertfromcnax->bindValue(':site', $siteid);
		$insertfromcnax->execute();
		echo "Migrated cna table".PHP_EOL;
	}
	else if ($sitename == "StraitsTimes")
	{
		$insertfromstq = "INSERT INTO combinedarticle(url_title,url_link,url_dt,url_cat,url_site) SELECT st_title,st_link,st_time,st_cat,:site FROM StraitsTimes";
		$insertfromstx = $dbh->prepare($insertfromstq);
		$insertfromstx->bindValue(':site',$siteid);
		$insertfromstx->execute();
		echo "Migrated ST Table".PHP_EOL;
	}

	else if ($sitename == "TodayOnline")
	{
		$insertfromtodayq = "INSERT INTO combinedarticle(url_title,url_link,url_dt,url_cat,url_site) SELECT today_title,today_link,today_dt,today_cat,:site FROM TodayOnline";
		$insertfromtodayx = $dbh->prepare($insertfromtodayq);
		$insertfromtodayx->bindValue(':site',$siteid);
		$insertfromtodayx->execute();
		echo "Migrated Today table".PHP_EOL;
	}

	else if ($sitename == "Mothership")
	{
		$insertmsq = "INSERT INTO combinedarticle(url_title,url_link,url_dt,url_cat,url_site) SELECT ms_title,ms_url,ms_time,:cat,:site FROM Mothership";
		$insertintomothershipx = $dbh->prepare($insertmsq);
		$insertintomothershipx->bindValue(':cat','mothershit');
		$insertintomothershipx->bindValue(':site',$siteid);
		$insertintomothershipx->execute();
		echo "Migrated mothershit table".PHP_EOL;
	}
}
?>
