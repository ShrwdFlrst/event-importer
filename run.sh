export $(cat .env | grep -v ^# | xargs)
php -f import.php