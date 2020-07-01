



# Manually deploying to https://dev.dray360.com




### Make copy of local repo over to the hosted dev server

````bash
ssh ocr-dev02 'rm -fr ordermaster; mkdir -p ordermaster/public/ ordermaster/frontend/public/'
````

````bash
cd /home/pbnelson/repos/tcompanies/ordermaster
git checkout master
git pull --all
rsync -av --exclude '.git/' --exclude 'node_modules/' --exclude 'vendor/' --exclude 'frontend/node_modules/' --exclude 'storage/logs/' --exclude 'tmp/' --exclude 'public/'  ../ordermaster  ocr-dev02:
rsync -av --exclude="css" --exclude="img" --exclude="js" ../ordermaster/public/ ocr-dev02:ordermaster/public/
rsync -av ../ordermaster/frontend/public/ ocr-dev02:ordermaster/frontend/public/

````


### Move ordermaster folder over to web directory

````bash
ssh ocr-dev02

folder="pbn_$(date '+%Y%m%d')"
sudo rm -fr /var/www/deploybot/${folder}
sudo mv ./ordermaster /var/www/deploybot/${folder}
sudo rm /var/www/deploybot/current
sudo ln -s /var/www/deploybot/${folder}/public /var/www/deploybot/current
sudo chown -R deploybot:deploybot /var/www/deploybot
sudo chmod -R g+w /var/www/deploybot/${folder}/storage

````

### Install correct .env files, install depencencies, build vuetify

````bash
sudo su -c "sudo -iu deploybot"
folder="pbn_$(date '+%Y%m%d')"
cd /var/www/deploybot/${folder}
cp /var/www/deploybot/env_files/.env* ./
cp /var/www/deploybot/env_files/frontend/.env* ./frontend/
npm install
composer install

php artisan migrate
php artisan db:seed --class="ProfitToolsCushingSeeder"  # will not add duplicates
# in testing only: php artisan migrate:fresh --database=testing --drop-views
php artisan db:seed --class="LaratrustSeeder"  # will not add duplicates
php artisan cache:clear
php artisan horizon:terminate
# only if the above horizon command fails to start artisan: php artisan queue:restart

cd frontend
npm install
npm run build
exit

````

### Restart web server

````bash
sudo systemctl restart httpd.service php-fpm.service
sudo systemctl status httpd.service php-fpm.service

````


### All done

````bash
exit
````

