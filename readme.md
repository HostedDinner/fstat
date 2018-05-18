**_Disclaimer: This piece of software is not conform to the DSGVO, as it stores all IP-Adresses without anonymizing!_**

English
==============
About
--------------
FStat is php stat script, which analyses useragentstring of a visitor with the help of the user-agent-string.info api and stores the stats in xml files. There is no need for a database like MySQL, SQL, ...

Supported features:
 - detects Browser, OS, Bots, Country, Referer, Search Keywords, IPv4/Ipv6
 - Show stats for a month
 - Shows "timeline"
 - Shows last 50 visitors in a list
 - Language: German, English 

This project uses Useragent Parser and Flag Icons from FamFamFam

IMPORTANT NOTE
--------------

This script is still alpha, and the performance is still very bad and it can cause much open/closing files when viewing the stats (~30 per monthsite in 0.6.2) I am workin on caching.




Deutsch
==============
Über
--------------
FStat ist ein PHP Statistik Skript, dass den Useragentstring von einem Besucher mit Hilfe von der user-agent-string.info API analysiert und die Statistik in XML Dateien speichert. Es benötigt keine Dtenbank wie MySQL, SQL, ...

Funktionen:

 - erkennt Browser, OS, Bots, Land, Referer, Suchwörter, IPv4/Ipv6
 - zeigt eine Statistik pro Monat an
 - zeigt eine "Zeitleiste"
 - zeigt letzen 50 Besucher in einer Liste
 - Sprachen: Deutsch, Englisch 

Das Projekt benutzt Useragent Parser und Flag Icons von FamFamFam

WICHTIGE MITTEILUNG
--------------
Dieses Script ist noch im Alphastadium, und die Performance ist noch sehr schlecht. Es kann dazu führen viele Dateien zu öffnen/zu schließen, wenn man sich die Statistik anschaut (~30 pro Monatsseite in 0.6.2). Ich bin an einem Caching am arbeiten.
Screenshots




Usage
==============
 - Unzip the content of the packed file into a new folder for example "fstat".
 - (0.7.5 and prior) Take a look into the file settings.php in the config subdirection (open it with a file editor) and edit it as your needs
 - (0.8+) Create a new file settings.user.php in config subfolder and overwrite settings from seetings.default.php like in the example file.
 - Upload the stuff to your webspace
 - (0.7.5 and prior) Prepare your Sites by adding to every site you want to track following lines:
    define("FSTAT_PATH", "./fstat/");
    include FSTAT_PATH."include.php";
 - (0.8+) Prepare your Sites by adding to every site you want to track following lines (no need to define FSTAT_PATH):
    include "fstat/include.php";
    
 - Now you can view your stat if you change in the dir fstat.




Changelog
==============
0.8.0
--------------
not released yet

0.7.5
--------------
 - shows Last Update of UAS-Data on About Page
 - add a page with last 50 Bots
 - Hostnames resolver for new entrys
 - Average Graph (just HTML/CSS)
 - add link to file in last 50
 - updated some icons 

0.7.1 (update only)
--------------
 - add differnt stylesheet for mobile phone browsers
 - fix some issues with malformed input strings (as site titles) 

0.7
--------------
 - add Atom Feed
 - updated some icons 

0.6.2
--------------
 - add all.php to grab all data at once to avoid open/closing files too often
 - forgot to remove test data in 0.6 :P
 - Countryparser (only full version) searchs with binary search instead of linear (faster)
 - changed apparence a liitle bit (mainly font adjustments) 

0.6
--------------
 - add help text
 - adjust some styles
 - fix keyword detection 

0.5
--------------
 - first release




Screenshots
==============
<img src="https://raw.githubusercontent.com/HostedDinner/fstat/master/showcase/over.png">
<img src="https://raw.githubusercontent.com/HostedDinner/fstat/master/showcase/last.png">