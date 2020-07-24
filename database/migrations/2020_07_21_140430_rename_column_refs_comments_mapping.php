<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnRefsCommentsMapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_companies', function (Blueprint $table) {
            $table->renameColumn('refs_comments_mapping', 'refs_custom_mapping');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('t_companies', 'refs_custom_mapping')) {
            Schema::table('t_companies', function (Blueprint $table) {
                $table->renameColumn('refs_custom_mapping', 'refs_comments_mapping');
            });
        }
    }
}
