# OrderMaster



#### Setup a fresh database

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
export OM_DBNAME=om
export OM_DBUSER=omuser
export OM_DUMP=/home/pbnelson/Downloads/notready.sql
export OM_REPO=poc-ordermaster
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
recreateDatabase ${OM_DBNAME} /tmp/nothing.sql ${OM_DBUSER} "secret" # replace secret with your real password here if you want something else, obviously

mysql --password=secret --database=omdb <ocr_dev-dump-20200225-155454.sql
mysql --password=secret --database=omdb <ocr_dev_PT-dump-20200225-154623.sql

````




#### Basic Usage:

Point your web server at the thisrepo/public/. For detailed directions, see draymaster setup directions, here: https://github.com/tcompanies/draymaster/blob/develop/docs/Dev_Workstation_Setup_PH_DM_ITG.md

```bash
# in a nutshell
sudo ln -s ~/repos/tcompanies/poc-ordermaster/public /var/www/html
cp ~/repos/tcompanies/poc-ordermaster/.env.example.local ~/repos/tcompanies/poc-ordermaster/.env

```

Visit these pages:

0. https://local.ordermaster.com/logout
0. https://local.ordermaster.com/register

After registering, you need to click the link found in the "email", which is just text logged in laravel.log (unless you changed that setting in the .env file).

````bash
tail -n 100 ~/public/repos/storage/logs/laravel.log
````

Now you can visit these other pages:

0. https://local.ordermaster.com/login
0. https://local.ordermaster.com/home
0. https://local.ordermaster.com/orders
0. https://local.ordermaster.com/addresses
0. https://local.ordermaster.com/companies
0. etc...



#### Code Linting  (before git push)

The grumphp linter will prevent any commits if it finds errors. To preview this, run

````bash
./vendor/bin/grumphp run
````

To skip those checks and commit anything

````bash
git commit -n
````





#### install

Source for INFYOM generator:

https://github.com/InfyOmLabs/laravel-generator
https://github.com/infyomlabs/coreui-templates
https://github.com/coreui/coreui-free-vue-admin-template

Documentation:

https://labs.infyom.com/laravelgenerator/docs/6.0/generator-options



#### publishing infyom pages (NOT NEEDED, ALREADY DONE)

````
php artisan vendor:publish  # answer: [9 ] Provider: InfyOm\Generator\InfyOmGeneratorServiceProvider
php artisan infyom:publish
php artisan infyom.publish:layout

````

#### generate models (NOT NEEDED, ALREADY DONE)



To generate model/migration from table (e.g. Order object from t_orders table)

```bash

function buildModelAndMigration() {
    exit 1 # this will overwrite existing models/migrations. don't do this.
    MODEL_NAME=${1}
    TABLE_NAME=${2}
    yes no | php artisan infyom:scaffold ${MODEL_NAME} --fromTable --tableName=${TABLE_NAME} --save
    yes no | php artisan infyom:migration ${MODEL_NAME} --tableName=${TABLE_NAME} --fieldsFile ./resources/model_schemas/${MODEL_NAME}.json --skip=routes,Model
}

buildModelAndMigration Order t_orders
buildModelAndMigration Address t_addresses
buildModelAndMigration Company t_companies
buildModelAndMigration Contact t_contacts
buildModelAndMigration OrderLineItem t_order_line_items
buildModelAndMigration CanonicalAddress t_canonical_addresses
buildModelAndMigration CanonicalAddressMatch t_canonical_address_matches
buildModelAndMigration CompanyAddressTMSCode t_company_address_tms_code
buildModelAndMigration OrderAddressEvent t_order_address_events
buildModelAndMigration TMSProvider t_tms_providers
#   n/a   t_job_latest_state
#   n/a   t_job_state_changes

```

Now go into each migration file and add the following (type) of code to identify the table foreign keys. Note that the order of table creation matters, as foreign keys cannot be created until the target table exists.

````php
// add foreign key constraints
$table->foreign('t_address_id')->references('id')->on('t_addresses');  // change field/tablename as appropriate
````



#### To build a VUE page - NOT IMPLEMENTED! DO NOT TRY!

```
./artisan infyom:vuejs Order --fieldsFile ./resources/model_schemas/Order.json --skip=routes,migration,Model
```


#### To build API. Meh. Works so-so.

```
./artisan infyom:api Order --fieldsFile ./resources/model_schemas/Order.json --tableName=t_orders --skip=routes,migration,Model

php artisan route:list

```


