<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteUnusedTablesAndColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('pt_companies');
        Schema::dropIfExists('pt_companies_latlong');
        Schema::dropIfExists('pt_contacts_base');
        Schema::dropIfExists('pt_contacts_newship');
        Schema::dropIfExists('pt_disp_items');
        Schema::dropIfExists('pt_disp_ship');
        Schema::dropIfExists('pt_equipment_lease_type');
        Schema::dropIfExists('t_accounts');
        Schema::dropIfExists('t_canonical_addresses');
        Schema::dropIfExists('t_canonical_address_matches');
        Schema::dropIfExists('t_company_ocrvariant_accessorial_mappings');
        Schema::dropIfExists('t_contacts');
        Schema::dropIfExists('t_order_accessorials');
        Schema::dropIfExists('t_terminals');

        DB::statement("drop view if exists
            v_all_incomplete_jobs,
            v_status_intake_accepted_detail,
            v_status_intake_rejected_detail,
            v_status_intake_started_detail,
            v_status_ocr_completed_detail,
            v_status_ocr_post_processing_complete_detail,
            v_status_ocr_post_processing_error_detail,
            v_status_ocr_waiting_detail,
            v_status_process_ocr_output_file_complete_detail,
            v_status_process_ocr_output_file_error_detail,
            v_status_summary
        ");

        Schema::table('t_order_line_items', function (Blueprint $table) {
            $table->dropColumnIfExists('unit_of_measure');
            $table->dropColumnIfExists('haz_contact_name');
            $table->dropColumnIfExists('haz_phone');
            $table->dropColumnIfExists('haz_un_code');
            $table->dropColumnIfExists('haz_un_name');
            $table->dropColumnIfExists('haz_class');
            $table->dropColumnIfExists('haz_qualifier');
            $table->dropColumnIfExists('haz_description');
            $table->dropColumnIfExists('haz_imdg_page_number');
            $table->dropColumnIfExists('packaging_group');
            $table->dropColumnIfExists('haz_flashpoint_temp');
            $table->dropColumnIfExists('haz_flashpoint_temp_uom');
        });

        Schema::table('t_order_address_event', function (Blueprint $table) {
            $table->dropColumnIfExists('address_schedule_description');
            $table->dropColumnIfExists('call_for_appointment');
            $table->dropColumnIfExists('delivery_window_from_localtime');
            $table->dropColumnIfExists('delivery_window_to_localtime');
            $table->dropColumnIfExists('delivery_instructions');
        });

        Schema::table('t_orders', function (Blueprint $table) {
            $table->dropColumnIfExists('yard_pre_pull');
            $table->dropColumnIfExists('has_chassis');
            $table->dropColumnIfExists('rate_quote_number');
            $table->dropColumnIfExists('estimated_arrival_utc');
            $table->dropColumnIfExists('last_free_date_utc');
            $table->dropColumnIfExists('cancelled_datetime');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No way Jose
    }
}
