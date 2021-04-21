<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->dropIndex(['auditable_type', 'auditable_id']);
            $table->index('auditable_id');
            $table->index('auditable_type');
            $table->index(['auditable_id', 'auditable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->dropIndex(['auditable_id', 'auditable_type']);
            $table->dropIndex(['auditable_id']);
            $table->dropIndex(['auditable_type']);
            $table->index(['auditable_type', 'auditable_id']);
        });
    }
}
