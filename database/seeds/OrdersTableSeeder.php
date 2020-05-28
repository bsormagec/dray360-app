<?php

// usage: php artisan db:seed --class=OrdersTableSeeder

// Note, if you get error about column port_ramp_of_origon not existing, run this in mysql:
//   alter table t_orders change port_ramp_of_origon port_ramp_of_origin varchar(64);


use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OrdersTableSeeder extends Seeder
{
    // class constants

    // define how many to seed
    const NUM_ORDERS_TO_CREATE = 10;
    const PCT_ORDERS_INTAKE_REJECTED = 15;

    // lists of sample data values
    const UOM_PACKAGE_LIST = ['BAG','BOX','LOT','CAS', 'CRT','TU','ROL','PAD','PAL','FT']; // https://www.doa.la.gov/osp/Vendorcenter/publications/unitofmeasurecodes.pdf
    const UOM_WEIGHT_LIST = ['TON','LBS','KGS'];
    const DIRECTION_LIST = ['Export','Import','Domestic'];
    const EQUIPMENT_LIST = ['GP-General Purpose', 'HQ-High Cube', 'CC-Car Carrier', 'DD-Double Door'];
    const DESTINATION_LIST = ['International', 'Domestic'];
    const EQUIPMENT_TYPE_LIST = ['Container', 'Trailer'];
    const UNIT_NUMBER_LIST = ['ACMU8009943','HJCU8281988','CSQU3054383','TOLU4734787','LSCU1077379','MSKU2666542','NYKU3086856','BICU1234565'];
    const EQUIPMENT_SIZE_LIST = ['20 ft', '40 ft', '45 ft', '48ft'];
    const OWNER_OR_SS_COMPANY_LIST = ['ACL','Antillean Lines','APL/CMA-CGM','Atlantic RO-Ro','Australia National Line','Bahri / National Shipping Company of Saudi Arabia','Bermuda International Shipping Ltd','BMC Line Shipping LLC','CCNI','Cheng Lie Navigation Co.,Ltd','Dole Ocean Cargo Express','Dongjin Shipping','Emirates Shipping Line','Evergreen Line','Frontier Liner Services'];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // load needed faker providers

        // create orders
        $numCreated = 0;
        while ($numCreated < self::NUM_ORDERS_TO_CREATE) {
            $this->seedOrder();
            $numCreated++;
        }
    }

    /**
     * Create an Order
     *
     * Make an order having a related OCR request, and order line items, etc.
     *
     * @return void
     */
    public function seedOrder()
    {
        $orderId = $this->seedOrderWithOcrRequestId($this->createOCRJob());
        $this->seedOrderLineItem_nonHazardous($orderId);
    }

    /**
     * Create an Order given an OCR request id
     *
     * Make an order having a related OCR request, and order line items, etc.
     *
     * @param string $requestId a UUID representing the OCR request id
     * @return void
     */
    public function seedOrderWithOcrRequestId($requestId)
    {
        echo('Creating Order with OCR request_id='.$requestId.PHP_EOL);
        $faker = \Faker\Factory::create();
        $lastFreeDate = Carbon::now()->addDays($faker->numberBetween($min = 1, $max = 10))->toDateTimeString();
        $estimated_arrival_utc = Carbon::now()->addDays($faker->numberBetween($min = 1, $max = 10))->toDateTimeString();

        $orderId = DB::table('t_orders')->insertGetId([
            'request_id' => $requestId,
            'shipment_designation' => $this->getRndElem(self::DESTINATION_LIST),
            'equipment_type' => $this->getRndElem(self::EQUIPMENT_TYPE_LIST),
            'shipment_direction' => $this->getRndElem(self::DIRECTION_LIST),
            'one_way' => $faker->boolean,
            'yard_pre_pull' => $faker->boolean,
            'has_chassis' => $faker->boolean,
            'unit_number' => $this->getRndElem(self::UNIT_NUMBER_LIST),
            'equipment' => $this->getRndElem(self::EQUIPMENT_LIST),
            'equipment_size' => $this->getRndElem(self::EQUIPMENT_SIZE_LIST),
            'owner_or_ss_company' => $this->getRndElem(self::OWNER_OR_SS_COMPANY_LIST),
            'hazardous' => null, // this should roll up from the line item?
            'expedite_shipment' => $faker->boolean(),
            'reference_number' => $faker->lexify(),
            'rate_quote_number' => $faker->numerify('##########'),
            'seal_number_list' => $faker->bothify('["?#######","?#######","?#######"]'),  // this must be valid JSON
            // note, from confusion in migrations? you may need to run mysql command:
            //   alter table t_orders rename column port_ramp_of_origon to port_ramp_of_origin;
            'port_ramp_of_origin' => strtoupper($faker->lexify('???')),
            'port_ramp_of_destination' => strtoupper($faker->lexify('???')),
            'vessel' => $faker->firstNameFemale(),
            'voyage' => $faker->numerify('###').strtoupper($faker->lexify('???')),
            'master_bol_mawb' => $this->getRndElem([null, strtoupper($faker->bothify('??????#####????#'))]),
            'house_bol_hawb' => $this->getRndElem([null, strtoupper($faker->bothify('??????#####????#'))]),
            'estimated_arrival_utc' => $estimated_arrival_utc,
            'last_free_date_utc' => $lastFreeDate,
        ]);

        return $orderId;
    }

    /**
     * Create an OrderLineItem
     *
     * Make an order line item, associated with an orderId
     *
     * @param int $orderId the orderId for the line item
     *
     * @return void
     */
    public function seedOrderLineItem_nonHazardous($orderId)
    {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker)); // needed for productName

        $quantity = $faker->numberBetween($min = 1, $max = 1000);
        $weight = $faker->numberBetween($min = 1, $max = 100);

        $orderLineItemId = DB::table('t_order_line_items')->insertGetId([
            't_order_id' => $orderId,
            'quantity' => $quantity,
            'unit_of_measure' => $this->getRndElem(self::UOM_PACKAGE_LIST),
            'description' => $faker->productName,
            'weight' => $weight,
            'total_weight' => $quantity * $weight,
            'weight_uom' => $this->getRndElem(self::UOM_WEIGHT_LIST),
            'is_hazardous' => false,
            'haz_contact_name' => null,
            'haz_phone' => null,
            'haz_un_code' => null,
            'haz_un_name' => null,
            'haz_class' => null,
            'haz_qualifier' => null,
            'haz_description' => null,
            'haz_imdg_page_number' => null,
            'haz_flashpoint_temp' => null,
            'haz_flashpoint_temp_uom' => null,
            'packaging_group' => null,
        ]);

        return $orderLineItemId;
    }

    public function createOCRJob()
    {
        if (rand(0, 100) < self::PCT_ORDERS_INTAKE_REJECTED) {
            return $this->seedOcrJob_intakeRejected();
        } else {
            return $this->seedOcrJob_ocrPostProcessingComplete();
        }
    }

    //
    // "flow" replicated from: bab69a29-80f4-51c4-a3f0-cc4d3cc8b6a5
    // create state #1: intake-started
    // create state #2: ocr-waiting
    // create state #3: ocr-completed
    // create state #4: process-ocr-output-file-complete
    // create state #5: ocr-post-processing-complete
    //

    public function seedOcrJob_ocrPostProcessingComplete()
    {
        echo('Creating OCR job with status=ocr-post-processing-complete'.PHP_EOL);
        $faker = \Faker\Factory::create();

        // request_id must be shared by all states, and resulting order
        $request_id = $faker->uuid;

        // handy variables
        $time5MinutesAgo = Carbon::now()->subMinutes(5)->toDateTimeString();
        $time4MinutesAgo = Carbon::now()->subMinutes(4)->toDateTimeString();
        $time3MinutesAgo = Carbon::now()->subMinutes(3)->toDateTimeString();
        $time2MinutesAgo = Carbon::now()->subMinutes(2)->toDateTimeString();
        $time1MinutesAgo = Carbon::now()->subMinutes(1)->toDateTimeString();

        // create state #1: intake-started
        DB::table('t_job_state_changes')->insert([
            'request_id' => $request_id,
            'status_date' => $time5MinutesAgo,
            'status' => 'intake-started',
            'status_metadata' => '{"event_info": {"event_time": "2019-12-06T20:28:59.595Z", "object_key": "intakeemail/4tckssjbuh0c2dt8rlund3efvcd4g6pmjeagee81", "bucket_name": "dmedocproc-emailintake-dev", "aws_request_id": "'.$request_id.'", "log_group_name": "/aws/lambda/intake-filter-dev", "log_stream_name": "2019/12/06/[$LATEST]55e4fa95494f4364a68a85e537e8e3fa", "event_time_epoch_ms": 1575664139000}, "request_id": "'.$request_id.'", "source_summary": {"source_type": "email", "source_email_subject": "Fwd: test 202", "source_email_to_address": "dev@docprocessing.draymaster.com", "source_email_from_address": "Peter Nelson <peter@peternelson.com>", "source_email_body_prefixes": ["b\'---------- Forwarded message ---------\\r\\nFrom: Peter Nelson <peter@peternelson.com>\\r\\nDate: Fri, Dec 6, 2019 at 1:43 PM\\r\\nSubject: test 202\\r\\nTo: Peter B. Nelson <peter@peternelson.com>\\r\\n\'", "b\'<div dir=\"ltr\"><div class=\"gmail_default\" style=\"font-size:small\"><br></div><br><div class=\"gmail_quote\"><div dir=\"ltr\" class=\"gmail_attr\">---------- Forwarded message ---------<br>From: <b class=\"gmail_sendername\" dir=\"auto\">Peter Nelson</b> <span dir=\"auto\">&lt;<a href=\"mailto:peter@peternelson.com\">peter@peternelson.com</a>&gt;</span><br>Date: Fri, Dec 6, 2019 at 1:43 PM<br>Subject: test 202<br>To: Peter B. Nelson &lt;<a href=\"mailto:peter@peternelson.com\">peter@peternelson.com</a>&gt;<br><"], "source_email_string_length": 164489, "source_email_attachment_filenames": ["MATSON-examplar.pdf"]}, "read_log_commandline": "aws --profile=draymaster logs get-log-events --log-group-name=\'/aws/lambda/intake-filter-dev\' --log-stream-name=\'2019/12/06/[$LATEST]55e4fa95494f4364a68a85e537e8e3fa\' --start-time=\'1575664139000\'"}',
        ]);

        // create state #2: ocr-waiting
        DB::table('t_job_state_changes')->insert([
            'request_id' => $request_id,
            'status_date' => $time4MinutesAgo,
            'status' => 'ocr-waiting',
            'status_metadata' => '{"wait_reason": "WorkflowException", "exception_message": "No files found matching '.$request_id.'*.csv"}',
        ]);

        // create state #3: ocr-completed
        DB::table('t_job_state_changes')->insert([
            'request_id' => $request_id,
            'status_date' => $time3MinutesAgo,
            'status' => 'ocr-completed',
            'status_metadata' => '{"file_list": ["'.$request_id.'_MATSON-examplar_00000001.csv"], "s3_bucket": "dmedocproc-processedjobs-dev", "s3_region": "us-east-2"}',
        ]);

        // create state #4: process-ocr-output-file-complete
        DB::table('t_job_state_changes')->insert([
            'request_id' => $request_id,
            'status_date' => $time2MinutesAgo,
            'status' => 'process-ocr-output-file-complete',
            'status_metadata' => '{"filename": "'.$request_id.'_MATSON-examplar_00000001.csv", "row_count": 1}',
        ]);

        // create state #5: ocr-post-processing-complete
        DB::table('t_job_state_changes')->insert([
            'request_id' => $request_id,
            'status_date' => $time1MinutesAgo,
            'status' => 'ocr-post-processing-complete',
            'status_metadata' => '{"num_files_to_process": 1, "num_files_processed_successfully": 1, "num_files_processed_unsuccessfully": 0}',
        ]);

        // all done, return request_id needed to create an order
        return $request_id;
    }

    //
    // "flow" replicated from: f9983481-87f6-5b57-b779-62e2247a6db7
    // create state #1: intake-started
    // create state #2: intake-rejected
    //

    public function seedOcrJob_intakeRejected()
    {
        echo('Creating OCR job status=intake-rejected'.PHP_EOL);
        $faker = \Faker\Factory::create();

        // request_id must be shared by all states, and resulting order
        $request_id = $faker->uuid;

        // handy variables
        $time5MinutesAgo = Carbon::now()->subMinutes(5)->toDateTimeString();
        $time4MinutesAgo = Carbon::now()->subMinutes(4)->toDateTimeString();

        // create state #1: intake-started
        DB::table('t_job_state_changes')->insert([
            'request_id' => $request_id,
            'status_date' => $time5MinutesAgo,
            'status' => 'intake-started',
            'status_metadata' => '{"event_info": {"event_time": "2019-12-06T00:31:12.873Z", "object_key": "intakeemail/3ii4d5gekodc5mnmdaqim08ft445k3aodf3laqo1", "bucket_name": "dmedocproc-emailintake-dev", "aws_request_id": "'.$request_id.'", "log_group_name": "/aws/lambda/intake-filter-dev", "log_stream_name": "2019/12/06/[$LATEST]5fdbcbb1b8e24ee0afa6cc506b24b387", "event_time_epoch_ms": 1575592272000}, "request_id": "'.$request_id.'", "source_summary": {"source_type": "email", "source_email_subject": "test193", "source_email_to_address": "dev@docprocessing.draymaster.com", "source_email_from_address": "Peter Nelson <peter@peternelson.com>", "source_email_body_prefixes": ["b\'\\r\\n\'\", \"b\'<div dir=\"ltr\"><div class=\"gmail_default\" style=\"font-size:small\"><br></div></div>\\r\\n\'"], "source_email_string_length": 233429, "source_email_attachment_filenames": ["cai-logistics-pg1.pdf", "cai-logistics-pg2.pdf", "cai-logistics-pg3.pdf"]}}',
        ]);

        // create state #2: intake-rejected
        DB::table('t_job_state_changes')->insert([
            'request_id' => $request_id,
            'status_date' => $time4MinutesAgo,
            'status' => 'intake-rejected',
            'status_metadata' => '{"rejection_reason": "WorkflowException", "exception_message": "Ambiguous attachments in this email. Attachments found: [\'cai-logistics-pg1.pdf\', \'cai-logistics-pg2.pdf\', \'cai-logistics-pg3.pdf\']"}',
        ]);

        // all done, return request_id needed to create an order
        return $request_id;
    }

    public function getRndElem($stringArray)
    {
        return $stringArray[rand(0, count($stringArray) - 1)];
    }
}





/*
-- MYSQL CODE: SHOW FOREIGN KEY CONSTRAINTS

    select concat(fks.constraint_schema, '.', fks.table_name) as foreign_table,
        '->' as rel,
        concat(fks.unique_constraint_schema, '.', fks.referenced_table_name)
                as primary_table,
        fks.constraint_name,
        group_concat(kcu.column_name
                order by position_in_unique_constraint separator ', ')
                as fk_columns
    from information_schema.referential_constraints fks
    join information_schema.key_column_usage kcu
        on fks.constraint_schema = kcu.table_schema
        and fks.table_name = kcu.table_name
        and fks.constraint_name = kcu.constraint_name
    where fks.constraint_schema = 'omdb' -- SPECIFY THE DB NAME HERE
    group by fks.constraint_schema,
            fks.table_name,
            fks.unique_constraint_schema,
            fks.referenced_table_name,
            fks.constraint_name
    order by fks.constraint_schema,
            fks.table_name;
*/



/*
-- DEAL WITH FOREIGN KEY CONSTRAINTS

    set foreign_key_checks=0;
    delete from tablename where id=something;
    set foreign_key_checks=1;
*/



/*
-- DELETE ALL "NEWLY" CREATED ORDERS, IN PROPER SEQUENCE

    delete from t_order_line_items where created_at > '2020-03-01'; delete from t_orders where created_at > '2020-03-01'; delete from t_job_latest_state where created_at > '2020-03-01'; delete from t_job_state_changes where created_at > '2020-03-01';

*/



/*
-- SEE LASTEST STATUS SUMMARY FOR ANY OCR JOB

    select * from t_job_latest_state join v_status_summary on t_job_latest_state.t_job_state_changes_id =  v_status_summary.t_job_state_changes_id;
*/
