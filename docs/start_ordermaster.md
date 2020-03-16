


### Installing the passport (oauth2) and spatie (permissions)

see: https://medium.com/@devandtechtest/laravel-rest-api-project-with-oauth-password-grant-for-authentication-spatie-for-permissions-963d933c53b4

````bash
composer require laravel/passport
php artisan migrate
php artisan passport:install
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="migrations"
php artisan migrate:refresh # DESTRUCTIVE!
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="config"

````

Note these secrets logged to stdout

````log
Encryption keys generated successfully.
Personal access client created successfully.
Client ID: 1
Client secret: hhW6k1DiB8Ztf9jZBIRPMq2gCekFJeCYjXJ7eW46
Password grant client created successfully.
Client ID: 2
Client secret: eXJICT1J1CgVStvpuK19YSUJpB4TGlSRbJAAKdnz

````


Fix VisualCode error with PHPCS configuration

````bash
composer require --dev squizlabs/php_codesniffer
````






### Running the app

````bash
sudo ln -s /home/pbnelson/repos/tcompanies/ordermaster/public /var/www/html/localom
cd /home/pbnelson/repos/tcompanies/ordermaster/frontend
npm run serve

````

see: https://local.ordermaster.com/
see: http://192.168.8.25:8080/
see: http://localhost:8080/

