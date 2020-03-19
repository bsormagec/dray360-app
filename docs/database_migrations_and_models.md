# OrderMaster

General setup instructions for getting local dev environment running





### Generate a new `.env` file and APP_KEY

````bash
cp .env.example .env
php artisan key:generate
````




### Setup a fresh database

````bash

cat << EOF > ~/tcvars2.sh
#!/bin/bash

# Set environment variables for local dev workstation

# common variables
export GIT_FOLDER=/home/pbnelson/repos/tcompanies
export USERIFY_USERNAME=pnelson # or aadrian, abryden, rhayle, chorton, etc.

# ordermaster variables
export OM_VHOST=localom
export OM_HOSTNAME_ALT=local.ordermaster.com
export OM_VHOST_IP=127.0.0.8 # pick another IP if this is in use already
export OM_DBNAME=omdb
export OM_DBUSER=omuser
export OM_DUMP=/home/pbnelson/Downloads/notready.sql
export OM_REPO=ordermaster
export OM_ROOT=${GIT_FOLDER}/${OM_REPO} # don't change this derivative variable

EOF
source ~/tcvars2.sh


function recreateDatabase() {
    DBNAME=${1}
    DUMPFILE=${2}
    USERNAME=${3}
    USERPW=${4}

    if [ -f "${DUMPFILE}" ]; then
        sudo mysql --execute="drop database if exists ${DBNAME};"
        sudo mysql --execute="create database ${DBNAME};"
        # time sudo mysql ${DBNAME} <${DUMPFILE}
        # the pv command shows a progress bar
        pv ${DUMPFILE} | sudo mysql ${DBNAME}
        sudo mysql --execute="show databases;"
        sudo mysql --execute="
            drop user if exists ${USERNAME};
            create user '${USERNAME}'@'%' identified with mysql_native_password by '${USERPW}';
            grant create routine, alter routine, references, select, insert, update, delete, create, drop, alter, execute on ${DBNAME}.* to '${USERNAME}'@'%';
        "
    else
        echo "Dumpfile '${DUMPFILE}' not found. Edit ~/tcvars2.sh and set the XYZ_DUMP variable"
    fi
}

source ~/tcvars2.sh
echo "" >/tmp/nothing.sql
source ~/tcvars2.sh
recreateDatabase ${OM_DBNAME} /tmp/nothing.sql ${OM_DBUSER} "secret" # password set to "secret"

````



### Update local `.env` file

Change these settings, as appropriate

````env
APP_URL=https://local.ordermaster.com

DB_DATABASE=omdb
DB_USERNAME=omuser
DB_PASSWORD=secret

MAIL_DRIVER=log
````



### Finish database setup

# run migrations
php artisan migrate




### seed database from PBN local copy
````bash
mysql --password=secret --database=omdb <ocr_dev-dump-20200225-155454.sql
mysql --password=secret --database=omdb <ocr_dev_PT-dump-20200225-154623.sql

````









#### (ALREADY DONE AND MERGED-IN) Generate model templates


Generate templates

````bash
php artisan make:model Models/Account --migration
php artisan make:model Models/Address --migration
php artisan make:model Models/CanonicalAddress --migration
php artisan make:model Models/CanonicalAddressMatch --migration
php artisan make:model Models/Company --migration
php artisan make:model Models/CompanyAddressTMSCode --migration
php artisan make:model Models/Contact --migration
php artisan make:model Models/Order --migration
php artisan make:model Models/OrderAddressEvent --migration
php artisan make:model Models/OrderLineItem --migration
php artisan make:model Models/TMSProvider --migration

````


Overwrite model templates from POC repo

````bash
cp ~/repos/tcompanies/poc-ordermaster/app/Models/Account.php ./app/Models/
cp ~/repos/tcompanies/poc-ordermaster/app/Models/Address.php ./app/Models/
cp ~/repos/tcompanies/poc-ordermaster/app/Models/CanonicalAddress.php ./app/Models/
cp ~/repos/tcompanies/poc-ordermaster/app/Models/CanonicalAddressMatch.php ./app/Models/
cp ~/repos/tcompanies/poc-ordermaster/app/Models/Company.php ./app/Models/
cp ~/repos/tcompanies/poc-ordermaster/app/Models/CompanyAddressTMSCode.php ./app/Models/
cp ~/repos/tcompanies/poc-ordermaster/app/Models/Contact.php ./app/Models/
cp ~/repos/tcompanies/poc-ordermaster/app/Models/Order.php ./app/Models/
cp ~/repos/tcompanies/poc-ordermaster/app/Models/OrderAddressEvent.php ./app/Models/
cp ~/repos/tcompanies/poc-ordermaster/app/Models/OrderLineItem.php ./app/Models/
cp ~/repos/tcompanies/poc-ordermaster/app/Models/TMSProvider.php ./app/Models/

````

Remove new migration templates

````bash
rm ./database/migrations/2020_03_11_150832_create_addresses_table.php
rm ./database/migrations/2020_03_11_150833_create_companies_table.php
rm ./database/migrations/2020_03_11_150834_create_contacts_table.php
rm ./database/migrations/2020_03_11_150835_create_order_line_items_table.php
rm ./database/migrations/2020_03_11_150836_create_canonical_addresses_table.php
rm ./database/migrations/2020_03_11_150838_create_canonical_address_matches_table.php
rm ./database/migrations/2020_03_11_150839_create_company_address_t_m_s_codes_table.php
rm ./database/migrations/2020_03_11_150840_create_order_address_events_table.php
rm ./database/migrations/2020_03_11_150842_create_t_m_s_providers_table.php
rm ./database/migrations/2020_03_11_151401_create_accounts_table.php

````

Copy migration templates from POC repo

````bash
cp ~/repos/tcompanies/poc-ordermaster/database/migrations/2020_03_03_231123_create_t_orders_table.php ./database/migrations/
cp ~/repos/tcompanies/poc-ordermaster/database/migrations/2020_03_04_211747_create_t_addresses_table.php ./database/migrations/
cp ~/repos/tcompanies/poc-ordermaster/database/migrations/2020_03_04_213942_create_t_companies_table.php ./database/migrations/
cp ~/repos/tcompanies/poc-ordermaster/database/migrations/2020_03_04_213943_create_t_contacts_table.php ./database/migrations/
cp ~/repos/tcompanies/poc-ordermaster/database/migrations/2020_03_04_214139_create_t_order_line_items_table.php ./database/migrations/
cp ~/repos/tcompanies/poc-ordermaster/database/migrations/2020_03_04_214432_create_t_canonical_addresses_table.php ./database/migrations/
cp ~/repos/tcompanies/poc-ordermaster/database/migrations/2020_03_04_214433_create_t_canonical_address_matches_table.php ./database/migrations/
cp ~/repos/tcompanies/poc-ordermaster/database/migrations/2020_03_04_214438_create_t_company_address_tms_code_table.php ./database/migrations/
cp ~/repos/tcompanies/poc-ordermaster/database/migrations/2020_03_04_214445_create_t_order_address_events_table.php ./database/migrations/
cp ~/repos/tcompanies/poc-ordermaster/database/migrations/2020_03_04_214451_create_t_tms_providers_table.php ./database/migrations/
cp ~/repos/tcompanies/poc-ordermaster/database/migrations/2020_03_05_231254_create_t_accounts_table.php ./database/migrations/

````

