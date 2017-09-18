<?php
/*
*PHP Script to consolidate databases
*Written by xlanor
*For Matilda
*The purpose of this script is to place all previous tables under one table.
*/

include('staging_connection.php');
$createq = "CREATE TABLE IF NOT EXISTS combinedarticle(url_id int(11) NULL AUTO_INCREMENT UNIQUE,url_title varchar(200), url_link varchar(200), url_dt datetime, url_cat varchar(150),url_site varchar(150))";
$createx = $dbh->prepare($createq);
$createx->execute();
echo "Created new table".PHP_EOL;

$altertableq = "ALTER TABLE combinedarticle AUTO_INCREMENT = 1";
$altertablex = $dbh->prepare($altertableq);
$altertablex->execute();
echo "Reset autoincre of table".PHP_EOL;

$insertfromcnaq = "INSERT INTO combinedarticle(url_title,url_link,url_dt,url_cat,url_site) SELECT cna_title,cna_link,cna_dt,cna_cat,:site FROM ChannelNewsAsia";
$insertfromcnax = $dbh->prepare($insertfromcnaq);
$insertfromcnax->bindValue(':site','CNA');
$insertfromcnax->execute();
echo "Migrated cna table".PHP_EOL;


$insertfromstq = "INSERT INTO combinedarticle(url_title,url_link,url_dt,url_cat,url_site) SELECT st_title,st_link,st_time,st_cat,:site FROM StraitsTimes";
$insertfromstx = $dbh->prepare($insertfromstq);
$insertfromstx->bindValue(':site','ST');
$insertfromstx->execute();
echo "Migrated ST Table".PHP_EOL;


$insertfromtodayq = "INSERT INTO combinedarticle(url_title,url_link,url_dt,url_cat,url_site) SELECT today_title,today_link,today_dt,today_cat,:site FROM TodayOnline";
$insertfromtodayx = $dbh->prepare($insertfromtodayq);
$insertfromtodayx->bindValue(':site','Today');
$insertfromtodayx->execute();
echo "Migrated Today table".PHP_EOL;



$insertmsq = "INSERT INTO combinedarticle(url_title,url_link,url_dt,url_cat,url_site) SELECT ms_title,ms_url,ms_time,:cat,:site FROM Mothership";
$insertintomothershipx = $dbh->prepare($insertmsq);
$insertintomothershipx->bindValue(':cat','mothershit');
$insertintomothershipx->bindValue(':site','mothership');
$insertintomothershipx->execute();
echo "Migrated mothershit table".PHP_EOL;
?>
