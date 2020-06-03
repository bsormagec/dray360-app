# Manually deploying to https://dev.dray360.com




### Make copy of local repo over to the hosted dev server

````bash
cd /home/pbnelson/repos/tcompanies/ordermaster
git checkout master
git pull --all
rsync -av ../ordermaster ocr-dev02:

````


### Move ordermaster folder over to web directory

````bash
ssh ocr-dev02

folder="pbn_$(date '+%Y%m%d')"
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
cp /var/www/deploybot/pbn_20200529/.env* ./
cp /var/www/deploybot/pbn_20200529/frontend/.env* ./frontend/
composer install
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