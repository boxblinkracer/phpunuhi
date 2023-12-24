sudo apt-get update
sudo apt-get install -y zip
sudo apt-get install -y unzip
sudo apt-get install -y php-sqlite3


sudo chmod 777 /etc/php/8.2/cli/php.ini
sudo echo 'phar.readonly => 0' >  /etc/php/8.2/cli/php.ini


cd /var/www && make restart-php