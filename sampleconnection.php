<?php
/*
PDO connection string sample
for matilda-tools.
*/
try
{
    //checking if connection to database is sucessful
    $dbh = new PDO("mysql:host=127.0.0.1;dbname=xmldb;charset=utf8mb4",'user','password'); //set utf-8 encoding for cheena characters
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


}
catch(PDOException $e)
{   //else exception. self-explanatory
    echo $e->getMessage();
}
?>
