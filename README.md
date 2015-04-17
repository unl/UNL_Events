UNL Events
==================

This is the new branch for the manager rewrite of UNL Events. 
This branch will eventually contain the manager, frontend, and backend, all in one repo.

INSTALL using vagrant
---------------------
1. run `cd vagrant/dev`
2. run `vagrant up`
3. Wait for vagrant to install, this can take quite a long time.
4. The initial install will take place, including the manual tasks outlined below.
5. Install the sample data set

You will need to load an sql data set.  You can do this by:
1. copy the .sql file to the project root
2. run `cd vagrant/dev`
3. run `vagrant ssh` with the vagrant machine running
4. once you are ssh'd into the machine, run `cd /var/www/html`
5. run `mysql -uevents -ppassword events < name-of-your-sql-file.sql`

manual INSTALL
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


