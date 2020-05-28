<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

/**
 * Create a new database for
 */
class DbCreateMySQLCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "
        db:createmysql
            {dbname : database name to be created}
            {username : database non-root username}
            {password : new db username password (if username does not exist already)}
        ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Create a new app database in mysql.
                                Example: php artisan migrate:createmysql omdb omuser secret
                                Because this command must be run as mysql root user
                                on no database, the local `.env` file must specify:
                                    DB_DATABASE=
                                    DB_USERNAME=root
                                    DB_PASSWORD=<mysql root password>";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $dbname = $this->argument('dbname');
            $username = $this->argument('username');
            $password = $this->argument('password');
            $connection = 'mysql'; // this only works with mysql
            $this->info("Attempting to create database '$dbname' for user '$username'/'password' on connection '$connection'");

            try {
                $hasDb = DB::connection($connection)->select("select schema_name from information_schema.schemata where schema_name = '$dbname' ");

                if (!empty($hasDb)) {
                    $this->error("Database $dbname already exists for $connection connection");
                } else {
                    // finally, make the new database (and user)
                    db::connection($connection)->statement("drop database if exists $dbname");
                    db::connection($connection)->statement("create database if not exists $dbname");
                    DB::connection($connection)->statement("create user if not exists 'omuser'@'%' identified with mysql_native_password by '$password'");
                    DB::connection($connection)->statement("grant create view, create routine, alter routine, trigger, references, select, insert, update, delete, create, drop, alter, execute on $dbname.* to '$username'@'%'");
                    DB::connection($connection)->statement("update mysql.user set Super_Priv='Y' where user='omuser'");

                    // all done
                    $this->info("Database '$dbname' created for '$connection' connection");
                }
            } catch (\Exception $e1) {
                $this->error($e1->getMessage());
            }
        } catch (\Exception $e2) {
            $this->error($e2->getMessage());
        }
    }
}
