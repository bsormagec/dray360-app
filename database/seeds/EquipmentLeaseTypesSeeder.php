<?php

/**
 * Usage: php artisan db:seed --class=EquipmentLeaseTypesSeeder
 */

use Carbon\Carbon;
use Illuminate\Database\Seeder;

// for CSV file loading




/**
 * TerminalSeeder builds tables from Heathers XLSX (saved as CSV) profittools table extracts
 *
 * Specifically, Heather extracts the Profit Tools table/columns to XLSX
 * which is manually saved as CSV with the help of LibreCalc or Excel:
 *
 * EquipmentLeaseType
 * =====================
 * id
 * line
 * type
 * equipmentlength
 * scac
 *
 *
 */
class EquipmentLeaseTypesSeeder extends Seeder
{
    const INPUT_FILES = [
        [
            "COMPANY_ID" => 1,
            "T_TMS_PROVIDER_ID" => 1,
            "FILENAME" => 'database/seeds/Cushing Equipment Lease Types.20200917.csv'
        ],
        [
            "COMPANY_ID" => 2,
            "T_TMS_PROVIDER_ID" => 1,
            "FILENAME" => 'database/seeds/TCompaniesDev Equipment Lease Types.20200917.csv'
        ],
        [
            "COMPANY_ID" => 4,
            "T_TMS_PROVIDER_ID" => 1,
            "FILENAME" => 'database/seeds/IXTOnboarding Equipment Lease Types.20200917.csv'
        ]
    ];

    const TOMS_TERMINAL_LIST_CSV_FILE = 'database/seeds/Copy-of-list-of-terminals-work-in-progress.20200517.csv';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // echo("\ndieing\n\n");
        // die();

        foreach (self::INPUT_FILES as $inputfile) {
            $companyId = $inputfile['COMPANY_ID'];
            $tmsProviderId = $inputfile['T_TMS_PROVIDER_ID'];
            $filename = $inputfile['FILENAME'];
            $equipmentData = self::getCsvData($filename);

            foreach ($equipmentData as $csvRow) {
                $equipmentTypeRow = self::getEquipmentTypeRowFromCsvRow($csvRow, $companyId, $tmsProviderId);
                self::insertRow($equipmentTypeRow);
            }
        }
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
        if (($handle = fopen($filename, "r")) !== false) {
            $headers = null;
            $rownum = -1;
            while (($data = fgetcsv($handle)) !== false) {
                $rownum++;
                if ($rownum == 0) {
                    $headers = $data;
                } else {
                    $rowData = ['rownum' => $rownum];
                    $fieldcount = count($data);
                    for ($i = 0; $i < $fieldcount; $i++) {
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

    /**
     * Parse one row of Heather's CSV data
     *
     * @param $csvrow
     *
     * @return EquipmentType
     */
    public function getEquipmentTypeRowFromCsvRow($csvRow, $companyId, $tmsProviderId)
    {
        $sizeAndType = trim($csvRow['type']);
        $size = trim($csvRow['equipmentlength']);
        $type = trim(str_replace($size, '', $sizeAndType));

        // if the "size" isn't found within "sizeandtype" then neither size nor type can be trusted
        // On second thought, just leave them alone
        // if (! $size || ! strpos($sizeAndType, $size)) {
        //     $size = null;
        //     $type = null;
        // }
        //

        return ([
            't_company_id' => $companyId,
            't_tms_provider_id' => $tmsProviderId,
            'tms_equipment_id' => trim($csvRow['id']),
            'equipment_owner' => trim($csvRow['line']),
            'row_type' => 'combined',  // vs separate
            'equipment_type_and_size' => $sizeAndType,
            'equipment_type' => $type,
            'equipment_size' => $size,
            'scac' => trim($csvRow['scac']),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'rownum' => $csvRow['rownum'],
        ]);
    }

    /**
     * Insert row into t_equipment_types database
     *
     * @param $equipmentTypeRow a single row of data
     *
     * @return int new row id
     */
    public function insertRow($equipmentTypeRow)
    {
        $e = $equipmentTypeRow;
        // insert the address
        $equipmentTypeId = DB::table('t_equipment_types')->insertGetId(
            [
                't_company_id' => $e['t_company_id'],
                't_tms_provider_id' => $e['t_tms_provider_id'],
                'tms_equipment_id' => $e['tms_equipment_id'],
                'equipment_owner' => $e['equipment_owner'],
                'row_type' => $e['row_type'],
                'equipment_type_and_size' => $e['equipment_type_and_size'],
                'equipment_type' => $e['equipment_type'],
                'equipment_size' => $e['equipment_size'],
                'scac' => $e['scac'],
                'created_at' => $e['created_at'],
                'updated_at' => $e['updated_at'],
            ]
        );

        // happy message, return
        $msg = 'inserted Heather\'s equipment csvfile row:'.$e['rownum'].' as id:'.$equipmentTypeId."\n";
        print($msg);
        return ($equipmentTypeId);
    }
}
