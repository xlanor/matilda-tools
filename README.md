# Matilda-tools
Tools for Matilda, a python telegram bot.
Matilda is mostly written in python, but I'll be writing her tools in PHP where possible, so that I can take the opportunity to brush up on both languages.

## Licensing
Matilda-tools is licensed under the [Affero General Public License Version 3](LICENSE).

## newstories_mothership.php
This is a PHP script to scrape new stories from mothership, and save the Title and URL into a mySQL Database for retrieving by Matilda, in a soon-to-be-built search function.

To execute, write your own connection string (I used PDO as compared to mySQLi for this script, so take note of that), create a database table in your local mySQL table, and run as a cronjob. 

## st_newstories.php
This is a PHP script to scrape new stories from Straits Times using the RSS feed, and save the Title, URL and category into a mySQL Database for retrieving by Matilda, in a soon-to-be-built search function.

To execute, write your own connection string (I used PDO as compared to mySQLi for this script, so take note of that), create a database table in your local mySQL table, and run as a cronjob. 

## cna_newstories.php
This is a PHP script to scrape new stories from CNA using the RSS feed, and save the Title, URL and category into a mySQL Database for retrieving by Matilda, in a soon-to-be-built search function.

To execute, write your own connection string (I used PDO as compared to mySQLi for this script, so take note of that), create a database table in your local mySQL table, and run as a cronjob. 
This requires [Simple HTML DOM](http://simplehtmldom.sourceforge.net/) to run.

## running the script as a cronjob.

```bash
sudo crontab -e
```
You can set 20 to a value that you are comfortable with. Read up on cronjob scheduling for more information.

```bash
*/20 * * * * /usr/bin/php7.0 /home/matilda-tools/newstories_mothership.php
```

To make sure that the cronjob is running,
```bash
grep CRON /var/log/syslog
```

