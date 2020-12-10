# Dray 360 Database Schema

This document is where overall intentions and particular complexities of the data model can be described. 

It is not intended to document every individual column and table, most of which are self-explanatory and need no futher comment. 






## Table/Column Notes





#######################

### t_job_state_changes

Every state change for a request/order combination (or request/null when orders don't exist yet) is logged here. 

### status

Each status is very carefully defined, here is a complete list (as of 11/27/2020):

1. failure-imageuploding-to-blackfl
1. failure-sending-to-wint
1. intake-accepted
1. intake-accepted-datafile
1. intake-exception
1. intake-rejected
1. intake-started
1. ocr-completed
1. ocr-post-processing-complete
1. ocr-post-processing-error
1. ocr-timedout
1. ocr-waiting
1. process-ocr-output-file-complete
1. process-ocr-output-file-error
1. sending-to-wint
1. shipment-created-by-wint
1. shipment-not-created-by-wint
1. shipment-not-updated-by-wint
1. shipment-updated-by-wint
1. success-imageuploding-to-blackfl
1. success-sending-to-wint
1. success-updating-to-wint
1. untried-imageuploding-to-blackfl
1. updated-by-subsequent-order
1. updates-prior-order
1. upload-requested



#### status_metadata

This JSON type column records different information for each `status` type. I will be updating this document to record all possible states here. It is currently documented in a Draw.io diagram and not very accessible.

Here follows a list of all status types and their associated metadata. If this list is found to be incompleted, inaccurate, out of date or in error... please correct it.

All status_metadata object include the following properties:

1. request_id
1. datetime_utciso
1. company_id
1. order_id (if available)




#### `ocr-waiting` status_metadata

* created by `./processingqueuemonitor/processingqueuemonitor.py`
* triggered by retry of `intake-accepted`

1. wait_reason
1. exception_message




#### `ocr-completed` status_metadata

* created by `./processingqueuemonitor/processingqueuemonitor.py`
* triggered by retry of `intake-accepted`

1. s3_bucket: where output files are stored
1. s3_folder: folder in that bucket
1. s3_region: where output files are stored
1. file_list: array of files matching `<requestid>_<sha256>_*`




#### `ocr-timedout` status_metadata

* created by `./processingqueuemonitor/processingqueuemonitor.py`
* triggered by retry of `intake-accepted` message after <N> times, see:
  - dray360-deploy:/cloudformation/templates/microservices.yml SQSOCRProcessingMonitorQueue.maxReceiveCount

1. message
1. receive_count, should be set to visibilitytimeout + 1
1. resurrection_count, should be set to max_resurrection_count




#### `intake-started` status_metadata

* created by `./intakefilter/intakefilter.py`
* triggered by S3 bucket file creaation, from SES email receipt or manual upload

1. source_summary:
   - source_type: "email"
   - source_email_subject
   - source_email_to_address
   - source_email_from_address
   - source_email_body_prefixes
   - source_email_string_length
   - source_email_attachment_filenames
   - source_email_recipient0  (i.e. aws "to" address)
   
   - source_type: "upload"
   - source_upload_filename
   - source_upload_api_request_id
1. event_info:
   - bucket_name
   - recipient0
   - object_key
   - event_key
   - event_time
   - aws_request_id
   - log_group_name
   - log_stream_name
   - event_time_epoch_ms
1. read_log_commandline
1. ocr_request_id
1. sha256sum
1. variant_name
   - if specified on upload by user-selected droplist
   - or in email subject header with keyword "variant", like "variant: mybestfreightbuyervariant"




#### `intake-accepted` status_metadata

* created by `./intakefilter/intakefilter.py`
* triggered by successful parsing of the intake message, the culmination of the intake-started process.

1. document_type: pdf | image | datafile
1. document_filename
1. archive_location
1. original_filename
1. variant_name (if specified)
1. resurrection_count, when original visibilitytimeout has expired (currrenty 1000), system will set this property (or increment it) and resubmit the event for another visibilitytimeout retries, up to MAX_RESURRECTION_COUNT (currently 5).




##### `failure-imageuploding-to-blackfl` status_metadata

* created by `./wintupdater/imageuploader.py`

1. message: 'exception sending image to profittools'
1. exception: exception message




##### `success-imageuploding-to-blackfl` status_metadata

* created by `./wintupdater/imageuploader.py`

1. imagetype: (blackfly image type, e.g. PRENOTE or DELIVERYORDER)
1. jpg_file_list: all individual files aggregated into a multi-page TIFF and uploaded as a single image



##### `untried-imageuploding-to-blackfl` status_metadata

* created by `./wintupdater/imageuploader.py`

1. 'message': 'no image files, blackfly credentials or tms_shipment_id found'




##### `status_metadata.order_id_list` (ocr-post-processing-error and ocr-post-processing-complete)

This is an array property in `status_metadata` for both the `ocr-post-processing-error` and `ocr-post-processing-complete` states. Here is a sample query showing how to parse its value and its length.

````sql
select 
        id
    ,request_id
    ,status
    ,json_extract(status_metadata,'$.order_id_list[0]') as first_order_id
    ,json_extract(status_metadata,'$.order_id_list') as order_id_list
    ,json_length(json_extract(status_metadata,'$.order_id_list')) as order_id_length
from t_job_state_changes 
where status in ('ocr-post-processing-error', 'ocr-post-processing-complete') 
having order_id_length > 2
order by id asc 
;
````

##### status_metadata.file_list (ocr-post-processing-error and ocr-post-processing-complete)

Every file processed in this request. If the list grows too large that it cannot be stored in a single SNS message (256KB max) then it will be truncated by deleting the largest element (and in case of a tie for largest, one will be picked at random) until the total size is under 256KB.





#######################

### t_job_latest_state

For a given request/order combination (or request/null when orders don't exist yet) the latest row inserted into the `t_job_state_changes` table will be recorded on this table. 

Note that this "latest state" is recorded as written to the database, not as dated. Ergo, in the odd case where an earlier status change is written to the database later than an actual subsequent status change (due to some kind of asynchronous process delay) then the earlier timestamp will be recorded as the "latest state". This is an hypothetical problem only, and has not been seen in practice, but I mention it here in case it ever does happen.

As of November 2020, the only thing that writes to the t_job_latest_state table is an automated mysql trigger called `t_job_state_change_updated`

#### company_id and order_id 

These columns are nullable and _not_ foreign key references to the t_companies/t_orders table. This is intentional, because we can record order_id's for orders that were rolled-back and not actually committed to the database.




#######################



### t_orders


#### interchange_count

A given order-creation request (ocr or datafile) can create 0 to <n> orders. Each order will record in this `interchange_count` column the total number of orders created by its order-creation request batch.

#### interchange_err_count

If there were errors in an order-creation batch that resulted in some orders being created but others failing to be created, the total number of failures will be recorded in this `interchange_count_errors` on every order that did get created. Note that this value cannot be recorded on the failed orders because they weren't created and don't exist, heh.






#######################

### t_companies

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
````




### t_ocrvariants

By rights this table would be called `t_datasource_variants` but when it was originally created the only variant type we processed was 'ocr' hence the name. Renaming it is fine if someone wants to do that at some point.

#### variant_type

This indicates what type of variant is being described. Valid values are:

1. NULL (implies 'ocr')
1. ocr
1. edi
1. tabular (includes CSV, XLSX, and possibly HTML tables; any flat colum/row data)

#### classification

JSON data structure: 
```json
{
    "required_header_field_list": [
        "field1",
        "field2",
        "etc."
    ], 
    "forbidden_header_field_list": [
        "fieldA",
        "fieldB",
        "etc."
    ]    
}
```


#### mapping

JSON data structure: 
```pseudo-json
{
    need to define this
    ===================
    list of header=fieldname, 
    list of header=fixedvalue
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
select * from t_ocrvariants where json_contains(t_company_id_list, '2','$');
````

Which will find company_id=2, for example. note that the single-quotes are required for integer values. String values would be searched with embedded double-quotes, like `'"two"'`






#######################

### t_equipment_types

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

