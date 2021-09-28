<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddDeletedatToVAddresses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // v_addresses view
        DB::statement("
            create or replace view v_addresses as
            select 
               a.id as address_id
              ,c.id as company_id 
              ,j.company_address_tms_code as tms_code
              ,least(a.updated_at, j.updated_at) as address_created_at
              ,greatest(a.updated_at, j.updated_at) as address_updated_at
              ,nullif(least(coalesce(a.deleted_at, '9999-12-31'), coalesce(j.deleted_at, '9999-12-31')), '9999-12-31') as address_deleted_at
              ,c.name as company_name
              ,tms.name as tms_name

              ,coalesce(address_line_1, '') as address_line_1
              ,coalesce(address_line_2, '') as address_line_2
              ,coalesce(city, '') as city
              ,coalesce(state, '') as state
              ,coalesce(postal_code, '') as postal_code
              ,coalesce(country, '') as country
              ,coalesce(location_name, '') as location_name
              ,coalesce(location_phone, '') as location_phone
              ,coalesce(is_billable, 0) as is_billable
              ,coalesce(is_terminal, 0) as is_terminal
              ,is_cc_payor
              ,is_cc_customer
              ,is_cc_ssrr
              ,is_cc_carrier
              ,is_cc_consignee
              ,is_cc_driver
              ,is_cc_shipper
              ,is_cc_vendor
              ,coalesce(address_concatenated_text, '') as address_concatenated_text

            from t_company_address_tms_code as j
            join t_companies as c on j.t_company_id = c.id
            join t_addresses as a on j.t_address_id = a.id
            join t_tms_providers as tms on c.default_tms_provider_id = tms.id
            where true
              and c.deleted_at is null
              and tms.deleted_at is null
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('drop view v_addresses');
    }
}
