OverMonitor
===========

README

	Over Monitor is a Web App for Smartphone that can check the status of your server. The application was created to monitor your server even when you not have of your PC, with a smartphone you can check the status of the server.
	
	Over Monitor is a Free Software and Open Source. 

	You can control:
	* System Information (Distribution, Kernel, CPU and more);
    * Uptime;
    * Load Average;
    * Memory (Total, Freed, Used);
    * Partitions;
    * Traffic Meter.
    
    Also every day generate the chart with Load Average and Memory, you can see the graph of the day in the "Statistics" section and graphs passed in the folder "/chart".
    
CONTACT and WEB SITE

	If you have problems, questions, ideas or suggestions, please contact by eMail: info@andreadraghetti.it
	Visit the web site for the latest news and download:
	
		http://www.overmonitor.org
		
REQUIREMENT

	For use Over Monitor you must make sure you have these Software:
	* Linux servers;
	* Apache, NGINX, LigHTTPD or any web server support PHP;
	* GD Library;
	* FreeType PHP
	
INSTALLATION GUIDE

	It is really easy to install Over Monitor, you must not configure ANYTHING! Extract the latest version of Over Monitor in a folder on your Web Server, access the url from your smartphone and you will see the data in real time.
	
	Is recommended the creation of a crontab for proper generation of charts.
	
UPDATE GUIDE
	
	To update you should delete all previous files except the folder "/chart" and "/log". Then upload the new files.
	
CRONTAB

	Over Monitor can refresh the data on each visit or you can automate this process using cron. The automatic update is essential for the creation of the charts, every 15 minutes will add new values. Configuring your crontab file will vary from host-to-host--some even offer a web-based GUI to simplify the process. In any case you'll want cron to perform the following command every fifteen minutes:
	
	curl -L -s 'http://example.com/over_monitor/functions.php'
	
	If you were manually editing your crontab file you would add the following line:
	
	00,15,30,45 * * * * curl -L -s --user-agent 'Fever Refresh Cron''http://example.com/over_monitor/functions.php'
	
	If the command "curl" does not work, we recommend using "wget". Example:
	
	wget http://example.com/over_monitor/functions.php -O /dev/null --cache=off
	
PASSWORD PROTECTION

	Over Monitor integrates a protection system to prevent unknown users to access information of your server, if you don't want this feature you will have to comment on the sixth and seventh line of the file index.php.
	
	To customize the password (default is "hello") edit the sixth line of the file index.php, writing your password by encrypting it sha1().
	
	For convenience, you can use this online calculator: http://www.xorbin.com/tools/sha1-hash-calculator

CHANGELOG

	0.1 (beta)
		* He was born Over Monitor.
	0.2 (beta)
		* Abandoned the platform iWebKit, new GUI with jQuery Mobile;
		* English translation is 100%.
	0.3 (beta)
		* Improved graphics;
		* Reduced package size by 50%;
		* Outsourced the data collection function.
	0.4 (beta)
		* Optimized the function of collecting data from the server;
		* Fixed problem in the url of WordPress;
		* Center the header image;
		* Commented most of the code;
		* Added favicon;
		* Added an eMail to contact the developer.
	0.5 (beta)
		* Fixed the background of charts;
		* Added header to prevent caching of charts;
		* Other small fixes of php code.
	0.6 (beta)
		* Removed unused fonts;
		* Ulterior reduced package size by 50%;
		* Adding the date in the Chart.
	0.7 (beta)
		* Update the floating balloon for iOS User;
		* Added support for iPhone Web App Fullscreen (also iphone 5);
		* Added touchIcon for iOS user;
		* Added version control, you will be notified of the availability of a new version.
	0.8 (beta)
		* Improved reading of the X axis;
		* Reduced the number of PHP files, all in the index page;
		* Improved graphics;
		* Added effects of the transaction page;
		* Update jQuery Mobile Script and CSS;
		* Fixed background of the chart memory now transparent;
		* New icons in the menu;
		* Logo adapted to the retina display.
	0.9 (beta)
		* Fixed position of Footer bar;
		* Implement Authentication System (sha1 password encryption);
		* Minor bug fixed.
	1.0
		* Displays the version of Apache and PHP;
		* View the default password in the login screen;
		* Changed the credits at the footer of the pages;
		* Repository moved to GitHub;
		* Minor bug fixed.
	1.1
		* Updated About Page, fix translation error;
		* Update jQuery Mobile CSS and Script to latest stable version: 1.3.2;
		* Fixed a bug in the status bar of iOS7, was all black with nothing;
		* Remove JavaScript for Windows Phone 8, don't work;
		* Update Server Status Page, fix translation error;
		* New website (overmonitor.org) and return on SourceForge (sorry for the short change);
		* Updated the script for updates. 
	1.2
		* Repository moved definitively to GitHub;
		* pChart update from version 2.1.3 to 2.1.4 (The update fixes several vulnerabilities);
		* Update Social Icons Image.
		
	
CREDITS

	Andrea Draghetti is the creator of the project, I want thank:
	* Simone Margaritelli to support for coding;
	* Matteo Spinelli (cubiq.org) for iOS floating balloon.
	
LICENSE

GNU General Public License version 2.0 (GPLv2)