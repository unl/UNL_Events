APP_BASEDIR="/var/www/html"

echo "installing unl_ucbcn_system"

#Go to the basedir to perform commands.
cd $APP_BASEDIR

git submodule init
git submodule update

/usr/local/bin/composer install

make

#copy .htaccess
if [ ! -f ${APP_BASEDIR}/.htaccess ]; then
    echo "Creating .htaccess"
    cp ${APP_BASEDIR}/www/sample.htaccess ${APP_BASEDIR}/www/.htaccess
fi

#copy config
if [ ! -f ${APP_BASEDIR}/config.inc.php ]; then
    echo "Creating config.inc.php"
    cp ${APP_BASEDIR}/config.sample.php ${APP_BASEDIR}/config.inc.php
fi

echo "FINISHED installing unl_ucbcn_system"
echo "... You will still need to load a sample database using 'mysql -uevents -ppassword events < events_same_data.sql'"
