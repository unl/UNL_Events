echo "Updating the wdn templates, this can take a long time..."

wget --quiet -r -nH -np -l 15 --cut-dirs=1 --reject "index.html*,*.LCK" http://wdn.unl.edu/wdn/ -P /var/www/html/www/wdn/
touch /var/www/html/www/wdn/templates_4.0/includes/wdnResources.html

echo "Finished updating the WDN templates"
