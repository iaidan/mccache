MCCache
=======================================
Player caching script for Minecraft servers

Features
--------

 * Cache multible servers
 * MySQL Database backend
 * Fully HTML Formatted, with option to render without html
 * Easily view player caches

Dependencies
------------
PHP 5.3.X, MySQL 5.6.X, crontab/schtasks

Optional: Apache/IIS

Getting Started
===============

Installing Dependencies
-----------------------

Installing MySQL & PHP

On Red Hat-based distributions:

  	yum install mysql-server mysql
		yum install php php-mysql
	
On Debian based systems such as Ubuntu:

		apt-get install mysql-server libapache2-mod-auth-mysql php5-mysql
		sudo apt-get install php5 libapache2-mod-php5 php5-mcrypt
	
On Windows bases systems:

		I highly reccomend using either XAMP (http://www.apachefriends.org/en/xampp.html)
		or WAMP (http://www.wampserver.com/)


Enabling MCCache
----------------------

1. Paste the script files into the directory they will be ran from

2. Create a MySQL Database for the script and import `structure.sql`

3. Edit the variables in `includes/configuration.inc.php` to suite your needs

4. Edit crontab (Linux systems)
	As the server user you're running the script as:
  	
  		crontab -e
  
  	Add these lines:
  
  		#m 	h 	dom	mon	dow	command
  		*/5 * * * * php FAKEPATH/cache/cache.php ALL
  		0 0 * * * php FAKEPATH/cache/averages.php ALL
		
   Edit schtasks (windows systems)
	Open CMD (Command Prompt) and type:
  	
  		schtasks /create /sc minute /mo 5 /tn "MCCache" /tr "PHP_INTERPRITER FAKEPATH\cache\cache.php ALL"
  		schtasks /create /sc hourly /mo 23 /tn "MCCache" /tr "PHP_INTERPRITER FAKEPATH\cache\averages.php ALL"
	
Viewing Cache data
===============
The simplest way to view cache data is in the web browser using Apache or IIS

From console just type the following (i reccomend having no_html set to true:

1. Windows
    PHP_INTERPRITER FAKEPATH\index.php SERVER

2. Linux
    php FAKEPATH\index.php SERVER

