



# Manually deploying to https://dev.dray360.com




### Make copy of local repo over to the hosted dev server

````bash
ssh ocr-dev02 'rm -fr ordermaster; mkdir -p ordermaster/public/ ordermaster/frontend/public/'
````

````bash
cd /home/pbnelson/repos/tcompanies/ordermaster
git checkout master && git pull --all && git reset --hard origin/master
rsync -av --exclude '.git/' --exclude 'node_modules/' --exclude 'vendor/' --exclude 'frontend/node_modules/' --exclude 'storage/logs/' --exclude 'tmp/' --exclude 'public/'  ../ordermaster  ocr-dev02:
rsync -av --exclude="css" --exclude="img" --exclude="js" ../ordermaster/public/ ocr-dev02:ordermaster/public/
rsync -av ../ordermaster/frontend/public/ ocr-dev02:ordermaster/frontend/public/

````


### Move ordermaster folder over to web directory

````bash
ssh ocr-dev02

# make new folder and move ordermaster files into it
folder="pbn_$(date '+%Y%m%d')"
sudo rm -fr /var/www/deploybot/${folder}
sudo mv ./ordermaster /var/www/deploybot/${folder}

# copy env files into new folder
cd /var/www/deploybot/${folder}
cp /var/www/deploybot/env_files/.env* ./
cp /var/www/deploybot/env_files/frontend/.env* ./frontend/

# fix file ownership and group permissions
sudo chown -R deploybot:deploybot /var/www/deploybot
sudo chmod -R g+w /var/www/deploybot/${folder}/storage


````

### Install correct .env files, install depencencies, build vuetify

````bash

# switch to deploybot user and current laravel runtime directory
sudo su -c "sudo -iu deploybot"
cd /var/www/html/
cd -P ..

# run npm install, composer install
npm install
composer install

# run migrations, seeds, clear cache, restart horizon
php artisan migrate
php artisan db:seed --class="ProfitToolsCushingSeeder"  # will not add duplicates
# in testing only: php artisan migrate:fresh --database=testing --drop-views
php artisan db:seed --class="LaratrustSeeder"  # will not add duplicates
php artisan cache:clear
php artisan horizon:terminate
# only if the above horizon command fails to start artisan: php artisan queue:restart

# build frontend
cd frontend
npm install
npm run build

exit

````

### Symlink the new deploy folder to "current" and restart server

````bash
# remake symlink
sudo rm /var/www/deploybot/current
sudo ln -s /var/www/deploybot/${folder}/public /var/www/deploybot/current

# restart web server
sudo systemctl restart httpd.service php-fpm.service
sudo systemctl status httpd.service php-fpm.service

````

