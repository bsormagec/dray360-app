
/* 
REQUIREMENTS:
    https://envasetechnologies-my.sharepoint.com/:x:/p/aaron_bryden/EWPGV93XGK1DqAQ5Tq4SKXcB-ZIFNI3ADQaHYpn7OC6T_w?rtime=Smih-RAF2Ug
*/

/*
 t_fieldmaps default ->
    t_tms_providers ->
      t_ocrvariants ->
        t_companies ->
          t_company_ocrvariant

... lowest layer takes priority.
*/


/*
set collation_connection = utf8mb4_unicode_ci;

set @VARIANT_NAME = 'WilliamGrant-import';
set @COMPANY_NAME = 'Zariz';
set @TMSPROVIDER_NAME = 'Profit Tools';

set @COMPANY_ID = (select id from t_companies where name = @COMPANY_NAME);
set @TMSPROVIDER_ID = (select id from t_tms_providers where name = @TMSPROVIDER_NAME);

set @OCRVARIANT_ID = (select id from t_ocrvariants where abbyy_variant_name = @VARIANT_NAME);
set @COMPANY_OCRVARIANT_ID = (select id from t_company_ocrvariant where t_ocrvariant_id = @OCRVARIANT_ID and t_company_id = @COMPANY_ID);
set @DEFAULT_FIELDMAP_ID = (select id from t_fieldmaps as f where f.system_default and f.replaced_at is null order by f.id desc limit 1);

select json_pretty(json_merge_patch(
     coalesce(( select f.fieldmap_config from t_fieldmaps as f                                                          where f.id = @DEFAULT_FIELDMAP_ID   ), '{}')
    ,coalesce(( select f.fieldmap_config from t_fieldmaps as f join t_tms_providers      as t on t.t_fieldmap_id = f.id where t.id = @TMSPROVIDER_ID        ), '{}')
    ,coalesce(( select f.fieldmap_config from t_fieldmaps as f join t_ocrvariants        as c on c.t_fieldmap_id = f.id where c.id = @OCRVARIANT_ID         ), '{}')
    ,coalesce(( select f.fieldmap_config from t_fieldmaps as f join t_companies          as c on c.t_fieldmap_id = f.id where c.id = @COMPANY_ID            ), '{}')
    ,coalesce(( select f.fieldmap_config from t_fieldmaps as f join t_company_ocrvariant as v on v.t_fieldmap_id = f.id where v.id = @COMPANY_OCRVARIANT_ID ), '{}')
))
\G

*/

/* 
    Cushing's example "custom/refs" mapping:

    refs_custom_mapping: {
        "custom3": {"value": "No"}, 
        "custom8": {"source": "actual_destination"}, 
        "ds_equip_req": {"source": "ship_comment"}, 
        "ds_ref2_text": {"source": "reference_number"}, 
        "ds_ref2_type": {"value": 2}, 
        "ds_ref3_type": {"value": 3}
    }
*/


insert into t_fieldmaps(system_default, fieldmap_config) values(true, '
{
    "unit_number" : {
        "d3canon_name": "unit_number",
        "d3canon_table": "t_orders",
        "d3canon_column": "unit_number",
        "abbyy_source_field": "equip_info.unit_number",
        "abbyy_source_regex": null,
        "available": true,
        "templateable": false,
        "use_template_value": true,
        "adminreview_if_missing": false,
        "adminreview_validation_regex": null,
        "screen_hide": false,
        "screen_name": "Unit number",
        "constant_value": null,
        "post_process_source_field": null,
        "post_process_source_regex": null,
        "profittools_destination": "Equipment.eq_ref",
        "cargowise_destination": null,
        "compcare_destination": null,
        "notes": null
    },
    "custom8" : {
        "d3canon_name": "custom8",
        "d3canon_table": "t_orders",
        "d3canon_column": "custom8",
        "abbyy_source_field": null,
        "abbyy_source_regex": null,
        "available": true,
        "templateable": false,
        "use_template_value": true,
        "adminreview_if_missing": false,
        "adminreview_validation_regex": null,
        "screen_hide": false,
        "screen_name": "Actual destination",
        "constant_value": null,
        "post_process_source_field": "actual_destination",
        "post_process_source_regex": null,
        "profittools_destination": "custom8",
        "cargowise_destination": null,
        "compcare_destination": null,
        "notes": null
    },
    "custom9" : {
        "d3canon_name": "custom9",
        "d3canon_table": "t_orders",
        "d3canon_column": "custom9",
        "abbyy_source_field": null,
        "abbyy_source_regex": null,
        "available": true,
        "templateable": false,
        "use_template_value": true,
        "adminreview_if_missing": false,
        "adminreview_validation_regex": null,
        "screen_hide": false,
        "screen_name": "Is Texas",
        "constant_value": null,
        "post_process_source_field": "bill_to_address.city",
        "post_process_source_regex": "s/texas/1/, i.e. something to return true if texas is in post_process_source_field",
        "profittools_destination": "custom9",
        "cargowise_destination": null,
        "compcare_destination": null,
        "notes": null
    },
    "division_code" : {
        "d3canon_name": "division_code",
        "d3canon_table": "t_orders",
        "d3canon_column": "division_code",
        "abbyy_source_field": null,
        "abbyy_source_regex": null,
        "available": true,
        "templateable": true,
        "use_template_value": true,
        "adminreview_if_missing": false,
        "adminreview_validation_regex": null,
        "screen_hide": false,
        "screen_name": "Division Code",
        "constant_value": null,
        "post_process_source_field": null,
        "post_process_source_regex": null,
        "profittools_destination": "ds_ship_type",
        "cargowise_destination": null,
        "compcare_destination": null,
        "notes": "Not captured in Abbyy"
    },
    "bill_to_address_code" : {
        "d3canon_name": "bill_to_address_code",
        "d3canon_table_column": "t_company_address_tms_code",
        "d3canon_column": "company_address_tms_code",
        "abbyy_source_field": "order_info.bill_to_address",
        "abbyy_source_regex": null,
        "available": true,
        "templateable": true,
        "use_template_value": true,
        "adminreview_if_missing": false,
        "adminreview_validation_regex": null,
        "screen_hide": false,
        "screen_name": "Bill To",
        "constant_value": null,
        "post_process_source_field": null,
        "post_process_source_regex": null,
        "profittools_destination": "ds_ship_type",
        "cargowise_destination": null,
        "compcare_destination": null,
        "notes": null
    },
    "shipment_direction" : {
        "d3canon_name": "shipment_direction",
        "d3canon_table": "t_orders",
        "d3canon_column": "shipment_direction",
        "abbyy_source_field": "order_info.order_type",
        "abbyy_source_regex": null,
        "available": true,
        "templateable": true,
        "use_template_value": true,
        "adminreview_if_missing": true,
        "adminreview_validation_regex": null,
        "screen_hide": false,
        "screen_name": "Shipment Direction",
        "constant_value": null,
        "post_process_source_field": null,
        "post_process_source_regex": null,
        "profittools_destination": "Events.movecode",
        "cargowise_destination": null,
        "compcare_destination": null,
        "notes": "profittools: I/E/O for Import/Export/Oneway(Crosstown)"
    },
    "event_type" : {
        "d3canon_name": "event_type",
        "d3canon_table": "t_order_address_events",
        "d3canon_column": "is_hook_event|is_mount_event|is_deliver_event|is_dismount_event|is_drop_event|is_pickup_event",
        "abbyy_source_field": "event#.type",
        "abbyy_source_regex": null,
        "available": true,
        "templateable": true,
        "use_template_value": true,
        "adminreview_if_missing": false,
        "adminreview_validation_regex": null,
        "screen_hide": false,
        "screen_name": null,
        "constant_value": null,
        "post_process_source_field": null,
        "post_process_source_regex": null,
        "profittools_destination": "Events.de_event_type",
        "cargowise_destination": null,
        "compcare_destination": null,
        "notes": "profittools: H=hook, M=mount, D=deliver, N=dismount, R=drop, P=pickup"
    },
    "event_note" : {
        "d3canon_name": "event_note",
        "d3canon_table": "t_order_address_events",
        "d3canon_column": "note",
        "abbyy_source_field": null,
        "abbyy_source_regex": null,
        "available": true,
        "templateable": true,
        "use_template_value": true,
        "adminreview_if_missing": false,
        "adminreview_validation_regex": null,
        "screen_hide": false,
        "screen_name": null,
        "constant_value": null,
        "post_process_source_field": null,
        "post_process_source_regex": null,
        "profittools_destination": "Events.de_note",
        "cargowise_destination": null,
        "compcare_destination": null,
        "notes": null
    },
    "event_address_tms_code" : {
        "d3canon_name": "event_address_tms_code",
        "d3canon_table_column": "t_company_address_tms_code",
        "d3canon_column": "company_address_tms_code",
        "abbyy_source_field": "event#.location",
        "abbyy_source_regex": null,
        "available": true,
        "templateable": true,
        "use_template_value": true,
        "adminreview_if_missing": false,
        "adminreview_validation_regex": null,
        "screen_hide": false,
        "screen_name": null,
        "constant_value": null,
        "post_process_source_field": null,
        "post_process_source_regex": null,
        "profittools_destination": "Events.de_site",
        "cargowise_destination": null,
        "compcare_destination": null,
        "notes": null
    },
    "item_contents" : {
        "d3canon_name": "item_contents",
        "d3canon_table_column": "t_order_line_items",
        "d3canon_column": "contents",
        "abbyy_source_field": "contents",
        "abbyy_source_regex": null,
        "available": true,
        "templateable": true,
        "use_template_value": true,
        "adminreview_if_missing": false,
        "adminreview_validation_regex": null,
        "screen_hide": true,
        "screen_name": null,
        "constant_value": null,
        "post_process_source_field": null,
        "post_process_source_regex": null,
        "profittools_destination": "Items.di_description",
        "cargowise_destination": null,
        "compcare_destination": null,
        "notes": null
    },
    "item_quantity" : {
        "d3canon_name": "item_quantity",
        "d3canon_table_column": "t_order_line_items",
        "d3canon_column": "quantity",
        "abbyy_source_field": "quantity",
        "abbyy_source_regex": null,
        "available": true,
        "templateable": true,
        "use_template_value": true,
        "adminreview_if_missing": false,
        "adminreview_validation_regex": null,
        "screen_hide": false,
        "screen_name": null,
        "constant_value": null,
        "post_process_source_field": null,
        "post_process_source_regex": null,
        "profittools_destination": "Items.di_qty",
        "cargowise_destination": null,
        "compcare_destination": null,
        "notes": null
    },
    "item_weight" : {
        "d3canon_name": "item_weight",
        "d3canon_table_column": "t_order_line_items",
        "d3canon_column": "weight",
        "abbyy_source_field": "weight",
        "abbyy_source_regex": null,
        "available": true,
        "templateable": true,
        "use_template_value": true,
        "adminreview_if_missing": false,
        "adminreview_validation_regex": null,
        "screen_hide": false,
        "screen_name": null,
        "constant_value": null,
        "post_process_source_field": null,
        "post_process_source_regex": null,
        "profittools_destination": "Events.di_totitemweight",
        "cargowise_destination": null,
        "compcare_destination": null,
        "notes": null
    }
}
');




-- TCompaniesDemo site, just testing...
insert into t_fieldmaps(fieldmap_config) values('
{
    "item_description" : {
        "use_template_value": false
    },
    "item_weight" : {
        "use_template_value": true
    },
    "item_quantity" : {
        "use_template_value": false
    }}
');
set @TCOMPANIESDEMO_COMPANY_FIELDMAP_ID = (SELECT LAST_INSERT_ID());
set @TCOMPANIESDEMO_COMPANY_ID = (select id from t_companies where name = 'TCompaniesDemo');
update t_companies set t_fieldmap_id=@TCOMPANIESDEMO_COMPANY_FIELDMAP_ID where id=@TCOMPANIESDEMO_COMPANY_ID;




-- For Zariz, they don't want decription/weight/quantity to be templated
insert into t_fieldmaps(fieldmap_config) values('
{
    "item_description" : {
        "use_template_value": false
    },
    "item_weight" : {
        "use_template_value": true
    },
    "item_quantity" : {
        "use_template_value": false
    }}
');
set @ZARIZ_COMPANY_FIELDMAP_ID = (SELECT LAST_INSERT_ID());
set @ZARIZ_COMPANY_ID = (select id from t_companies where name = 'Zariz');
update t_companies set t_fieldmap_id=@ZARIZ_COMPANY_FIELDMAP_ID where id=@ZARIZ_COMPANY_ID;




-- for TransportDSquare, they don't want events to be templated
insert into t_fieldmaps(fieldmap_config) values('
{
    "item_description" : {
        "use_template_value": false
    },
    "item_weight" : {
        "use_template_value": true
    },
    "item_quantity" : {
        "use_template_value": false
    }}
');
set @TRANSPORTDSQUARE_COMPANY_FIELDMAP_ID = (SELECT LAST_INSERT_ID());
set @TRANSPORTDSQUARE_COMPANY_ID = (select id from t_companies where name = 'TransportDSquare');
update t_companies set t_fieldmap_id=@TRANSPORTDSQUARE_COMPANY_FIELDMAP_ID where id=@TRANSPORTDSQUARE_COMPANY_ID;


