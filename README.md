UNL Events
==================

This is the new branch for the manager rewrite of UNL Events. 
This branch will eventually contain the manager, frontend, and backend, all in one repo.

INSTALL
-------
1. run `git submodule init`
2. run `git submodule update`
3. run `cp config.sample.php config.inc.php`
4. run `cp www/sample.htaccess www/.htaccess`
5. run `composer install` Don't get composer through brew, as it is outdated in there. Instead get it at the composer website.
6. run `wget -r -nH -np -l 15 --cut-dirs=1 --reject "index.html*,*.LCK" http://wdn.unl.edu/wdn/ -P www/wdn/` to get the latest WDN stuff.
7. This misses an empty file that the code looks for. Run `touch www/wdn/templates_4.0/includes/wdnResources.html`
8. You need to compile the stuff. First, `npm install less-plugin-clean-css` This dependency is missing for some reason.
9. Now `make`. Your assets should now be compiled.
10. Set up a database.  For now we are copying down the live data into a development database.
11. customize config.inc.php and www/.htaccess to your environment.


