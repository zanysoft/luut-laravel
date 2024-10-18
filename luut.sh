# Description:
# This is a pipline script for frontend ...!

# Usage
# Run Pipline

echo -e $PWD

echo -e '\e[1m\e[34mEntering into frontend directory...\e[0m\n'

#go to root
# shellcheck disable=SC2164
cd ~/

#go to project directory
# shellcheck disable=SC2164
cd /var/www/luut-frontend

git restore .
git reset --hard

echo -e '\e[1m\e[34mGoing to take pull...\e[0m\n'
git pull origin main
echo 'git pull done'

echo -e '\e[1m\e[34mInstalling composer dependencies\e[0m\n'
composer install --no-interaction --prefer-dist --optimize-autoloader

echo -e '\e[1m\e[34mBootstrap & Storage Directory permission update\e[0m\n'
sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache

echo -e '\e[1m\e[34mMigrating database\e[0m\n'
php artisan migrate

echo -e '\e[1m\e[34mClearing optimizations\e[0m\n'
php artisan optimize:clear

echo -e '\e[1m\e[34mAll Done...\e[0m\n'
