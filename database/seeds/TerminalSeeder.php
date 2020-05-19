<?php

/**
 * Usage: php artisan db:seed --class=TerminalSeeder
 */

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;  // for CSV file loading


/**
 * TerminalSeeder builds tables from Tom's spreadsheet
 */
class TerminalSeeder extends Seeder
{

    // define filename from Tom's spreadsheet listing validated terminals
    // note that column A was renamed to
    const TOMS_TERMINAL_LIST_CSV_FILE = 'database/seeds/Copy-of-list-of-terminals-work-in-progress.20200517.csv';



    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $terminalData = self::getCsvData(self::TOMS_TERMINAL_LIST_CSV_FILE);
        foreach ($terminalData as $terminal) {
            self::insertRow($terminal);
        }
    }


    /**
     * Parse one row of Tom's CSV data
     *
     * @param $terminal array a single row\
     *
     * @return void
     */
    public function insertRow($terminal) {

        // insert the address
        $addressId = DB::table('t_addresses')->insertGetId(
            [
                'location_name' => $terminal['name'],
                'address_line_1' => $terminal['address'],
                'city' => $terminal['city'],
                'state' => $terminal['state'],
                'postal_code' => $terminal['zip'],
                'country' => $terminal['country'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        // insert the terminal
        $terminalId = DB::table('t_terminals')->insertGetId(
            [
                't_address_id' => $addressId,
                'verification_status' => $terminal['verification_status'],
                'name' => $terminal['name'],
                'type' => $terminal['type'],
                'terminal_code' => $terminal['terminal_code'],
                'major_metro' => $terminal['Major Metro'],
                'metro_region' => $terminal['Metro Region'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        // happy message
        $msg = 'inserted Tom\'s csvfile row:'.$terminal['rownum'].' as id:'.$terminalId."\n";
        print ($msg);

    }







    /**
     * Load the CSV file, return array
     * @param string $filename name of csv file
     * @return array
     */
    public function getCsvData($filename)
    {
        print('reading: '.$filename."\n");

        $alldata = [];
        if (($handle = fopen($filename, "r")) !== FALSE) {
            $headers = null;
            $rownum = -1;
            while (($data = fgetcsv($handle)) !== FALSE) {
                $rownum++;
                if ($rownum == 0) {
                    $headers = $data;
                } else {
                    $rowData = ['rownum' => $rownum];
                    $fieldcount = count($data);
                    for ($i=0; $i < $fieldcount; $i++) {
                        $colname = $headers[$i];
                        $coldata = $data[$i];
                        $rowData[$colname] = $coldata;
                    }
                    array_push($alldata, $rowData);
                }
            }
            fclose($handle);
        }
        return $alldata;
    }

}
