<?php

use App\Models\TMSProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConfigurationToTmsProvider extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_tms_providers', function (Blueprint $table) {
            $table->json('configuration')->nullable();
        });

        TMSProvider::where('name', TMSProvider::COMPCARE)
            ->update([
                'configuration' => [
                    'shipment_directions' => [
                        ['tmscode' => 'I', 'd3code' => 'import', 'display' => 'inbound'],
                        ['tmscode' => 'O', 'd3code' => 'export', 'display' => 'outbound'],
                        ['tmscode' => 'T', 'd3code' => 'truckload', 'display' => 'truckload'],
                        ['tmscode' => 'L', 'd3code' => 'oneway', 'display' => 'landbridge'],
                    ],
                ],
            ]);
        TMSProvider::where('name', TMSProvider::PROFIT_TOOLS)
            ->update([
                'configuration' => [
                    'shipment_directions' => [
                        ['tmscode' => 'I', 'd3code' => 'import', 'display' => 'import'],
                        ['tmscode' => 'E', 'd3code' => 'export', 'display' => 'export'],
                        ['tmscode' => 'O', 'd3code' => 'oneway', 'display' => 'oneway'],
                        ['tmscode' => 'O', 'd3code' => 'crosstown', 'display' => 'crosstown'],
                    ],
                ],
            ]);
        TMSProvider::where('name', TMSProvider::CARGOWISE)
            ->update([
                'configuration' => [
                    'shipment_directions' => [
                        ['tmscode' => 'IMP', 'd3code' => 'import', 'display' => 'import'],
                        ['tmscode' => 'EXP', 'd3code' => 'export', 'display' => 'export'],
                        ['tmscode' => 'ONE', 'd3code' => 'oneway', 'display' => 'oneway'],
                        ['tmscode' => 'ONE', 'd3code' => 'crosstown', 'display' => 'crosstown'],
                    ],
                ],
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_tms_providers', function (Blueprint $table) {
            $table->dropColumnIfExists('configuration');
        });
    }
}
