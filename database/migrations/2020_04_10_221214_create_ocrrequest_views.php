<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcrrequestViews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // v_status_summary view
        DB::statement("
            create or replace view v_status_summary as
            select
                t_job_state_changes.id as t_job_state_changes_id,
                t_job_latest_state.t_job_state_changes_id is not null as is_latest_status,
                t_job_state_changes.request_id,
                t_job_state_changes.status_date as status_date_utc,
                convert_tz(t_job_state_changes.status_date, 'UTC','US/Central') as status_date_cst,
                t_job_state_changes.status,
                if(status = 'intake-started', concat('source_type: ', json_extract(status_metadata, '$.source_summary.source_type'), ', email_from: ', json_extract(status_metadata, '$.source_summary.source_email_from_address'), ', email_subject: ', json_extract(status_metadata, '$.source_summary.source_email_subject'), ', files: ', json_extract(status_metadata, '$.source_summary.source_email_attachment_filenames')),
                if(status = 'intake-rejected', concat('rejection_reason: ', json_extract(status_metadata, '$.exception_message')),
                if(status = 'intake-accepted', concat('document_filename: ', json_extract(status_metadata, '$.document_filename')),
                if(status = 'ocr-waiting', concat('wait_reason: ', json_extract(status_metadata, '$.wait_reason'), ', exception_message: ', json_extract(status_metadata, '$.exception_message')),
                if(status = 'ocr-completed', concat('file_list: ', json_extract(status_metadata, '$.file_list')),
                if(status = 'process-ocr-output-file-error', concat('filename: ', json_extract(status_metadata, '$.filename'), ', exception_type: ', json_extract(status_metadata, '$.exception_type'), ', exception_message: ', json_extract(status_metadata, '$.exception_message')),
                if(status = 'process-ocr-output-file-complete', concat('filename: ', json_extract(status_metadata, '$.filename'), ', row_count: ', json_extract(status_metadata, '$.row_count')),
                if(status = 'ocr-post-processing-error', concat('exception_message: ', json_extract(status_metadata, '$.exception_message'), ', files: ', json_extract(status_metadata, '$.num_files_to_process'), ', success: ', json_extract(status_metadata, '$.num_files_processed_successfully'), ', fail: ', json_extract(status_metadata, '$.num_files_processed_unsuccessfully')),
                if(status = 'ocr-post-processing-complete', concat('num_files_processed: ', json_extract(status_metadata, '$.num_files_to_process')),
                if(status = 'upload-requested', concat('original_filename: ', json_extract(status_metadata, '$.original_filename'), ', uploading_filename: ', json_extract(status_metadata, '$.uploading_filename')),
                'no summary available for this status - see the programmer'
                )))))))))) as status_summary, -- put here as many closing-parenthesis as there are if-statements
                status_metadata as status_metadata
            from t_job_state_changes
            left join t_job_latest_state on t_job_state_changes.id = t_job_latest_state.t_job_state_changes_id
            order by t_job_state_changes.request_id, t_job_state_changes.created_at
        ");

        // v_status_intake_started_detail view
        DB::statement("
            create or replace view v_status_intake_started_detail as
            select
                t_job_state_changes.id as t_job_state_changes_id,
                t_job_state_changes.request_id,
                convert_tz(t_job_state_changes.status_date, 'UTC','US/Central') as status_CST,
                t_job_state_changes.status,

                json_extract(status_metadata, '$.request_id') as metadata_request_id,
                json_extract(status_metadata, '$.read_log_commandline') as metadata_read_log_commandline,

                json_extract(status_metadata, '$.source_summary.source_type') as metadata_source_type,
                json_extract(status_metadata, '$.source_summary.source_email_subject') as metadata_source_email_subject,
                json_extract(status_metadata, '$.source_summary.source_email_to_address') as metadata_source_email_to_address,
                json_extract(status_metadata, '$.source_summary.source_email_from_address') as metadata_source_email_from_address,
                json_extract(status_metadata, '$.source_summary.source_email_body_prefixes') as metadata_source_email_body_prefixes,
                json_extract(status_metadata, '$.source_summary.source_email_string_length') as metadata_source_email_string_length,
                json_extract(status_metadata, '$.source_summary.source_email_attachment_filenames') as metadata_source_email_attachment_filenames,

                json_extract(status_metadata, '$.event_info.bucket_name') as metadata_bucket_name,
                json_extract(status_metadata, '$.event_info.object_key') as metadata_object_key,
                json_extract(status_metadata, '$.event_info.event_time') as metadata_event_time,
                json_extract(status_metadata, '$.event_info.aws_request_id') as metadata_aws_request_id,
                json_extract(status_metadata, '$.event_info.log_group_name') as metadata_log_group_name,
                json_extract(status_metadata, '$.event_info.log_stream_name') as metadata_log_stream_name,
                json_extract(status_metadata, '$.event_info.event_time_epoch_ms') as metadata_event_time_epoch_ms

            from t_job_state_changes
            where status = 'intake-started'
            order by request_id
        ");

        // v_status_intake_rejected_detail view
        DB::statement("
            create or replace view v_status_intake_rejected_detail as
            select
                t_job_state_changes.id as t_job_state_changes_id,
                t_job_state_changes.request_id,
                convert_tz(t_job_state_changes.status_date, 'UTC','US/Central') as status_CST,
                t_job_state_changes.status,

                json_extract(status_metadata, '$.rejection_reason') as metadata_rejection_reason,
                json_extract(status_metadata, '$.exception_message') as metadata_exception_message

            from t_job_state_changes
            where status = 'intake-rejected'
            order by request_id
        ");



        // v_status_intake_accepted_detail view
        DB::statement("
            create or replace view v_status_intake_accepted_detail as
            select
                t_job_state_changes.id as t_job_state_changes_id,
                t_job_state_changes.request_id,
                convert_tz(t_job_state_changes.status_date, 'UTC','US/Central') as status_CST,
                t_job_state_changes.status,

                json_extract(status_metadata, '$.document_type') as metadata_document_type,
                json_extract(status_metadata, '$.document_filename') as metadata_document_filename

            from t_job_state_changes
            where status = 'intake-accepted'
            order by request_id
        ");


        // v_all_incomplete_jobs view
        DB::statement("
            create or replace view v_all_incomplete_jobs as
            select
                t_job_state_changes.id as t_job_state_changes_id,
                t_job_state_changes.request_id as request_id,
                convert_tz(t_job_state_changes.created_at, 'UTC','US/Central') as created_CST,
                convert_tz(t_job_state_changes.status_date, 'UTC','US/Central') as status_CST,
                t_job_state_changes.status as status,
                v_status_summary.status_summary
            from t_job_latest_state
            join t_job_state_changes on t_job_latest_state.t_job_state_changes_id = t_job_state_changes.id
            join v_status_summary on t_job_state_changes.id = v_status_summary.t_job_state_changes_id
            where t_job_state_changes.status <> 'ocr-post-processing-complete'
            order by t_job_latest_state.created_at
        ");

        // v_status_ocr_post_processing_error_detail view
        DB::statement("
            create or replace view v_status_ocr_post_processing_error_detail as
            select
                t_job_state_changes.id as t_job_state_changes_id,
                t_job_state_changes.request_id,
                convert_tz(t_job_state_changes.status_date, 'UTC','US/Central') as status_CST,
                t_job_state_changes.status,

                json_extract(status_metadata, '$.exception_type') as metadata_exception_type,
                json_extract(status_metadata, '$.exception_message') as metadata_exception_message,
                json_extract(status_metadata, '$.num_files_to_process') as metadata_num_files_to_process,
                json_extract(status_metadata, '$.num_files_processed_successfully') as metadata_num_files_processed_successfully,
                json_extract(status_metadata, '$.num_files_processed_unsuccessfully') as metadata_num_files_processed_unsuccessfully

            from t_job_state_changes
            where status = 'ocr-post-processing-error'
            order by request_id
        ");

        // v_status_ocr_post_processing_complete_detail view
        DB::statement("
            create or replace view v_status_ocr_post_processing_complete_detail as
            select
                t_job_state_changes.id as t_job_state_changes_id,
                t_job_state_changes.request_id,
                convert_tz(t_job_state_changes.status_date, 'UTC','US/Central') as status_CST,
                t_job_state_changes.status,

                json_extract(status_metadata, '$.num_files_to_process') as metadata_num_files_to_process,
                json_extract(status_metadata, '$.num_files_processed_successfully') as metadata_num_files_processed_successfully,
                json_extract(status_metadata, '$.num_files_processed_unsuccessfully') as metadata_num_files_processed_unsuccessfully

            from t_job_state_changes
            where status = 'ocr-post-processing-complete'
            order by request_id
        ");


        // v_status_ocr_waiting_detail view
        DB::statement("
            create or replace view v_status_ocr_waiting_detail as
            select
                t_job_state_changes.id as t_job_state_changes_id,
                t_job_state_changes.request_id,
                convert_tz(t_job_state_changes.status_date, 'UTC','US/Central') as status_CST,
                t_job_state_changes.status,

                json_extract(status_metadata, '$.wait_reason') as metadata_wait_reason,
                json_extract(status_metadata, '$.exception_message') as metadata_exception_message

            from t_job_state_changes
            where status = 'ocr-waiting'
            order by request_id
        ");


        // v_status_ocr_completed_detail view
        DB::statement("
            create or replace view v_status_ocr_completed_detail as
            select
                t_job_state_changes.id as t_job_state_changes_id,
                t_job_state_changes.request_id,
                convert_tz(t_job_state_changes.status_date, 'UTC','US/Central') as status_CST,
                t_job_state_changes.status,

                json_extract(status_metadata, '$.s3_bucket') as metadata_s3_bucket,
                json_extract(status_metadata, '$.s3_region') as metadata_s3_region,
                json_extract(status_metadata, '$.file_list') as metadata_file_list

            from t_job_state_changes
            where status = 'ocr-completed'
            order by request_id
        ");


        // v_status_process_ocr_output_file_error_detail view
        DB::statement("
            create or replace view v_status_process_ocr_output_file_error_detail as
            select
                t_job_state_changes.id as t_job_state_changes_id,
                t_job_state_changes.request_id,
                convert_tz(t_job_state_changes.status_date, 'UTC','US/Central') as status_CST,
                t_job_state_changes.status,

                json_extract(status_metadata, '$.filename') as metadata_filename,
                json_extract(status_metadata, '$.row_count') as metadata_row_count,
                json_extract(status_metadata, '$.exception_type') as metadata_exception_type,
                json_extract(status_metadata, '$.exception_message') as metadata_exception_message

            from t_job_state_changes
            where status = 'process-ocr-output-file-error'
            order by request_id
        ");

        // v_status_process_ocr_output_file_complete_detail view
        DB::statement("
            create or replace view v_status_process_ocr_output_file_complete_detail as
            select
                t_job_state_changes.id as t_job_state_changes_id,
                t_job_state_changes.request_id,
                convert_tz(t_job_state_changes.status_date, 'UTC','US/Central') as status_CST,
                t_job_state_changes.status,

                json_extract(status_metadata, '$.filename') as metadata_filename,
                json_extract(status_metadata, '$.row_count') as metadata_row_count

            from t_job_state_changes
            where status = 'process-ocr-output-file-complete'
            order by request_id
        ");


        // END OF VIEWS CREATION
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('drop view v_status_process_ocr_output_file_complete_detail');
        DB::statement('drop view v_status_process_ocr_output_file_error_detail');
        DB::statement('drop view v_status_ocr_completed_detail');
        DB::statement('drop view v_status_ocr_waiting_detail');
        DB::statement('drop view v_status_ocr_post_processing_complete_detail');
        DB::statement('drop view v_status_ocr_post_processing_error_detail');
        DB::statement('drop view v_all_incomplete_jobs');
        DB::statement('drop view v_status_intake_accepted_detail');
        DB::statement('drop view v_status_intake_rejected_detail');
        DB::statement('drop view v_status_intake_started_detail');
        DB::statement('drop view v_status_summary');
    }
}
