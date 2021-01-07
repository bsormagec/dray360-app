# Status and Status-Metadata




Not that full detalI will be updating this document to record all possible states here. It is currently documented in a Draw.io diagram and not very accessible.


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

New statuses for Chainio (cargowise) as of 1/6/2021

1. sending-to-chainio
1. 





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





#### `ocr-timedout` status_metadata

* created by `./processingqueuemonitor/processingqueuemonitor.py`
* triggered by retry of `intake-accepted` message after <N> times, see:
  - dray360-deploy:/cloudformation/templates/microservices.yml SQSOCRProcessingMonitorQueue.maxReceiveCount

1. message
1. receive_count, should be set to visibilitytimeout + 1
1. resurrection_count, should be set to max_resurrection_count




#### `ocr-completed` status_metadata

* created by `./processingqueuemonitor/processingqueuemonitor.py`
* triggered by retry of `intake-accepted`

1. s3_bucket: where output files are stored
1. s3_folder: folder in that bucket
1. s3_region: where output files are stored
1. file_list: array of files matching `<requestid>_<sha256>_*`




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



#### `sending-to-wint` status_metadata

* created by `./Http/Controllers/Api/SendToTmsController.php` when user selects [Send to TMS] option for Profit Tools orders

1. order_id
1. company_id
1. tms_provider_id (must be 1, i.e. Profit Tools)
1. user_id



#### `sending-to-chainio` status_metadata

* created by `./Http/Controllers/Api/SendToTmsController.php` when user selects [Send to TMS] option for Compcare orders

1. order_id
1. company_id
1. tms_provider_id (must be 2, i.e. Compcare)
1. user_id

#### `success-sending-to-chainio` status_metadata

* created by `./submitchainio/submitchainio.py`

1. chainio_status
1. chainio_data
1. event_info

#### `failure-sending-to-chainio`

* created by `./submitchainio/submitchainio.py`

1. exception_message
1. event_info

#### `shipment-created-by-chainio` and `shipment-not-created-by-chainio` status_metadata

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







#### `shipment-created-by-wint` and `shipment-updated-by-wint` status_metadata

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
