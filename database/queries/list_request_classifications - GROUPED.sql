set @START_DATE=date_sub(CURRENT_TIMESTAMP, interval 192 hour);
set @END_DATE=null;
set @MAX_ORDER_LIST_CHARS=23;
set @SHOW_CMDLINE=1;
set @VARIANTID_REGEX="(?<=\\.v)(\\d{1,6})(?=v\\.[a-zA-Z]{3,4}$)";


select 
     left(created_at, 5) as thedate
    ,class_status
    ,count(*) as thecount
from (
    select
        (case
            when has_postprocessing_error                                                                                                                                                                 then '<exception>'
            when verified_type = ''                                                                                                                                                                       then '<processing>'
            when verified_type = 'pdftext'                                                                                                                                                                then 'D3-CARGOWISE'
            when verified_type = 'tabular'                                                                                                                                                                then 'D3-TABULAR'
            when                                                 lower(download_source)     like '%.v112v.pdf%'                                                                                           then 'D3-MISSING'
            when lower(download_source) not like '%.vv.pdf%' and lower(download_source) not like '%.v112v.pdf%'                                        and verified_id not in ('', classified_variantid)  then 'D3-WRONG'
            when lower(download_source)     like '%.vv.pdf%'                                                    and verified_name     like '%unknown%'                                                    then 'AB-BAD'
            when lower(download_source)     like '%.vv.pdf%'                                                    and verified_name not like '%unknown%'                                                    then 'AB-GOOD'
            else 'D3-GOOD'
        end) as class_status

        ,subq.created_at
        ,if (subq.pages = 'null', '', subq.pages) as pdf_pages
        ,subq.company_name
        ,subq.classified_name_id
        ,if(subq.verified_name = '', '<processing>', subq.verified_name) as verified_name
        ,if(subq.verified_id = '', '<processing>', subq.verified_id) as verified_id
        ,if(subq.verified_type = '', '<processing>', subq.verified_type) as verified_type
        ,if(subq.order_id_list = '', '<processing>', if(length(subq.order_id_list) <= @MAX_ORDER_LIST_CHARS, subq.order_id_list, concat(left(subq.order_id_list, @MAX_ORDER_LIST_CHARS), '... (', subq.order_count, ' orders)' ))) as order_ids
        ,if(@SHOW_CMDLINE, subq.download_source, subq.request_id) as download_source
    from (
        select
             date_format(min((select min(s2.created_at) from t_job_state_changes as s2 where s.request_id = s2.request_id)), '%m/%d %H:%i') as created_at
            ,c.name as company_name
            ,(select json_unquote(json_extract(max(s2.status_metadata), '$.pdf_page_count')) from t_job_state_changes as s2 where s.request_id = s2.request_id and status in ('intake-started', 'intake-accepted') order by id asc limit 1) as pages
            
             -- ,json_unquote(json_extract(min(s.status_metadata), '$.classified_variantid')) as classified_variantid
            ,coalesce(concat(
                (select abbyy_variant_name from t_ocrvariants where deleted_at is null and abbyy_variant_id = (select regexp_substr(json_unquote(json_extract(max(status_metadata), '$.document_archive_location')), @VARIANTID_REGEX) collate utf8mb4_unicode_ci as classified_variantid) order by id desc limit 1)
               , ' ('
               ,(select regexp_substr(json_unquote(json_extract(max(status_metadata), '$.document_archive_location')), @VARIANTID_REGEX)) collate utf8mb4_unicode_ci
               , ')')
               , ''
            ) as classified_name_id
            ,(select regexp_substr(json_unquote(json_extract(max(status_metadata), '$.document_archive_location')), @VARIANTID_REGEX)) collate utf8mb4_unicode_ci as classified_variantid
    
            ,if(count(o.id) = 0, '', coalesce((select variant_type from t_ocrvariants where abbyy_variant_name = min(o.variant_name) and deleted_at is null order by id desc limit 1), 'ocr')) as verified_type
            ,coalesce(min(o.variant_name), '') as verified_name
            ,coalesce((select abbyy_variant_id from t_ocrvariants where abbyy_variant_name = min(o.variant_name) and deleted_at is null order by id desc limit 1), '') as verified_id
    
            ,s.request_id
            ,min(s.id) as t_job_state_changes_id
            ,coalesce(group_concat(distinct o.id order by o.id), '') as order_id_list
            ,count(distinct o.id) as order_count
            ,max(concat('aws --profile dray360-webapp-user-prod s3 cp ', json_extract(status_metadata, '$.document_archive_location'), ' "./', replace(json_unquote(json_extract(status_metadata, '$.original_filename')), '\r\n', ''), '"')) as download_source
            ,coalesce(max(json_unquote(json_extract(ocr_data, '$.original_fields.last_editor.value'))), '') as verifier
            ,exists(select id from t_job_state_changes as s2 where s.request_id = s2.request_id and s2.status = 'ocr-post-processing-error') as has_postprocessing_error
    
        from t_job_state_changes as s
        join t_companies as c on s.company_id = c.id
        left join t_orders as o on s.request_id = o.request_id
        where s.status in ('intake-accepted', 'intake-accepted-datafile')
          and date(s.created_at) >= date(@START_DATE)
          and s.created_at <= coalesce(@END_DATE, CURRENT_TIMESTAMP)
        group by c.name, s.request_id
        order by min(s.id) asc
    ) as subq
    -- order by class_status, tjsc_id
    order by created_at
) as outerq
group by thedate, class_status
;
