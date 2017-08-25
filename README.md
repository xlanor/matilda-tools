# Matilda-tools
Tools for Matilda, a python telegram bot.

## Licensing
Matilda-tools is licensed under the [Affero General Public License Version 3](LICENSE).

## newstories_mothership.php
This is a PHP script to scrape new stories from mothership, and save the Title and URL into a mySQL Database for retrieving by Matilda, in a soon-to-be-built search function.

To execute, write your own connection string (I used PDO as compared to mySQLi for this script, so take note of that), create a database table in your local mySQL table, and run as a cronjob. 

```bash
sudo crontab -e
```

```bash
*/15 * * * * /home/elanor/ftp/files/matilda-tools/newstories_mothership.php
```
