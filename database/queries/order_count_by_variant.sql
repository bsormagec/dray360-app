select
     v.abbyy_variant_id                            as variant_id
    ,date_format(v.created_at, '%Y-%m-%d')         as variant_created
    ,if(v.deleted_at is not null, 'deleted', '')   as variant_status
    ,v.abbyy_variant_name                          as variant_name

    ,coalesce(    sum(c.name not like '%demo%' and c.name not like '%onboard%' and true                       )                     ,  0) as order_count
    ,coalesce(    sum(c.name not like '%demo%' and c.name not like '%onboard%' and tms_shipment_id is not null)                     ,  0) as shipment_count
    ,coalesce( if(sum(c.name not like '%demo%' and c.name not like '%onboard%') = 0, '', date_format(min(o.created_at), '%Y-%m-%d')), '') as first_order_date
    ,coalesce( if(sum(c.name not like '%demo%' and c.name not like '%onboard%') = 0, '', date_format(max(o.created_at), '%Y-%m-%d')), '') as last_order_date

from t_ocrvariants as v
left join t_orders as o on o.variant_id = v.abbyy_variant_id
left join t_companies as c on o.t_company_id = c.id
where coalesce(v.variant_type, 'ocr') = 'ocr'
  and v.abbyy_label1 is not null
group by v.abbyy_variant_id, v.abbyy_variant_name, v.deleted_at
order by shipment_count, order_count, v.abbyy_variant_id
;
