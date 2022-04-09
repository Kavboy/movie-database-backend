if [ -f "./.installed" ]; then
    exit 0
fi

echo =============== PHP STARTED                   ==========================

echo =============== PHP Composer Install                ==========================
composer install --optimize-autoloader --no-dev

echo =============== PHP Create Key                      ==========================
php artisan key:generate

echo =============== PHP Clear                           ==========================
php artisan cache:clear
php artisan optimize:clear
php artisan config:clear
php artisan route:clear

echo =============== PHP New Config                      ==========================
php artisan config:cache

echo =============== PHP New View cache                  ==========================
php artisan view:cache

echo =============== PHP Migrate                         ==========================
php artisan migrate --seed

echo =============== PHP New Route cache                 ==========================
php artisan route:cache

echo =============== PHP Optimize                        ==========================
php artisan optimize

echo =============== PHP Finished                        ==========================
touch ./.installed
