# Setup Dray360 Local Dev Workstation on Ubuntu

Instructions for setting up Dray360 local development workstation on Ubuntu 20.0.

These instructions should also work on earlier versions of Ubuntu, Linux Mint, and any other Debian-derived flavor of Linux.

With some modification these instructions should also suffice for macOS.





### Create environment-variables script

This script will be used repeatedly in the rest of the setup. Getting this setup right has to be the first step.

Create a file in your home directory, `~/tcvars.sh`, to export the following variables, like this:

*_Important! Modify pathnames as appropriate for your personal system._*


````bash
#!/bin/bash

# Set environment variables for local dev workstation
export GIT_FOLDER=/home/${USER}/repos/tcompanies  # just the root, not! the project folder, not! pbnelson!
export D3_VHOST=locald3
export D3_HOSTNAME_ALT=local.dray360.com
export D3_VHOST_IP=127.0.0.8 # pick another IP if this is in use already
export D3_DBNAME=d3db
export D3_DBUSER=d3user
export D3_DUMP=
export D3_REPO=dray360-app

# don't change this derivative variable
export D3_ROOT=${GIT_FOLDER}/${D3_REPO}

````

##### Test the above

If this fails, do not! go any further, stop and get this working.

````bash
source ~/tcvars.sh
cd ${GIT_FOLDER}
echo ${?}  # this should output "0" and if not, stop and fix your tcvars.sh and/or directory structure
echo ${D3_REPO} # this should output "dray360-app" to the console

````




### Configure etc/hosts file to contain site name

This entry in your /etc/hosts file is needed to access the local server

##### Draymaster
````bash
source ~/tcvars.sh
grep ${D3_VHOST_IP} /etc/hosts || echo "${D3_VHOST_IP} ${D3_VHOST} ${D3_HOSTNAME_ALT}" | sudo tee -a /etc/hosts

````






### Install GIT, and setup SSL access


````bash
sudo apt install git
````

If you don't already have github setup with your SSL certificates, follow these instructions.

https://help.github.com/en/github/authenticating-to-github/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent
https://help.github.com/en/github/authenticating-to-github/adding-a-new-ssh-key-to-your-github-account





### Install Node & NPM (using NVM)

We want version 10.23 of node, and 6.13 of NPM.

Follow directions on github to install, here: https://github.com/nvm-sh/nvm#installing-and-updating

````bash
# current directions, as of Jan 2021, this installs NVM
# curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.37.2/install.sh | bash
````

````bash
nvm install v10
nvm use v10
node -v # confirm version 10x
npm -v # confirm version 6x
````




### Install PHP 7.4 & FPM

````bash
sudo apt install php
sudo apt install php-fpm
sudo apt install php-mbstring php-xml php-bcmath php-gd php-zip
sudo apt install php-imagick php-curl php-xdebug php-mysql
sudo apt install php-sqlite3 php-bcmath
sudo apt install php-mysql # ubuntu?: sudo apt install php7.4-mysql

sudo systemctl enable php7.4-fpm.service
sudo systemctl restart php7.4-fpm.service

sudo a2enmod proxy_fcgi setenvif
sudo a2enconf php7.4-fpm
sudo phpenmod xdebug

````

Inspect php.ini file and verify it is correct:

````bash
less /etc/php/7.4/cli/php.ini
````

Inspect fpm's www.conf file and verify it is correct:

````bash
less /etc/php/7.4/fpm/pool.d/www.conf
````



#### Confirm `pcntl` is installed

The PHP extension `pcntl` is required to run _Laravel Horizon_. It's installed by default in Ubuntu and Mint. But if you are using another system, please search how to install it in your machine.

To confirm whether `pcntl` is installed, use this command.

````bash
php -i | grep pcntl  # should output: pcntl support => enabled


````



### Install Composer

Go to https://getcomposer.org/download and follow directions to install latest version.

To make this work everywhere on your computer, do this:

````bash
sudo mv ./composer.phar /usr/local/bin/composer
composer --version # as of Jan 2021, this is at v2.0.8. We had been running v1.10.1

````




### Install MySQL and restore the database


````bash
sudo apt install pv  # a helpful utility for monitoring status of long d/b restores
sudo apt install mysql-server-8.0  # as of Jan 2021 this is version 8.0.22 (but 8.0.17 is running on our hosted AWS/RDS d/b server)
sudo systemctl enable mysql
sudo systemctl restart mysql

````

##### Create a database

Warning: this will erase any existing local dray360 database

Note that this gives a password of 'secret', you may change this if you like.

````bash
source ~/tcvars.sh
sudo mysql --execute "
    drop database if exists ${D3_DBNAME};
    create database ${D3_DBNAME};
    drop database if exists ${D3_DBNAME}_test;
    create database ${D3_DBNAME}_test;
    create user if not exists '${D3_DBUSER}'@'%' identified with mysql_native_password by 'secret';
    grant event, show view, create temporary tables, create view, create routine, alter routine, trigger, references, select, insert, update, delete, create, drop, alter, execute on ${D3_DBNAME}.* to '${D3_DBUSER}'@'%';
    grant event, show view, create temporary tables, create view, create routine, alter routine, trigger, references, select, insert, update, delete, create, drop, alter, execute on ${D3_DBNAME}_test.* to '${D3_DBUSER}'@'%';
"
sudo mysql --execute "show databases"

````


##### Restore database from a backup

Ask Peter Nelson for a current backup copy of the dev database

````bash
source ~/tcvars.sh
dumpfilename='~/Downloads/ocr_dev-dump-20200521-132513.sql' # or whatever the latest SQL dump filename is
pv ${dumpfilename} | sudo mysql ${D3_DBNAME}

````

### Install Redis

```bash
sudo apt install redis-server
```

Next, open up the Redis configuration file with your preferred text editor:

```bash
sudo nano /etc/redis/redis.conf
```

Inside the file, find the `supervised no` directive which allows you to declare an init system to manage Redis as a service. Since you are running Ubuntu, which uses the systemd init system, change its value to `supervised systemd`

Then, restart the Redis service to reflect the changes you made to the configuration file:

```bash
sudo systemctl enable redis-server.service
sudo systemctl restart redis-server.service
sudo systemctl status redis-server.service

```

Then run:
```bash
redis-cli ping # It should echo output "PONG"
```


### Clone Github repository, install dependencies and build site

For these steps you'll need to get a few things from Peter Nelson.


##### Clone the repo

If this git clone fails, you will need to get your SSL keys setup in github. Peter Nelson can help if needed.

````bash
source ~/tcvars.sh
git clone git@github.com:tcompanies/dray360-app.git ${D3_ROOT}
cd ${D3_ROOT} # this should take you into the project source-code folder

````


##### Edit various `env` files

````bash
source ~/tcvars.sh
code ${D3_ROOT}/.env
````

````text
<get secret stuff from Peter Nelson>
````


````bash
cat <<EOF > ${D3_ROOT}/frontend/.env.development
VUE_APP_APP_URL=http://localhost:8080
APP_URL=http://${D3_HOSTNAME_ALT}
EOF

````


````bash
cat <<EOF > ${D3_ROOT}/frontend/.env.production
VUE_APP_APP_URL=http://${D3_HOSTNAME_ALT}
EOF

````


````bash
cat <<EOF > ${D3_ROOT}/frontend/.env.test
VUE_APP_APP_URL=http://localhost:8080
APP_URL=http://${D3_HOSTNAME_ALT}

VUE_APP_TEST_USER_NAME=testuser
VUE_APP_TEST_USER_EMAIL=peter+test13@peternelson.com
VUE_APP_TEST_USER_PASSWORD=mongomongo
EOF

````




##### Run composer install

Get Nova username and password from Peter Nelson

````bash
source ~/tcvars.sh
cd $D3_ROOT
composer install
# when prompted for Nova username/password, ask Peter Nelson

````

###### Troubleshooting

If for some reason you get the error `laravel/horizon dev-master requires ext-pcntl * -> the requested PHP extension pcntl is missing from your system.`. Try running this instead:

```bash
composer install --ignore-platform-reqs ext-pcntl ext-posix  # ONLY IF PCNTL IS MISSING
```



##### Run NPM install and build

````bash
source ~/tcvars.sh
cd $D3_ROOT/frontend
nvm use v10
npm install
npm run build

````




### Install and Setup Apache


##### Install Apache server and required modules

````bash
sudo apt install apache2
sudo apt install libapache2-mod-php7.4

sudo a2enmod php7.4
sudo a2enmod rewrite
sudo a2enmod proxy_fcgi
sudo a2enmod proxy_http
sudo a2enmod setenvif
sudo a2enmod ssl

sudo a2enconf php7.4-fpm

sudo systemctl enable apache2.service
sudo systemctl restart apache2.service
sudo systemctl status apache2.service

# confirm the above modules are listed by this command
apachectl -M | egrep -i "proxy|write|php|set"

````


##### Configure Apache server

Link the new project folder to the apache folder

````bash
source ~/tcvars.sh

sudo ln -sf ${D3_ROOT}/public /var/www/html/${D3_VHOST}
ll /var/www/html/${D3_VHOST}/ # confirm this looks good, should list index.php, favicon.ico, etc.

````

Make sure the above command worked okay, and lists the project files.

Now, add the vhosts section for the new project to the apache config file.

````bash
# add global servername attribute to stop annoying warning messages
echo "ServerName localhost" | sudo tee -a /etc/apache2/apache2.conf

# helper function to add virtual host section for hostname/localip
add_vhost_config() {
    VHOST=${1}
    VHOST_IP=${2}

    # add virtual host section
    echo "
# setup the ${VHOST} virtual host for insecure (http)
<VirtualHost ${VHOST_IP}:80>
    DocumentRoot "/var/www/html/${VHOST}"
    DirectoryIndex index.php
    ServerName ${VHOST}
    <Directory "/var/www/html/${VHOST}">
        Options FollowSymLinks
        Options All
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

" | sudo tee /etc/apache2/sites-available/${VHOST}.conf
sudo ln -s /etc/apache2/sites-available/${VHOST}.conf /etc/apache2/sites-enabled
}

source ~/tcvars.sh
sudo systemctl stop apache2
add_vhost_config ${D3_VHOST} ${D3_VHOST_IP}
sudo systemctl restart apache2
sudo systemctl status apache2.service

````






### Fix permissions for apache

Make apache/www-data a member of each other's group

````bash
source ~/tcvars.sh

sudo usermod -a -G $USER www-data  # make Apache's www-data user a member of your group
id www-data

sudo usermod -a -G www-data $USER  # make your user a member of Apache's www-data group
id $USER

touch ${D3_ROOT}/storage/logs/laravel.log # make a default log file
sudo chmod g+w -R ${D3_ROOT}/storage # give everything in storage folder group-write permissions

sudo systemctl restart apache2
sudo systemctl status apache2.service

````




### Run the app locally

````bash
source ~/tcvars.sh
cd ${D3_ROOT}/frontend
npm run serve

````

Now navigate to http://localhost:8080/
* email: peter+test13@peternelson.com
* Password: <ask Peter Nelson for help>




## Troubleshooting



#### Troubleshooting: View error logs in real time

````bash
source ~/tcvars.sh
sudo tail \
  -f /var/log/apache2/error.log \
  -f /var/log/php7.4-fpm.log \
  -f ${D3_ROOT}/storage/logs/laravel.log

````



#### Troubleshoting: Create and seed a blank database

It should be possible to make a completely empty, fresh database with these commands.

````bash
source ~/tcvars.sh
cd ${D3_ROOT}
php artisan db:createmysql
php artisan db:seed

````



#### Troubleshooting: No audio on Linux Mint 19, Dell Vostro 5490


`sudo nano /etc/default/grub`
Change `GRUB_CMDLINE_LINUX_DEFAULT="quiet splash"` to `GRUB_CMDLINE_LINUX_DEFAULT="quiet splash snd_hda_intel.dmic_detect=0"`
Save and exit nano editor
`sudo update-grub; sudo reboot`




#### Troubleshooting: remove old version of PHP, upgrade to 7.4

Remove old PHP versions

````bash
dpkg --get-selections | grep -i php
sudo apt remove --purge 'php*'
````

Install newer version of PHP

```bash
sudo apt-get update
sudo apt-get -y install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo add-apt-repository ppa:ondrej/apache2
sudo apt-get update

sudo apt-get install -y php7.4
sudo apt-get install -y php7.4-fpm
sudo apt-get install -y php7.4-mbstring php7.4-xml php7.4-bcmath php7.4-gd php7.4-zip
sudo apt-get install -y php7.4-imagick php7.4-curl php7.4-xdebug php7.4-mysql
sudo apt-get install -y php7.4-sqlite3 php7.4-bcmath
sudo apt-get install -y php7.4-mysql

```

Restart apache and php-fpm

```bash
sudo systemctl restart apache2.service php7.4-fpm.service
sudo systemctl status apache2.service php7.4-fpm.service
```



#### Troubleshooting: Install MySQL 8.0 on Linux Mint 19 / Ubuntu 18

````bash
wget -c https://repo.mysql.com//mysql-apt-config_0.8.13-1_all.deb
sudo dpkg -i mysql-apt-config_0.8.13-1_all.deb

````

1. Select "bionic"
1. Select "mysql server"
1. Select "ok"

````bash
sudo apt upgrade
sudo apt install mysql-server
````

1. Enter a root password
1. Select legacy password authentication



#### Troubleshooting: Install Visual Studio Code on Linux Mint 19

````bash
curl https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor > microsoft.gpg
sudo install -o root -g root -m 644 microsoft.gpg /etc/apt/trusted.gpg.d/
sudo sh -c 'echo "deb [arch=amd64] https://packages.microsoft.com/repos/vscode stable main" > /etc/apt/sources.list.d/vscode.list'

sudo apt-get update
sudo apt-get install apt-transport-https
sudo apt-get install code
````




#### Troubleshooting: Enable xdebug to work with vscode

````bash
grep remote_autostart /etc/php/7.4/mods-available/xdebug.ini || echo "xdebug.remote_autostart=true
xdebug.remote_enable = 1" | sudo tee -a /etc/php/7.4/mods-available/xdebug.ini
sudo phpenmod xdebug
sudo systemctl restart apache2.service php7.4-fpm.service
sudo systemctl status apache2.service php7.4-fpm.service

````



#### Troubleshooting: Running "backend" laravel tests

Github Actions automatically runs these tests when a Pull Request is created. If you get an error there it may be more convenient to to troubleshooting locally. Here are the commands to run the API backend tests.

To run all tests:

````bash
composer run migrate-test  # usually only need to run this command once
./vendor/bin/phpunit  # runs all tests

````

To run a single test:

````bash
./vendor/bin/phpunit --filter it_should_queue_a_job_for_each_address_from_the_endpoint
# which is equivalent to: ./vendor/bin/phpunit --filter=ImportProfitToolsAddressesTest

````




#### Troubleshooting: Test a controller in your local dev workstation

Clean up the local test database 

````bash
composer run migrate-test
````
Open the controller source code, for example: `./app/Http/Controllers/Api/OrderStatusHistoryController.php`

put `dd($whatever)` at the line where you want a breakpoint.

Run the unit test from the command line, like this

````bash
./vendor/bin/phpunit --filter=OrderStatusHistoryTest
````



#### Troubleshooting: Apache Error `Symbolic link not allowed or link target not accessible`

The `www-data` user must be able to read the directory where your repo is stored.

````bash
source ~/tcvars.sh
sudo chown www-data:www-data /var/www/html
sudo chmod g+r /home/${USER}
sudo chmod g+x /home/${USER}
sudo chmod -R g+w ${D3_ROOT}/storage

````



#### Troubleshooting: App Exception, `SQLSTATE[HY000] [2002] Connection refused (SQL: select * from users where email = peter+test13@peternelson.com and users.deleted_at is null limit 1)`

Double check that your `.env` file has the right username and password for the database




#### Troubleshooting: Local MySQL Database Error `SQLSTATE[HY000]: General error: 1419 You do not have the SUPER privilege and binary logging is enabled (you *might* want to use the less safe log_bin_trust_function_creators variable)`

This can be encountered when creating local test database for phpunit.

To enable "log_bin_trust_function_creators" and solve the above error, run this code:

````bash
grep log_bin_trust_function_creators /etc/mysql/my.cnf || echo "[mysqld]
log_bin_trust_function_creators = 1" | sudo tee -a /etc/mysql/my.cnf
sudo systemctl restart mysql.service; sudo systemctl status mysql.service
sudo mysql --execute "show variables like 'log_bin_trust_function_creators'"

````
