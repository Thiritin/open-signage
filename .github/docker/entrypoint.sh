echo -e "Starting cron jobs."
crond -L /var/log/crond -l 5

echo -e "Migrate Database"
php artisan migrate --force

echo -e "Start Supervisor"
exec "$@"
