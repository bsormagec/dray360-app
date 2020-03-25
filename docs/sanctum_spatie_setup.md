# THESE ARE SETUP NOTES BY PBN. DO NOT USE.





### Installing Airlock

Passport was *NOT* what we wanted. We wanted airlock instead.

https://divinglaravel.com/authentication-and-laravel-airlock
https://github.com/laravel/sanctum
https://laravel-news.com/laravel-airlock
https://laravel.com/docs/7.x/authentication#protecting-routes
https://laravel.com/docs/7.x/sanctum#spa-authentication
https://laravel.com/docs/master/sanctum
https://medium.com/@godilite/using-laravel-airlock-with-vuejs-1d343ee6f10
https://medium.com/@JillevdWeerd/app-authentication-with-laravel-airlock-36e3d2027994 (BUGGY)
https://redfern.dev/vue-js-auth-using-laravel-airlock/
https://serversideup.net/using-laravel-sanctum-airlock-with-nuxtjs/

for postman/cors issue, see: https://github.com/laravel/sanctum/issues/11




### Seeding database

https://laravel.com/docs/6.x/seeding#writing-seeders
https://laravel-news.com/seeding-data-testing




### Writing Order Resource

https://laravel.com/docs/7.x/eloquent-resources
https://scotch.io/tutorials/laravel-eloquent-api-resources




### php artisan command reference

https://quickadminpanel.com/blog/list-of-21-artisan-make-commands-with-parameters/


### Installing the passport (oauth2) and spatie (permissions)

NOTE, PASSPORT NO LONGER WANTED.


see: https://medium.com/@devandtechtest/laravel-rest-api-project-with-oauth-password-grant-for-authentication-spatie-for-permissions-963d933c53b4


also see: https://medium.com/swlh/create-an-admin-middleware-for-with-spatie-laravel-permission-6419152049cf

ASDF RESUME AT "Step 0: Add a test"



ASDF SEE: https://scotch.io/tutorials/user-authorization-in-laravel-54-with-spatie-laravel-permission
AND UNDERSTAND WHY WE DO NOT WANT "user_has_permissions" TABLE
AND HOW THAT CAN BE REMOVED BETWEEN LVL5.4 AND LVL7.0???
WHY REMOVED FROM '/home/pbnelson/repos/tcompanies/ordermaster/config/permission.php'
THESE LINES:
```php
/*
* When using the "HasRoles" trait from this package, we need to know which
* table should be used to retrieve your users permissions. We have chosen a
* basic default value but you may easily change it to any table you like.
*/

'user_has_permissions' => 'user_has_permissions',
```
```text
The v1 table names which are no longer used in v2 are:
role_has_permissions, user_has_roles, user_has_permissions
```

THESE WERE REPLACED WITH "MODEL" AS IN
```list
role_has_permissions
model_has_roles
model_has_permissions
roles
permissions
```


````bash
composer require laravel/ui "^2.0"  # per https://laravel.com/docs/7.x/upgrade#authentication-scaffolding
#composer require laravel/passport
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


Fix VisualCode error with PHPCS configuration:

__phpcs: Request workspace/configuration failed with message: Unable to locate phpcs. Please add phpcs to your global path or use composer dependency manager to install it in your project locally.__

````bash
composer require --dev squizlabs/php_codesniffer
````


### Convert passport to airlock

https://laravel.com/docs/master/airlock

````bash
composer remove laravel/passport

````

Use medium.com page to reverse steps taken when installing

````bash
composer require laravel/ui "^2.0"
php artisan ui vue --auth

````

````bash
#composer require laravel/airlock
#php artisan vendor:publish --provider="Laravel\Airlock\AirlockServiceProvider"
#php artisan vendor:publish --tag=airlock-config # maybe not needed?
#php artisan vendor:publish --tag=airlock-migrations # maybe not needed?
#php artisan migrate

# after SANCTUM rename
composer require laravel/sanctum
composer update
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
composer require fruitcake/laravel-cors


````

````bash
php artisan make:migration add_remember_token_to_users_table --table=users
````


php artisan migrate:make add_paid_to_users





### Create seeder

````bash
php artisan make:seeder RolesAndPermissionsSeeder
# edit database/seeds/RolesAndPermissionsSeeder.php
php artisan db:seed --class=RolesAndPermissionsSeeder

````


### Create Resource

````bash
php artisan make:resource Orders
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





### API USAGE (POSTMAN)

TYPE: POST
URL: https://local.ordermaster.com/api/login
Body: x-www-form-urlencoded
password mongomongo
email peter@peternelson.com



### HTTP-Request server status codes

Reference: https://tools.ietf.org/html/rfc7231#page-49




### DEBUGGING: to see HTTP request headers in auth:middleware

edit `./vendor/laravel/framework/src/Illuminate/Auth/Middleware/Authenticate.php`

find function `protected function authenticate($request, array $guards)`
currently at line 56
for the first line of the function, insert `var_dump($request);`


Here are some working tokens, for debugging...
````text
Accept application/json, text/plain, */*
X-XSRF-TOKEN eyJpdiI6IksyU0lDSGFTOW1EV0U1MFVER0dGeEE9PSIsInZhbHVlIjoiL0ZRKys4Y1dQMEVSRjVaL203Qm11cFRVT3lRYWtST2R0c3k5KzBVakJ1ZEV1MzRlNTRhNW10L3FPaWhZN3JRaiIsIm1hYyI6IjIxYmY1M2VmY2NkMGE0M2VkNTZmN2NjZThlNDQ2OWYzYTYzNmFiYTY5ZTk0M2MyMDhlODUxNTQ1NTUxYjBlZjIifQ==
Referer https://local.ordermaster.com/
Cookie laravel_session=eyJpdiI6InBiTkUwdlNTTEFkUVlpcEhJVXJHWlE9PSIsInZhbHVlIjoiREkvTTZVR1c5cmxXTjByWlNsTEZWWkNUaW1VbmlYUFBLSis0WFZQd1pFVDhFSWw5UjhXZXBxbGl6V2NkL3J2ZiIsIm1hYyI6ImM0NTM2OGQ4ODBjODdmYWMzZGI5ZDQxNWMyNzdlMzlhMDA1OGRiODllZTAzNWYxZDAwZDZiZWI1NjhlZmZiMjIifQ%3D%3D
````
