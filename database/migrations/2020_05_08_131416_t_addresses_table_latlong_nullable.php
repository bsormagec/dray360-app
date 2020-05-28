
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TAddressesTableLatlongNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_addresses', function (Blueprint $table) {
            $table->decimal('latitude', 12, 8)->nullable()->change();
            $table->decimal('longitude', 12, 8)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_accounts', function (Blueprint $table) {
            $table->dropIndex('name');
        });
    }
}
