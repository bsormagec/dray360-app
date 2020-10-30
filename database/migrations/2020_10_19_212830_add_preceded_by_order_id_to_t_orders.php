<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrecededByOrderIdToTOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('preceded_by_order_id')->nullable()->index();
            $table->unsignedBigInteger('succeded_by_order_id')->nullable()->index();
            $table->datetime('tms_submission_datetime')->nullable();
            $table->datetime('tms_cancelled_datetime')->nullable();
            $table->datetime('cancelled_datetime')->nullable();

            $table->foreign('preceded_by_order_id')->references('id')->on('t_orders');
            $table->foreign('succeded_by_order_id')->references('id')->on('t_orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropIfExists('t_orders', 'preceded_by_order_id', 't_orders_preceded_by_order_id_foreign');
        $this->dropIfExists('t_orders', 'succeded_by_order_id', 't_orders_succeded_by_order_id_foreign');
        $this->dropIfExists('t_orders', 'tms_submission_datetime');
        $this->dropIfExists('t_orders', 'tms_cancelled_datetime');
        $this->dropIfExists('t_orders', 'cancelled_datetime');
    }

    /**
     * Drop column only if it exists
     *
     * @param string $tableName  table name
     * @param string $columnName column name
     *
     * @return void
     */
    public function dropIfExists($tableName, $columnName)
    {
        if (Schema::hasColumn($tableName, $columnName)) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName, $columnName) {
                // first check for and delete foreign key
                $foreignKeys = $this->listTableForeignKeys($tableName);
                $foreignName = $tableName.'_'.$columnName.'_foreign';
                if (in_array($foreignName, $foreignKeys)) {
                    $table->dropForeign($foreignName);
                }
                // now remove the column
                $table->dropColumn($columnName);
            });
        }
    }

    /**
     * Return list of foreign keys for a table
     *
     * @param string $tableName table name
     *
     * @return array
     */
    public function listTableForeignKeys($tableName)
    {
        $foreignKeys = Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->listTableForeignKeys($tableName);
        return array_map(function ($key) {
            return $key->getName();
        }, $foreignKeys);
    }
}
