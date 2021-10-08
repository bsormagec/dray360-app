set @START_DATE=date_sub(CURRENT_TIMESTAMP, interval 25 hour);
set @END_DATE=null;

select
     ls.request_id
    ,ls.created_at
    ,jsc.status as latest_status
    ,(select c.name from t_job_state_changes as s join t_companies as c on c.id = s.company_id where ls.t_job_state_changes_id = s.id) as company

    ,if(exists(select id from t_job_state_changes as s where s.request_id = ls.request_id and s.status = 'intake-rejected'), 'rejected', 
        coalesce((
            select
                coalesce((select variant_type from t_ocrvariants where abbyy_variant_name = min(o.variant_name) and deleted_at is null order by id desc limit 1), 'ocr') as verified_type
            from t_orders as o
            where o.request_id = ls.request_id
            group by o.request_id
        ), '')
    ) as document_type

    ,coalesce(nullif((select json_unquote(json_extract(s.status_metadata, '$.pdf_page_count')) from t_job_state_changes as s where ls.request_id = s.request_id and status = 'intake-started' order by s.id asc limit 1), 'null'), '') as pages

    ,coalesce((
        select concat(
             coalesce(
                  (select abbyy_variant_name from t_ocrvariants where deleted_at is null and abbyy_variant_id = (select json_unquote(json_extract(s.status_metadata, '$.classified_variantid'))) order by id desc limit 1)
                 ,(select json_unquote(json_extract(s.status_metadata, '$.variant_name')))
             )
            ,if((select json_unquote(json_extract(s.status_metadata, '$.classified_variantid'))) = 'null', '', concat(
                 ' ('
                ,(select json_unquote(json_extract(s.status_metadata, '$.classified_variantid')))
                ,')'
            ))
        ) as classified_name_id
        from t_job_state_changes as s where s.request_id = ls.request_id and s.status in ('intake-accepted','intake-accepted-datafile')
        order by s.id
        limit 1
    ), '') as classified_name_id

    ,coalesce((select o.variant_name from t_orders as o where o.request_id = ls.request_id order by o.id asc limit 1), '') as verified_name
    ,exists(select s.id from t_job_state_changes as s where s.request_id = ls.request_id and status = 'ocr-waiting') as sent_to_abbyy

    ,not exists(select s.id from t_job_state_changes as s where s.request_id = ls.request_id and status in ('ocr-post-processing-review','process-ocr-output-file-review')) 
     and not exists(select id from t_job_state_changes as s where s.request_id = ls.request_id and s.status = 'intake-rejected') as skipped_admin_review

    ,exists(select s.id from t_job_state_changes as s where s.request_id = ls.request_id and status in ('auto-sending-to-wint','auto-sending-to-chainio','auto-sending-to-compcare')) as auto_send_to_tms
    ,(select count(id) from t_orders as o where o.request_id = ls.request_id and o.deleted_at is not null) deleted_orders
    ,(select count(o.id) from t_orders as o where ls.request_id = o.request_id and o.deleted_at is null) as orders
    ,(select count(o.tms_shipment_id) from t_orders as o where ls.request_id = o.request_id and o.deleted_at is null) as shipments
    ,(ls.deleted_at is not null) as request_deleted
    ,exists(select id from t_job_state_changes as s where s.request_id = ls.request_id and s.status = 'intake-rejected') as rejected
    ,coalesce((select group_concat(o.id) from t_orders as o where ls.request_id = o.request_id and o.deleted_at is null), '') as order_ids
    ,coalesce((select group_concat(o.tms_shipment_id) from t_orders as o where ls.request_id = o.request_id and o.deleted_at is null), '') as shipment_ids

    ,coalesce(TIMEDIFF(
         (select min(created_at) from t_job_state_changes as s where s.request_id = ls.request_id and s.status = 'ocr-completed')
        ,(select min(created_at) from t_job_state_changes as s where s.request_id = ls.request_id and s.status = 'ocr-waiting')
    ),'') as time_abbyy

    -- # Timestamps for debugging date computations, leave commented out for production
    -- ,(select min(created_at) from t_job_state_changes as s where s.request_id = ls.request_id and s.status = 'ocr-post-processing-review') as timestamp_admin_review_queued
    -- ,(select min(a.created_at) from audits as a join t_orders as o on (a.auditable_id = o.id and a.auditable_type = 'App\\Models\\Order' and o.request_id = ls.request_id)) as timestamp_admin_review_edit_started
    -- ,(select min(created_at) from t_job_state_changes as s where s.request_id = ls.request_id and s.status = 'ocr-post-processing-complete') as timestamp_admin_review_completed

    ,coalesce(TIMEDIFF(
         (select min(a.created_at) from audits as a join t_orders as o on (a.auditable_id = o.id and a.auditable_type = 'App\\Models\\Order' and o.request_id = ls.request_id))
        ,(select min(s.created_at) from t_job_state_changes as s where s.request_id = ls.request_id and s.status = 'ocr-post-processing-review')
    ),'') as time_admin_review_queued

    ,if ((select min(created_at) from t_job_state_changes as s where s.request_id = ls.request_id and s.status = 'ocr-post-processing-review') is null, ''
        ,coalesce(TIMEDIFF(
             (select min(created_at) from t_job_state_changes as s where s.request_id = ls.request_id and s.status = 'ocr-post-processing-complete')
            ,(select min(a.created_at) from audits as a join t_orders as o on (a.auditable_id = o.id and a.auditable_type = 'App\\Models\\Order' and o.request_id = ls.request_id))
        ),'')) as time_admin_review_editing

    ,coalesce(TIMEDIFF(
         (select min(created_at) from t_job_state_changes as s where s.request_id = ls.request_id and s.status = 'ocr-post-processing-complete')
        ,(select min(created_at) from t_job_state_changes as s where s.request_id = ls.request_id and s.status = 'ocr-post-processing-review')
    ),'') as time_admin_review_total

    ,coalesce((select group_concat(distinct reviewer) from (
        select u.name as reviewer
            from t_orders as o
            join audits as a on a.auditable_id = o.id and a.auditable_type = 'App\\Models\\Order'
            join users as u on u.id = a.user_id
            join role_user as ru on ru.user_id = u.id
            join roles as r on r.id = ru.role_id and r.name = 'order-review'
            where o.request_id = ls.request_id
        union select u.name as reviewer
            from t_order_address_events as oae
            join audits as a on a.auditable_id = oae.id and a.auditable_type = 'App\\Models\\OrderAddressEvent'
            join t_orders as o on o.id = oae.t_order_id and o.request_id = ls.request_id
            join users as u on u.id = a.user_id
            join role_user as ru on ru.user_id = u.id
            join roles as r on r.id = ru.role_id and r.name = 'order-review'
        union select u.name as reviewer
            from t_order_line_items as oli
            join audits as a on a.auditable_id = oli.id and a.auditable_type = 'App\\Models\\OrderLineItem'
            join t_orders as o on o.id = oli.t_order_id and o.request_id = ls.request_id
            join users as u on u.id = a.user_id
            join role_user as ru on ru.user_id = u.id
            join roles as r on r.id = ru.role_id and r.name = 'order-review'
        union select u.name as reviewer
            from users as u where u.id = (select json_extract(s.status_metadata, '$.user_id') as user_id from t_job_state_changes as s where ls.request_id = s.request_id and s.status = 'ocr-post-processing-complete' order by id asc limit 1)
    ) as reviewers), '') as admin_reviewers

    ,left(coalesce(TIMEDIFF(
         (select nullif(max(coalesce(tms_submission_datetime, '1900-12-31')), '1900-12-31') from t_orders as o where o.request_id = ls.request_id and o.deleted_at is null)
        ,(select min(created_at) from t_job_state_changes as s where s.request_id = ls.request_id and s.status = 'ocr-post-processing-complete')
    ),''), 8) as time_client_review_total

    ,if(exists(select id from t_job_state_changes as s where s.request_id = ls.request_id and s.status = 'intake-rejected'), '', (
        (select coalesce(sum(if(json_length(a.old_values) = 0, json_length(a.new_values), json_length(a.old_values))), 0)
            from audits as a
            join users as u on u.id = a.user_id
            join role_user as ru on ru.user_id = u.id
            join roles as r on r.id = ru.role_id and r.name = 'order-review'
            join t_orders as o on o.id = a.auditable_id and o.request_id = ls.request_id
            where a.auditable_type = 'App\\Models\\Order'
        )
        +
        (select coalesce(sum(if(json_length(a.old_values) = 0, json_length(a.new_values), json_length(a.old_values))), 0)
            from audits as a
            join users as u on u.id = a.user_id
            join role_user as ru on ru.user_id = u.id
            join roles as r on r.id = ru.role_id and r.name = 'order-review'
            join t_order_line_items as oli on a.auditable_id = oli.id
            join t_orders as o on o.id = oli.t_order_id and o.request_id = ls.request_id
            where a.auditable_type = 'App\\Models\\OrderLineItem'
        )
        +
        (select coalesce(sum(if(json_length(a.old_values) = 0, json_length(a.new_values), json_length(a.old_values))),0)
            from audits as a
            join users as u on u.id = a.user_id
            join role_user as ru on ru.user_id = u.id
            join roles as r on r.id = ru.role_id and r.name = 'order-review'
            join t_order_address_events as oav on a.auditable_id = oav.id
            join t_orders as o on o.id = oav.t_order_id and o.request_id = ls.request_id
            where a.auditable_type = 'App\\Models\\OrderAddressEvent'
        )
    )) as admin_review_changes

    ,if(exists(select id from t_job_state_changes as s where s.request_id = ls.request_id and s.status = 'intake-rejected'), '', (
        (select coalesce(sum(if(json_length(a.old_values) = 0, json_length(a.new_values), json_length(a.old_values))), 0)
            from audits as a
            join t_orders as o on o.id = a.auditable_id and o.request_id = ls.request_id
            join users as u on u.id = a.user_id and u.t_company_id = o.t_company_id
            where a.auditable_type = 'App\\Models\\Order'
        )
        +
        (select coalesce(sum(if(json_length(a.old_values) = 0, json_length(a.new_values), json_length(a.old_values))), 0)
            from audits as a
            join t_order_line_items as oli on a.auditable_id = oli.id
            join t_orders as o on o.id = oli.t_order_id and o.request_id = ls.request_id
            join users as u on u.id = a.user_id and u.t_company_id = o.t_company_id
            where a.auditable_type = 'App\\Models\\OrderLineItem'
        )
        +
        (select coalesce(sum(if(json_length(a.old_values) = 0, json_length(a.new_values), json_length(a.old_values))),0)
            from audits as a
            join t_order_address_events as oav on a.auditable_id = oav.id
            join t_orders as o on o.id = oav.t_order_id and o.request_id = ls.request_id
            join users as u on u.id = a.user_id and u.t_company_id = o.t_company_id
            where a.auditable_type = 'App\\Models\\OrderAddressEvent'
        )
    )) as client_changes

from t_job_latest_state as ls
join t_job_state_changes as jsc on ls.t_job_state_changes_id = jsc.id
where ls.order_id is null
  and ls.created_at >= @START_DATE
  and ls.created_at <= coalesce(@END_DATE, CURRENT_TIMESTAMP)
  and jsc.status <> 'intake-file-ingestion'

order by document_type, ls.id asc
;
