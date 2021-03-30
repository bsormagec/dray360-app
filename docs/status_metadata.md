
# Status and Status-MetaData

I will be updating this document to record all possible states here. It is currently documented [(here, copy link location)](https://raw.githubusercontent.com/tcompanies/dray360-microservices/master/docs/abbyy_arch3.xml?token=ABZX3DUKMXGNXQKEO43RIC3ACM734) and opened in [Draw.io](https://app.diagrams.net) using File/OpenFrom/URL.

Each status is very carefully defined, here is a complete list (as of 11/27/2020):


| Code module | Triggered by status | Issues statuses |
| :---------- | :------------------ | :-------------- |
| app/Http/Controllers/Api/SendToTmsController.php <br> _(laravel-api controller)_ | _user clicks [Send to TMS] button_ | [`sending-to-wint`](./status_metadata.md#sending-to-wint-status_metadata) <br> [`sending-to-chainio`](./status_metadata.md#sending-to-chainio-status_metadata) |
| processonefile.py | _order is auto-submitted_ | `auto-sending-to-wint` <br> `auto-sending-to-chainio` |
| app/Http/Controllers/Api/ReplicateOrdersController.php <br> _(laravel-api controller)_ | _user clicks [Replicate Order] button_ | [`replicated-from-existing-order`](./status_metadata.md#replicated-from-existing-order-status_metadata) |
| postprocessingqueue.py | `ocr-completed` | [`ocr-post-processing-error`](./status_metadata.md#ocr-post-processing-error-status_metadata) <br> [`ocr-post-processing-review`](./status_metadata.md#ocr-post-processing-complete-and-ocr-post-processing-review-status_metadata)  <br> [`ocr-post-processing-complete`](./status_metadata.md#ocr-post-processing-complete-and-ocr-post-processing-review-status_metadata) |
| processonefile.py | `ocr-completed` <br> _called by postprocessingqueue.py_ | [`process-ocr-output-file-error`](./status_metadata.md#process-ocr-output-file-error-status_metadata) <br> [`process-ocr-output-file-review`](./status_metadata.md#process-ocr-output-file-complete-and-process-ocr-output-file-review-status_metadata) <br> [`process-ocr-output-file-complete`](./status_metadata.md#process-ocr-output-file-complete-and-process-ocr-output-file-review-status_metadata)|
| need | need | `failure-imageuploding-to-blackfl` |
| need | need | `failure-sending-to-wint` |
| `./intakefilter/intakefilter.py` | _user emails PDF, or_<br>_uploads document_ | `intake-accepted-datafile` |
| `./intakefilter/intakefilter.py` | _user emails PDF, or_<br>_uploads document_ | `intake-accepted` |
| `./intakefilter/intakefilter.py` | _user emails PDF, or_<br>_uploads document_ | `intake-exception` |
| `./intakefilter/intakefilter.py` | _user emails PDF, or_<br>_uploads document_ | `intake-rejected` |
| `./intakefilter/intakefilter.py` | _user emails PDF, or_<br>_uploads document_ | `intake-started` |
| `./intakefilter/intakefilter.py` | _file-ingestion email received_ | `intake-file-ingestion` |
| need | need | `ocr-timedout` |
| need | need | `ocr-waiting` |
| need | need | `shipment-created-by-wint` |
| need | need | `shipment-not-created-by-wint` |
| need | need | `shipment-not-updated-by-wint` |
| need | need | `shipment-updated-by-wint` |
| need | need | `success-imageuploding-to-blackfl` |
| need | need | `success-sending-to-wint` |
| need | need | `success-updating-to-wint` |
| need | need | `untried-imageuploding-to-blackfl` |
| need | need | `updated-by-subsequent-order` |
| need | need | `updates-prior-order` |
| need | need | `upload-requested` |
| _app/Http/Controllers/Api/OcrRequestDoneStatusController(laravel-api controller)_ | _user clicks [Mark as done]_ | [`request-marked-done`](./status_metadata.md#request-marked-done-and-request-marked-undone-status-metadata) |
| _app/Http/Controllers/Api/OcrRequestDoneStatusController(laravel-api controller)_ | _user clicks [Mark as undone]_ | [`request-marked-undone`](./status_metadata.md#request-marked-done-and-request-marked-undone-status-metadata) |





<br>

-----

### `ALL STATUSES` 

All status_metadata objects share the following common properties:

1. request_id
1. datetime_utciso
1. company_id
1. order_id (if available)




<br>

-----

### `process-ocr-output-file-error`

1. exception_type
1. exception_message
1. file_list
1. field_count
1. orderitem_sequence
1. order_id_list
1. order_id



<br>

-----

### `process-ocr-output-file-complete` and `process-ocr-output-file-review`

For orders whose variant_name `t_ocrvariants` and `company_id` is found in `t_companies.abbyy_variant_name` and `t_companies.admin_review_company_id_list` respectively.

1. file_list
1. field_count
1. orderitem_sequence
1. order_id_list
1. order_id



<br>

-----

### `ocr-waiting`

* created by `./processingqueuemonitor/processingqueuemonitor.py`
* triggered by retry of `intake-accepted`

1. wait_reason
1. exception_message




<br>

-----

### `ocr-timedout`

* created by `./processingqueuemonitor/processingqueuemonitor.py`
* triggered by retry of `intake-accepted` message after <N> times, see:
  - dray360-deploy:/cloudformation/templates/microservices.yml SQSOCRProcessingMonitorQueue.maxReceiveCount

1. message
1. receive_count, should be set to visibilitytimeout + 1
1. resurrection_count, should be set to max_resurrection_count



<br>

-----

### `ocr-completed`

* created by `./processingqueuemonitor/processingqueuemonitor.py`
* triggered by retry of `intake-accepted`

1. s3_bucket: where output files are stored
1. s3_folder: folder in that bucket
1. s3_region: where output files are stored
1. file_list: array of files matching `<requestid>_<sha256>_*`



<br>

-----

### `failure-imageuploding-to-blackfl`

* created by `./wintupdater/imageuploader.py`

1. message: 'exception sending image to profittools'
1. exception: exception message



<br>

-----

### `success-imageuploding-to-blackfl`

* created by `./wintupdater/imageuploader.py`

1. imagetype: (blackfly image type, e.g. PRENOTE or DELIVERYORDER)
1. jpg_file_list: all individual files aggregated into a multi-page TIFF and uploaded as a single image



<br>

-----

### `untried-imageuploding-to-blackfl`

* created by `./wintupdater/imageuploader.py`

1. 'message': 'no image files, blackfly credentials or tms_shipment_id found'



<br>

-----

### `sending-to-wint` and `auto-sending-to-wint` 

* created by `./Http/Controllers/Api/SendToTmsController.php` when user selects [Send to TMS] option for Profit Tools orders

1. order_id
1. company_id
1. tms_provider_id (must be 1, i.e. Profit Tools)
1. user_id



<br>

-----

### `sending-to-chainio` and `auto-sending-to-chainio`

* created by `./app/Http/Controllers/Api/SendToTmsController.php` when user selects [Send to TMS] option for Compcare orders

1. order_id
1. company_id
1. tms_provider_id (must be 2, i.e. Compcare)
1. user_id



<br>

-----

### `replicated-from-existing-order`

* created by `./app/Http/Controllers/Api/ReplicateOrdersController.php` when user selects [Replicate Order] menu option

1. user_id
1. source_order_id



<br>

-----

### `success-sending-to-chainio`

* created by `./submitchainio/submitchainio.py`

1. chainio_status
1. chainio_data
1. event_info



<br>

-----

### `failure-sending-to-chainio`

* created by `./submitchainio/submitchainio.py`

1. exception_message
1. event_info



<br>

-----

### `shipment-created-by-chainio` and `shipment-not-created-by-chainio`

* created by `./apiinterface/function_compcarecallback.py`

1. message
1. request_id
1. order_id
1. company_id
1. tms_shipment_id
1. shipment_number
1. cw_shipment_number
1. booking_ref
1. request_body



<br>

-----

### `ocr-post-processing-error`

1. exception_type
1. exception_message
1. num_files_to_process
1. num_files_processed_successfully
1. num_files_processed_unsuccessfully
1. file_list
1. order_id_list (all ids, even failures)
1. order_id_success_list
1. order_id_failure_list
1. orderitem_success_list  (e.g. row numbers)
1. orderitem_failure_list




<br>

-----

### `ocr-post-processing-complete` and `ocr-post-processing-review`

1. num_files_to_process
1. num_files_processed_successfully
1. num_files_processed_unsuccessfully
1. file_list
1. order_id_list (all ids, even failures)
1. order_id_success_list
1. order_id_failure_list
1. orderitem_success_list  (e.g. row numbers)
1. orderitem_failure_list




<br>

-----

### `shipment-created-by-wint` and `shipment-updated-by-wint`

* created by `./submitwint/submitwint.py`

1. tms_shipment_id (if trigger 'success-sending-to-wint')
1. message
1. event_info
   - request_id
   - company_id
   - order_id
   - wint_request_id_list
   - tms_provider_id
   - eventSourceARN
1. ripcms_status *
   - error_list[]
   - waiting_list[]
   - success_list[]

* where each xyz_list[] array contains zero or more of these objects:

1. wint_request_id
1. message
1. errors
1. current_status: {
   - SH_REQUESTID
   - PTI_AM_ID
   - PTI_AB_ID
   - DS_ID
   - REQUESTSTATUS
   - ENTEREDDATETIME
   - ERRORS
   }



<br>

-----

#### `status_metadata.order_id_list` (ocr-post-pro cessing-error and ocr-post-processing-complete)

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

<br>

-----
#### status_metadata.file_list (ocr-post-processing-error and ocr-post-processing-complete)

Every file processed in this request. If the list grows too large that it cannot be stored in a single SNS message (256KB max) then it will be truncated by deleting the largest element (and in case of a tie for largest, one will be picked at random) until the total size is under 256KB.




<br>

-----
### `request-marked-done` and `request-marked-undone`
1. user_id




<br>

-----
### `intake-started`

* created by `./intakefilter/intakefilter.py`
* triggered by S3 bucket file creaation, from SES email receipt or manual upload

1. source_summary:
   - ..............
   - source_type: "email"
   - source_email_subject
   - source_email_to_address
   - source_email_from_address
   - source_email_body_prefixes
   - source_email_string_length
   - source_email_attachment_filenames
   - source_email_recipient0  (i.e. aws "to" address)
   - source_email_recipient1  up to 4 total "to" addresses, 
   - source_email_recipient2  but I'm not sure if these are 
   - source_email_recipient3  ever populated with anything
   - ..............
   - source_type: "upload"
   - source_upload_filename
   - source_upload_api_request_id
1. event_info:
   - company_id
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
1. pdf_page_count (if type is pdf)



<br>

-----

### `intake-accepted`

* created by `./intakefilter/intakefilter.py`
* triggered by successful parsing of the intake message, the culmination of the intake-started process.

1. document_type: pdf | image | datafile
1. document_filename
1. archive_location
1. original_filename
1. variant_name (if specified)
1. resurrection_count, when original visibilitytimeout has expired (currrenty 1000), system will set this property (or increment it) and resubmit the event for another visibilitytimeout retries, up to MAX_RESURRECTION_COUNT (currently 5).



<br>

-----

### `intake-accepted-datafile`

* created by `./intakefilter/intakefilter.py`

1. file_list: array of files matching <requestid>_<sha256>_*
1. s3_bucket
1. s3_folder
1. s3_region
1. variant_name
1. document_type (always "datafile")
1. document_filename
1. original_filename
1. document_archive_location




<br>

-----

### `intake-rejected` and `intake-exception`

1. rejection_reason
1. exception_message
1. read_log_commandline
1. bucket_name
1. key_name
1. traceback (if intake-exception)





<br>

-----

### `intake-file-ingestion`

* created by `./intakefilter/intakefilter.py`

1. ingestion_type
1. original_filename
1. s3_bucket
1. s3_key
