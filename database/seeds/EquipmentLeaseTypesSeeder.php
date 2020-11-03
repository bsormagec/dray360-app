<?php

/**
 * Usage: php artisan db:seed --class=EquipmentLeaseTypesSeeder
 */

use Carbon\Carbon;
use Illuminate\Database\Seeder;

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
 * Note, to clear out the existing rows from t_equipment_types, you have to work around
 * the foreign key constraint from t_orders.t_equipment_type_id -> t_equipment_types.id
 * Use this SQL statement: delete from t_equipment_types where id not in (select distinct t_equipment_type_id from t_orders where t_equipment_type_id is not null);
 */
class EquipmentLeaseTypesSeeder extends Seeder
{
    const INPUT_FILES = [
#        [
#            "COMPANY_ID" => 1,  # Cushing
#            "T_TMS_PROVIDER_ID" => 1,
#            "FILENAME" => 'database/seeds/cushing_equipment_lease_types_20201028.csv'
#        ],
#        [
#            "COMPANY_ID" => 2,  # TCompaniesDemo, doing double duty as CushingOnboarding
#            "T_TMS_PROVIDER_ID" => 1,
#            "FILENAME" => 'database/seeds/cushing_equipment_lease_types_20201028.csv'
#        ],
#        [
#            "COMPANY_ID" => 4,  # IXTOnboarding
#            "T_TMS_PROVIDER_ID" => 1,
#            "FILENAME" => 'database/seeds/ixt_equipment_lease_types_20201028.csv'
#        ],
#        [
#            "COMPANY_ID" => 5,  # IXT
#            "T_TMS_PROVIDER_ID" => 1,
#            "FILENAME" => 'database/seeds/ixt_equipment_lease_types_20201028.csv'
#        ],
        [
            "COMPANY_ID" => 6,  # PortCityLogisticsOnboarding
            "T_TMS_PROVIDER_ID" => 1,
            "FILENAME" => 'database/seeds/pcl_equipment_lease_types_20201028.csv'
        ],
        [
            "COMPANY_ID" => 7,  # PortCityLogistics
            "T_TMS_PROVIDER_ID" => 1,
            "FILENAME" => 'database/seeds/pcl_equipment_lease_types_20201028.csv'
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::INPUT_FILES as $inputfile) {
            $companyId = $inputfile['COMPANY_ID'];
            $tmsProviderId = $inputfile['T_TMS_PROVIDER_ID'];
            $filename = $inputfile['FILENAME'];
            $equipmentData = $this->getCsvData($filename);
            $rowCount = count($equipmentData);

            foreach ($equipmentData as $csvRow) {
                $equipmentTypeRow = $this->getEquipmentTypeRowFromCsvRow($csvRow, $companyId, $tmsProviderId);
                $tmsEquipId = $equipmentTypeRow['tms_equipment_id'];
                $exists = $this->equipmentExists($companyId, $tmsProviderId, $tmsEquipId);
                if (! $exists) {
                    $this->insertRow($rowCount, $equipmentTypeRow);
                } else {
                    $this->updateRow($rowCount, $equipmentTypeRow, $companyId, $tmsProviderId, $tmsEquipId);
                }
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
        print("\n");
        print("\n");
        print("\n");
        print('reading: '.$filename."\n");
        print("\n");

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
     * Assumes file format: id,line,type,equipmentlength,lineprefix,scac
     *
     * @param $csvRow        full row data
     * @param $companyId     t_company_id
     * @param $tmsProviderId t_tms_provider_id
     *
     * @return EquipmentType
     */
    public function getEquipmentTypeRowFromCsvRow($csvRow, $companyId, $tmsProviderId)
    {
        $sizeAndType = trim($csvRow['type']);
        $size = trim($csvRow['equipmentlength']);
        $type = trim(str_replace($size, '', $sizeAndType));
        $lineprefix = $csvRow['lineprefix'];
        if (strlen($lineprefix) == 0) {
            $prefixes = [];
        } else {
            $prefixes = explode(',', $lineprefix);
        }

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
            'line_prefix_list' => $prefixes,
        ]);
    }

    /**
     * Return true if row already exists for company/tms/equipid (and is not soft-deleted)
     *
     * @param $companyId      t_company_id
     * @param $tmsProviderId  t_tms_provider_id
     * @param $tmsEquipmentId tms_equipment_id
     *
     * @return boolean exists
     */
    public function equipmentExists($companyId, $tmsProviderId, $tmsEquipmentId)
    {
        $rows = DB::table('t_equipment_types')
            ->where(
                [
                    ['t_company_id', '=', $companyId],
                    ['t_tms_provider_id', '=', $tmsProviderId],
                    ['tms_equipment_id', '=', $tmsEquipmentId],
                ]
            )
            ->whereNull('deleted_at')  # don't delete a row that is already soft-deleted
            ->get();
        $exists = (count($rows) > 0);
        return $exists;
    }

    /**
     * Delete existing row from table if it already exists
     *
     * @param $companyId      t_company_id
     * @param $tmsProviderId  t_tms_provider_id
     * @param $tmsEquipmentId tms_equipment_id
     *
     * @return void
     */
    public function deleteRow_DEPRECATED($companyId, $tmsProviderId, $tmsEquipmentId)
    {
        DB::table('t_equipment_types')
            ->where(
                [
                    ['t_company_id', '=', $companyId],
                    ['t_tms_provider_id', '=', $tmsProviderId],
                    ['tms_equipment_id', '=', $tmsEquipmentId],
                ]
            )
            ->whereNull('deleted_at')  # don't delete a row that is already soft-deleted
            ->delete();
    }

    /**
     * Insert row into t_equipment_types database
     *
     * @param $equipmentTypeRow a single row of data
     *
     * @return int new row id
     */
    public function insertRow($rowCount, $equipmentTypeRow)
    {
        $e = $equipmentTypeRow;
        // insert the address
        $equipmentTypeId = DB::table('t_equipment_types')
        ->insertGetId(
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
                'line_prefix_list' => json_encode($e['line_prefix_list']),
            ]
        );

        // happy message, return
        $msg = 'inserted csvfile row:'.$e['rownum'].'/'.$rowCount.' for company:'.$e['t_company_id'].' as t_equipment_types.id:'.$equipmentTypeId."\n";
        print($msg);
        return ($equipmentTypeId);
    }

    /**
     * Update existing row for company/tms/equipid
     *
     * @param $equipmentTypeRow a single row of data
     *
     * @return void
     */
    public function updateRow($rowCount, $equipmentTypeRow, $companyId, $tmsProviderId, $tmsEquipmentId)
    {
        $e = $equipmentTypeRow;
        // insert the address
        $updatedRows = DB::table('t_equipment_types')
            ->where(
                [
                    ['t_company_id', '=', $companyId],
                    ['t_tms_provider_id', '=', $tmsProviderId],
                    ['tms_equipment_id', '=', $tmsEquipmentId],
                ]
            )
            ->whereNull('deleted_at')
            ->update(
                [
                    'equipment_owner' => $e['equipment_owner'],
                    'row_type' => $e['row_type'],
                    'equipment_type_and_size' => $e['equipment_type_and_size'],
                    'equipment_type' => $e['equipment_type'],
                    'equipment_size' => $e['equipment_size'],
                    'scac' => $e['scac'],
                    'updated_at' => $e['updated_at'],
                    'line_prefix_list' => json_encode($e['line_prefix_list']),
                ]
            );

        // happy message, return
        $msg = 'updated csvfile row:'.$e['rownum'].'/'.$rowCount.' for company:'.$e['t_company_id'].', tms_equipment_id:'.$tmsEquipmentId."\n";
        print($msg);
    }
}
