#!/bin/bash

set -e

if ! [ -f "/var/www/html/wp-config.php" ]; then
  echo "[ Creating wp-config.php ]"
  cp wp-config-sample.php wp-config.php
fi

if ! [ -d "/var/www/html/wp-content/themes/pcc" ]; then
  echo "[ Download theme ]"
  cd /var/www/html/wp-content/themes/
  git clone https://github.com/coopersystem-fsd/pcc.git
fi

if ! [ -d "/var/www/html/wp-content/plugins/pcc-framework" ]; then
  echo "[ Download plugin ]"
  cd /var/www/html/wp-content/plugins
  git clone https://github.com/coopersystem-fsd/pcc-framework.git
fi

if [[ "$ENV" == "hom" || "$ENV" == "prod" ]]; then
  cd /var/www/html/wp-content/themes/pcc
  git checkout -f master
  echo "[ PCC theme - Branch: master ]"
  cd /var/www/html/wp-content/plugins/pcc-framework
  git checkout -f master
  echo "[ PCC plugins - Branch: master ]"
else
  cd /var/www/html/wp-content/themes/pcc
  git checkout -f dev
  echo "[ PCC theme - Branch: dev ]"
  cd /var/www/html/wp-content/plugins/pcc-framework
  git checkout -f dev
  echo "[ PCC plugins - Branch: dev ]"
fi  

cd /var/www/html/wp-content/themes/pcc
echo "[ Install PCC theme dependencies ]"
npm install
composer install
npm run build:production


cd /var/www/html/wp-content/plugins/pcc-framework
echo "[ Install PCC Plugin dependencies ]"
npm install
composer install
npm run build:production


cd /var/www/html

echo "[ Set permissions to cache folder ]"
mkdir -p wp-content/uploads/cache
mkdir -p wp-content/cache
mkdir -p wp-content/w3tc-config

echo "[ Set permissions to some folders ]"
chmod -R 777 wp-content/uploads/cache
chmod -R 777 wp-content/cache
chmod -R 777 wp-content/w3tc-config

if [ "${1#-}" != "$1" ]; then
  echo "[ Starting apache2 service ]"
  set -- apache2-foreground "$@"
fi

echo "[ Starting mencached service ]"
service memcached stop
service memcached start

cd /var/www/html
echo "[ Clean WP cache ]"
wp core install && wp cache flush --allow-root

exec "$@"

docker-entrypoint.sh apache2-foreground