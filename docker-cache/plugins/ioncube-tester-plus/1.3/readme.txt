=== ionCube tester plus ===
Contributors:      harmr
Plugin Name:       IonCube Tester Plus
Plugin URI:        http://www.mapsmarker.com
Tags:              ioncube, loader, php, encoding, test
Author URI:        http://www.harm.co.at
Author:            Robert Harm
Donate link:       http://www.mapsmarker.com/donations
Requires at least: 2.x 
Tested up to:      3.9
Stable tag:        1.3
License:           GPLv2

This plugin helps you to determine if the ionCube loaders are installed correctly on your web server.

== Description ==

[ionCube encoder](http://www.ioncube.com) is an established industry standard solution for PHP encoding. In order to run encrypted files on your webserver, it has have ionCube encoders installed. This plugin checks if this is true and if not, you are given a guidance through the official loader wizard which determines what exactly has to be installed on your server on how this can be achieved (if you are not admin of your webserver, you are given instructions which you can easily forward to your admin).

== Installation ==

= The Famous 3-Minute Installation =

1. Login on your WordPress site with your user account (needs to have admin rights!)
2. Select "Add New" from the "Plugins" menu
3. Search for **ioncube**
4. Click on "Install now" below the entry "ionCube Tester Plus"
5. Click on "OK" on the popup "Are you sure you want to install this plugin?"
6. Click "Activate Plugin"

Done. You will immediately see an admin notice if the loaders are installed on your server or not. This plugin has no settings page.

== Frequently Asked Questions ==

= What do I do if ionCube loaders are not on my server? =

You can start an interactive loader wizard within this plugin which will give you guidance on how to proceed to install the needed loader files on your webserver.

== Screenshots ==

1. Admin notice when loader is installed on your server
2. Admin notice when loader is NOT installed on your server
3. loader wizard gives info on how to install the loader on your server

== Upgrade Notice ==
= v1.3 =
added support for partially finished loader installation, where php.ini still has to be copied to /wp-admin/

== Changelog ==
= v1.3 =
added support for partially finished loader installation, where php.ini still has to be copied to /wp-admin/

= v1.2 =
add fallback methods for detecting ioncube loaders (needed if extension_loaded('ionCube Loader') is unavailable, done by parsing phpinfo() and checking for ioncube_file_is_encoded()-function).

= v1.1 =
show version of installed ioncube loader

= v1.0 =
initial release