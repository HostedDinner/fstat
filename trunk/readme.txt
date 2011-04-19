FStat is php stat script, which analyses useragentstring of a visitor with the help of the user-agent-string.info api and stores the stats in xml files.

Supported features:
	* detects Browser, OS, Bots, Country, Referer and Search Keywords
	* Show stats for a month
	* Shows "timeline"
	* Shows last 50 visitors in a list
	* Language: German, English 
	
Usage:
	* Unzip the content of the packed file into a new folder for example "fstat".
	* Take a look into the file settings.php in the config subdirection (open it with a file editor) and edit it as your needs
	* Upload the stuff to your webspace
	* Prepare your Sites by adding to every site you want to track following lines:
		define("FSTAT_PATH", "./fstat/");
		include FSTAT_PATH."include.php";
	* Now you can view your stat if you change in the dir fstat.
	
