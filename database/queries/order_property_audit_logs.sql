-- set @D3PROP='master_bol_mawb';
-- set @D3PROP='unit_number';
-- set @D3PROP='booking_number';
-- set @D3PROP='reference_number';
-- set @D3PROP='purchase_order_number';
-- set @D3PROP='house_bol_hawb';

set @D3PROP='reference_number';
set @START_DATE='2021-09-01';
set @DATE_FORMAT='%m/%d %H:%i';

select
     @D3PROP as property_name
    ,replace(json_unquote(json_extract(old_values, concat('$.', @D3PROP))), '\n', '') as old_value
    ,replace(json_unquote(json_extract(new_values, concat('$.', @D3PROP))), '\n', '') as new_value
    ,c.name as company_name
    ,o.request_id as request_id
    ,o.id as order_id
    ,date_format(o.created_at, @DATE_FORMAT) as order_date
    ,json_unquote(json_extract(o.ocr_data, '$.fields.last_editor.value')) as verifier
    ,coalesce(o.variant_id, '') as variant_id
    ,left(o.variant_name, 30) as variant_name
    ,a.id as audit_id
    ,date_format(a.created_at, @DATE_FORMAT) as edit_date
    ,concat('(', u.id, ') ', r.name, ' ', u.name) as editor
from audits as a
join t_orders as o on a.auditable_id = o.id
join t_companies as c on o.t_company_id = c.id
join users as u on a.user_id = u.id
join role_user as ru on u.id = ru.user_id
join roles as r on ru.role_id = r.id
where true
  and a.auditable_type = 'App\\Models\\Order'
  and a.new_values like concat('%', @D3PROP, '%')
  and a.old_values like concat('%', @D3PROP, '%')
  and json_extract(a.old_values, concat('$.', @D3PROP)) <> json_extract(a.new_values, concat('$.', @D3PROP))
  and a.old_values not like concat('%"', @D3PROP, '": null%')
  and a.new_values not like concat('%"', @D3PROP, '": null%')
  and a.created_at >= @START_DATE
  and c.name not like '%onboard%'
  and c.name not like '%demo%'
  and o.tms_shipment_id is not null

order by variant_name, company_name, order_id
;
