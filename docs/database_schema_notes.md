# Dray 360 Database Schema

This document is where overall intentions and particular complexities of the data model can be described.

It is not intended to document every individual column and table, most of which are self-explanatory and need no futher comment.






<br><br>

## Table/Column Notes




<br><br><br>

### TABLE:  t_object_locks

------

0. id (ubigint primary key)
0. object_type (indexed varchar 64). for example “request”
0. object_id (indexed varchar 64). for example “0055bec1-d8e9-5726-9918-e6c0b8e380bd”
0. lock_type (varchar 64), how lock created. e.g. “claim-lock”, “select-request”, “open-order”
0. user_id (indexed ubint fk to users). user who owns the lock
0. created_at (datetime). when lock first request
0. updated_at (datetime). i.e. refreshed_at, used to indicate lock has been refreshed, s/b every <n> seconds. locks updated more than <M> seconds ago are considered to be implicitly released.
0. deleted_at (datetime). i.e. released_at, used to indicate the lock has been explicitly released.





<br><br><br>

### TABLE:  t_training_documents

------

This table is used by the (machine-learning) classification system, which identifies what type of document has been requested for processing (i.e. identifies a PDF's variant_type).

#### type

The value "deliveryorderpdf" is the only value, to start with. In future, we might see something like "customsdeclarationpdf", for example.

#### s3uri

Location of document on S3 bucket. e.g. "s3://ourbucket/documentfile.pdf"

#### sha256sum

The hash of the source document. e.g. "55a4a6938a637a216d5610d990f7fb6267bcb4ba8867428481f9bfbc787b28d6"

#### discovered_at

Datetime, indicates when this source document was first used/discovered)

#### created_at, updated_at, deleted_at

Datetime, creation date of this row, etc.

#### rendered_images

````json
    {
        "100x": [
            "s3://dray360-onboarding-extracts/labelled_documents/cargowise_pdftext_jan2021/not_cargowise/ITG-NOT-CW/sorted/AOT/export/titantirefreeporttosantos26-11168.100x.page_1.jpg"
        ],
        "200x": [
            "s3://dray360-onboarding-extracts/labelled_documents/cargowise_pdftext_jan2021/not_cargowise/ITG-NOT-CW/sorted/AOT/export/titantirefreeporttosantos26-11168.200x.page_1.jpg"
        ],
        "300x": [
            "s3://dray360-onboarding-extracts/labelled_documents/cargowise_pdftext_jan2021/not_cargowise/ITG-NOT-CW/sorted/AOT/export/titantirefreeporttosantos26-11168.300x.page_1.jpg"
        ],
        "400x": [
            "s3://dray360-onboarding-extracts/labelled_documents/cargowise_pdftext_jan2021/not_cargowise/ITG-NOT-CW/sorted/AOT/export/titantirefreeporttosantos26-11168.400x.page_1.jpg"
        ],
        "300dpi": [
            "s3://dray360-onboarding-extracts/labelled_documents/cargowise_pdftext_jan2021/not_cargowise/ITG-NOT-CW/sorted/AOT/export/titantirefreeporttosantos26-11168.300dpi.page_1.jpg"
        ]
    }
````
            $table->json('rendered_images')->nullable();

#### document_metadata

````json
    {
        "company_id": 2,
        "user_id": 19
    }
````

#### external_references

````json
    [
       {
           "type": "request_id",
           "value": "2ec6643b-18ea-51b5-888f-33bc886edd94"
       },
       {
           "type": "onboardingdepository",
           "value": "s3://ourbucket/documentfile.eml"
       }
    ]
````

#### extracted_features_list

````json
    [
        {
            "type": "ordered_word_list",
            "s3uri": "s3://dray360-onboarding-extracts/labelled_documents/cargowise_pdftext_jan2021/not_cargowise/ITG-NOT-CW/sorted/AOT/export/titantirefreeporttosantos26-11168.ordered_words_list.json"
        },
        {
            "type": "tableshapes",
            "s3uri": "s3://ourbucket/documentfile.tableshapes.json"
        }
    ]
````

#### labels

````json
    [
        {
            "type": "iscargowisepdf",
            "value": true,
            "source": "aaronbryden",
            "date": "2021-01-31"
        },
        {
            "type": "variantname",
            "value": "CargowiseHorizontal-import",
            "source": "productionrequest",
            "date": "2021-03-03"
        }
    ]
````





<br><br><br>

### TABLE:  t_dictionary_cache_definitions

------

This table `t_dictionary_cache_definitions` is used in conjuction with the `t_dictionary_cache_entries` and `t_dictionary_items` tables.

The basic idea is that for every distinct `t_dictionary_items.item_type` value there can be a single-row definition at `t_dictionary_cache_definitions.cache_type`. These define how to use any matching rows in `t_dictionary_cache_entries.cache_type` table, rows that each point to a specific memorized entry in `t_dictionary_items.item_type`.

* one `t_dictionary_cache_definitions.cache_type` entry ==>
* many `t_dictionary_cache_entries.cache_type` entries ==>
* one `t_dictionary_items.item_type` entry



<br><br>

#### Related Tables

| `t_dictionary_cache_definitions` | `t_dictionary_cache_entries`       | `t_dictionary_items` | Abbyy/rules fieldname                  | Mapped to raw text at                             | used by cache_type(s)           |  comments |
| :----------                      | :------------------                | :--------------      | :--------------                        | :--------------                                   | :-------------                  | :------------ |
| n/a                              | `t_dictionary_item_id`             | `id`                 | n/a                                    | n/a                                               |                                 | matches `t_orders.whatever_dictid` |
| n/a                              | `t_company_id`                     | `t_company_id`       | n/a                                    | n/a                                               |                                 | should match `t_orders.t_company_id` |
| n/a                              | `verified_count`                   | n/a                  | n/a                                    | n/a                                               |                                 | increments on every verify/submit |
|                                  |                                    |                      |                                        |                                                   |                                 | |
| `cache_type`                     | `cache_type`                       | `item_type`          | n/a                                    | n/a                                               |                                 | |
| n/a                              |  n/a                               | `t_tms_provider_id`  | n/a                                    | n/a                                               |                                 | should match `t_orders.t_tms_provider_id` |
| n/a                              |  n/a                               | `t_user_id`          | n/a                                    | n/a                                               |                                 | |
| n/a                              |  n/a                               | `item_key`           | n/a                                    | n/a                                               |                                 | value sent to TMS |
| n/a                              |  n/a                               | `item_display_name`  | n/a                                    | n/a                                               |                                 | value displayed in UI droplist |
| n/a                              |  n/a                               | `item_value`         | n/a                                    | n/a                                               |                                 | JSON blob of key/value/other_info |
|                                  |                                    |                      |                                        |                                                   |                                 | |
| `use_bill_to_address_raw_text`   | `cached_bill_to_address_raw_text`  | n/a                  | `bill_to_address`                      | `t_orders`<br>`.bill_to_address_raw_text`         | template                        | |
| `use_carrier`                  * | `cached_carrier`                 * | n/a                  | `carrier`                              | `t_orders`<br>`.carrier`                          | carrier                         | displayed as "SSL" on the ui for ITG |
| `use_equipment_size`           * | `cached_equipment_size`          * | n/a                  | `container_length`                     | `t_orders`<br>`.equipment_size`                   | itgcontainer                    | displayed as "Container" on the ui for ITG |
| `use_event1_address_raw_text`    | `cached_event1_address_raw_text`   | n/a                  | `event1_location`                      | `t_order_address_events`<br>`.t_address_raw_text` | template                        | where `t_order_address_events`<br>`.event_number` = 1 |
| `use_event2_address_raw_text`    | `cached_event2_address_raw_text`   | n/a                  | `event2_location`                      | `t_order_address_events`<br>`.t_address_raw_text` | template                        | where `t_order_address_events`<br>`.event_number` = 2 |
| `use_event3_address_raw_text`  * | `cached_event3_address_raw_text` * | n/a                  | `event3_location`                      | `t_order_address_events`<br>`.t_address_raw_text` | template                        | where `t_order_address_events`<br>`.event_number` = 3 |
| `use_hazardous`                * | `cached_hazardous`               * | n/a                  | `hazmat`                               | `t_orders`<br>`.hazardous`                        | template                        | this will be a 1 or 0 |
| `use_shipment_direction`       * | `cached_shipment_direction`      * | n/a                  | `order_type`                           | `t_orders`<br>`.shipment_direction`               | template                        | i.e. import/export/crosstown |
| `use_template`                 * | `cached_template_key`            * | `item_key`           | _lookup variant_<br>`container_length` | `t_orders`<br>`.tms_template_dictid.item_key`     | itgcontainer                    | first cache-search for template, then use template for further searches |
| `use_variant_name`               | `cached_variant_name`              | n/a                  | `variant_name`                         | `t_orders`<br>`.variant_name`                     | vessel<br>carrier<br>template   | |
| `use_vessel`                   * | `cached_vessel`                  * | n/a                  | `vessel`                               | `t_orders`<br>`.vessel`                           | vessel                          | |

_* indicates new as of Feb 2021_

<br><br>

#### Cache Types

Note that `t_dictionary_cache_definitions.cache_type` == `t_dictionary_cache_entries.cache_type` == `t_dictionary_items.item_type`

As of the initial implementation, Feb 2021, these are the in-use cache types

| Cache/Item Type | Where Mapped<br>_table.column_  | Mapped cache columns          |
| :----------     | :--------------                 | :------------                 |
| `vessel`        | `t_orders.vessel_dictid`        | variant<br>vessel             |
| `carrier`       | `t_orders.carrier_dictid`       | variant<br>carrier            |
| `itgcontainer`  | `t_orders.container_dictid`     | variant<br>template<br>equipment_size    |
| `template`      | `t_orders.tms_template_dictid`  | use_bill_to_address_raw_text<br>use_carrier<br>use_equipment_size<br>use_event(1/2/3)_address_raw_text<br>use_hazardous<br>use_shipment_direction<br>use_template<br>use_variant_name<br>use_vessel |









<br><br><br>

### TABLE:  t_chainio_requestid_submissions

------

This table is used to compute the submission sequence of an order within a request in a threadsafe (no race condition) way. To see how this is implemented, refer to the source code in `dray360-microservices:/lambdas/submitchainio/getsubmissionsequence.py`.

#### request_id

In fact this is a concatenated key containing `request_id` + `:` + `reference_number` because that is how the ITG/Chainio submission sequence is to be tracked. Note that the ordinary request_id value is only 36 characters, and this column is defined as varchar(512) to there is no reason to worry that the concatenation will run out of space.







<br><br><br>

### TABLE:  t_job_state_changes

------

Every state change for a request/order combination (or request/null when orders don't exist yet) is logged here.

#### status

Each entry contains a single-word `status`, for example `intake-accepted` or `ocr-waiting` or `sending-to-wint`, etc.

These `status` values are typically used as attributes in an SNS message, to trigger a queue-entry which triggers a python-microservice.

For detailed information on `status` values, see [Status Metadata Documentation](./status_metadata.md)


#### status_metadata

This JSON type column records different information for each `status` type.

See the dedicated document in this folder for details on each status_metadata type.

[Status Metadata Documentation](docs/status_metadata.md)







<br><br><br>

### TABLE:  t_job_latest_state

------

For a given request/order combination (or request/null when orders don't exist yet) the latest row inserted into the `t_job_state_changes` table will be recorded on this table.

Note that this "latest state" is recorded as written to the database, not as dated. Ergo, in the odd case where an earlier status change is written to the database later than an actual subsequent status change (due to some kind of asynchronous process delay) then the earlier timestamp will be recorded as the "latest state". This is an hypothetical problem only, and has not been seen in practice, but I mention it here in case it ever does happen.

As of November 2020, the only thing that writes to the t_job_latest_state table is an automated mysql trigger called `t_job_state_change_updated`

#### company_id and order_id

These columns are nullable and _not_ foreign key references to the t_companies/t_orders table. This is intentional, because we can record order_id's for orders that were rolled-back and not actually committed to the database.




<br><br><br>

### TABLE:  t_orders

------

#### interchange_count

A given order-creation request (ocr or datafile) can create 0 to <n> orders. Each order will record in this `interchange_count` column the total number of orders created by its order-creation request batch.

#### interchange_err_count

If there were errors in an order-creation batch that resulted in some orders being created but others failing to be created, the total number of failures will be recorded in this `interchange_count_errors` on every order that did get created. Note that this value cannot be recorded on the failed orders because they weren't created and don't exist, heh.






<br><br><br>

### TABLE:  t_companies

------

In this data model a "company" is a customer of Dray360. Each company is uniquely identified by name and id. Those name's are hardcoded into the application and must not be allowed to change. .

#### name & id

These are hardcoded in `app/Models/Company.php` and reference by constant in `app/Console/Commands/ImportProfitToolsAddresses.php` among other places.

At this point in time, the `name` must be unique, it must not change, and it must not be assigned to a different `id` than first assigned. The `name/id` combination must be identical between prod/dev/staging environments.

If at some point in the future a "display name" is desired, different than "name", then add a new column for that purpose.

#### company_config:json

Used for company-specific (i.e. never inherited and overridden by specific tenants/domains/users) configuration parameters. The idea is to not add a plethora of boolean columns to the `t_companies` table for every conceivable flag.

JSON data structure:
```json
{
    "profit_tools_send_quantity_and_weight": true,
    "profit_tools_set_container_to_unknown": true,
    "profit_tools_enable_templates": true
}
```


#### configuration:json

Used for inheritable UI-specific configurations. This field is made available to the frontend. It overrides the default `tenants.configuation:json` configuration with any identical properties, and can itself be overridden by `users.configuration:json`.

JSON data structure:
```json
{
    "profit_tools_enable_templates": true
}
```



<br><br><br>

### TABLE:  t_ocrvariants

------

By rights this table would be called `t_datasource_variants` but when it was originally created the only variant type we processed was 'ocr' hence the name. Renaming it is fine if someone wants to do that at some point.

#### abbyy_variant_id

The abbyy_variant_id. Used only for abbyy. For non-ocr variant types (i.e. for non-Abbyy variants) set this value to negative one, i.e. `-1`.

#### abbyy_variant_name

Not necessarily the "abbyy" name, but also used as the general purpose variant name for non-ocr variants. For `variant_type=='ocr'` this will be the exact name as used by Abbyy.

#### variant_type

This indicates what type of variant is being described. Valid values are:

1. NULL (implies 'ocr')
1. ocr (i.e. Abbyy)
1. edi
1. tabular (includes CSV, XLSX, and possibly HTML tables; any flat colum/row data)
1. pdftext (for files we will parse with pdfplumber instead of using abbyy ocr)

#### classification

A list of matching keywords (and potentially other information like regions), to help classify variants.

JSON data structure:
```json
{
    "and_all_keywords":
        [
            "Sea Freight FCL Cartage Advice with Receipt",
            "Ocean ASDF"
        ],
    "and_any_keywords":
        [
            "CONSOLE",
            "SHIPMENT"
        ],
    "and_no_keywords":
        [
            "Profit Tools"
        ]
}
```

#### mapping

JSON data structure:
```json
{
    "abbyy_fieldname_1": {
        "source": "xlsx_header_1"
    },
    "abbyy_fieldname_2": {
        "source": "xlsx_header_2"
    }
}
```

#### classifier

This indicates which classifier will report that variant type. It is used by the intakefilter engine.

Example value (for variant 'itgcargowisepdf'): `itgcargowisepdf-classifier`

#### parser

This indicates which pdfplumber parser will be used to parse the variant, It is used by the postprocessingqueue engine.

Example value (for variant 'itgcargowisepdf'): `itgcargowisepdf-parser`

#### parser_options

Set to the parser, to tell it what options (only when overriding its defaults) to use. Usually null, but for example:

```json
{
    "pdfplumber_lines_type_options": {
        "snap_tolerance": 2
    },
    "pdfplumber_text_type_options": {
        "text_x_tolerance": 2
    }
}
```

#### parser_fields_list

Sent to the parser, to tell it what fields (in case of overriding its defaults) to use. In other words, usually null, but when used, for example:

Sample JSON data structure:
```json
{
    "vertical_fields_list": [
        [ "house_bol_hawb", "HOUSE BILL OF LADING" ],
        [ "master_bol_mawb", "OCEAN BILL OF LADING" ],
        [ "customer_number", "ORDER NUMBERS / REFERENCE", "ORDER NUMBERS/REFERENCE" ],
    ],
    "multiline_vertical_fields_list": [
        [ "weight", "WEIGHT", "Gross Weight" ],
        [ "quantity", "PACKAGES" ],
    ],
    "horizontal_fields_list": [
        [ "reference_number", "CONSOL" ],
    ]
}
```

#### company_id_list

This is a json array that stores list of companies that can use this datafile variant. If NULL or empty it means all companies can use the variant.

The reason for this column and to *not* to use the `t_company_ocrvariant_ocrrules` table with its `t_company_id`/`t_variant_id` columns to associate a company with the variant is because: the `company_id_list` gets used in conjunction with the aforementioned `classification` column to identify whether a given datafile should be represented by a particular variant; rules don't come into it.

JSON data structure:
````json-list
[
    company_id_1,
    company_id_2,
    etc.
]
````

Note that to search the company_id_list for a given company_id, use the `json_contains` function, like this:

````sql
select * from t_ocrvariants where json_contains(company_id_list, '2','$');
````

Which will find company_id=2, for example. note that the single-quotes are required for integer values. String values would be searched with embedded double-quotes, like `'"two"'`




<br><br><br>

### TABLE:  t_equipment_types

------

This table is seeded by ./database/seeders/EquipmentLeaseTypesSeeder.php

<br><br>

#### line_prefix_list

This is another JSON array that stores a list of text container code prefixes to help find and identify equipment types. It can be null of no line prefixes are known for that particular equipment.

JSON data structure:
````json-list
[
    line_prefix_1,
    line_prefix_2,
    etc.
]
````

See `company_id_list` and `status_metadata.order_id_list` for sample SQL queries.

