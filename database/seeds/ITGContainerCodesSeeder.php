<?php

/**
 * Usage: php artisan db:seed --class=ITGContainerCodeSeeder
 */

use App\Models\Company;
use App\Models\DictionaryItem;
use Illuminate\Database\Seeder;

/**
 * ITGContainer Code DictionaryItem Seeder for import from D. Duke's spreadsheet
 * extract, saved as a CSV file. Must include headers (columns):
 *   Code
 *   Description
 *   Mode
 *   Container Type
 *   Times Used
 */
class ITGContainerCodeSeeder extends Seeder
{
    const INPUT_FILES = [
        [
            "FILENAME" => 'database/seeds/CargoWise One Export - 20210107113712 - 414.csv'
        ],
    ];

    const COMPANY_NAMES = [
        'ITGOnboarding',
        'ITG'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::COMPANY_NAMES as $company_name) {
            $company_id = Company::where('name', $company_name)->first()['id'];

            foreach (self::INPUT_FILES as $inputfile) {
                $filename = $inputfile['FILENAME'];
                $dictData = $this->getCsvData($filename);
                $rowCount = count($dictData);

                foreach ($dictData as $csvRow) {
                    $dictItem = $this->getDictionaryItemFromCsvRow($csvRow);
                    $dictLookup = [
                        't_company_id' => $company_id,
                        'item_type' => $dictItem['item_type'],
                        'item_key' => $dictItem['item_key'],
                    ];
                    $rownum = $dictItem['rownum'];
                    unset($dictItem['rownum']);

                    $x = DictionaryItem::updateOrCreate($dictLookup, $dictItem);
                    $status = 'updated ';
                    if ($x->wasRecentlyCreated) {
                        $status = 'created ';
                    }
                    // happy message, return
                    $msg = $status.'csvfile row:'.$rownum.'/'.$rowCount.' for company_name: "'.$company_name.'", item_key: "'.$dictItem['item_key'].'",      item_display_name: "'.$dictItem['item_display_name'].'"';
                    $this->command->info($msg);
                }
            }
        }
    }

    /**
     * Parse one row of the ITG Container Type Codes data
     *
     * @param $csvRow full row data
     *
     * @return dictionaryItemData
     */
    public function getDictionaryItemFromCsvRow($csvRow)
    {
        return ([
            # id
            # created_at
            # updated_at
            # t_company_id
            # t_tms_provider_id
            # t_user_id
            'item_type' => 'itgcontainer',
            'item_key' => $csvRow['Code'],
            'item_display_name' => $csvRow['Description'],
            'item_value' => [
                'code' => $csvRow['Code'],
                'description' => $csvRow['Description'],
                'mode' => $csvRow['Mode'],
                'container_type' => 'Container Type',
                'times_used' => 'Times Used',
            ],
            # deleted_at
            'rownum' => $csvRow['rownum'],
        ]);
    }

    /**
     * Load the CSV file, return array. Note that this is shameless
     * copy/pasta from the EquipmentLeaseTypeSeeder.
     *
     * TODO: make this a shared library somewhere.
     *
     * @param string $filename name of csv file
     *
     * @return array
     */
    public function getCsvData($filename)
    {
        $this->command->info('');
        $this->command->info('');
        $this->command->info('');
        $this->command->info('reading: '.$filename);
        $this->command->info('');

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
}
