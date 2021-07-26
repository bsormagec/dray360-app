<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameContainerDictidToItgContainer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->dropForeign(['container_dictid']);

            $table->renameColumn('container_dictid', 'itgcontainer_dictid');
            $table->renameColumn('container_dictid_verified', 'itgcontainer_dictid_verified');

            $table->foreign('itgcontainer_dictid')->references('id')->on('t_dictionary_items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->dropForeign(['itgcontainer_dictid']);

            $table->renameColumn('itgcontainer_dictid', 'container_dictid');
            $table->renameColumn('itgcontainer_dictid_verified', 'container_dictid_verified');

            $table->foreign('container_dictid')->references('id')->on('t_dictionary_items');
        });
    }
}
