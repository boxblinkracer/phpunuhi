apt-get update
apt-get install -y git
apt-get install -y php-xdebug
apt-get install -y zip
apt-get install -y unzip

# ----------------------------------------------------
php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer
# ----------------------------------------------------
echo 'phar.readonly=0' >> /usr/local/etc/php/conf.d/docker-php-phar-readonly.ini


tail -f /dev/null