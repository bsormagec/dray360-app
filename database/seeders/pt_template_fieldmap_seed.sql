/* 
REQUIREMENTS:
    https://envasetechnologies-my.sharepoint.com/:x:/p/aaron_bryden/EWPGV93XGK1DqAQ5Tq4SKXcB-ZIFNI3ADQaHYpn7OC6T_w?rtime=Smih-RAF2Ug
*/


/*
DONE bill_to_address_code: ds_billto_id
DONE division_code: ds_ship_type
DONE shipment_direction: Events.movecode
DONE note: Events.de_note
DONE is_xyz_event: Events.de_event_type
DONE event_address_tms_code: Events.de_site
*/

/*
select *,json_pretty(fieldmap_config) from t_fieldmaps \G
*/

/*
 t_fieldmappings ->
    t_tms_providers ->
      t_companies ->
        t_company_ocrvariant

... lowest layer takes priority.
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
        "abbyy_source_field": equip_info.unit_number,
        "abbyy_source_regex": null,
        "available": true,
        "templateable": false,
        "adminreview_if_missing": false,
        "adminreview_validation_regex": null,
        "screen_hide": false,
        "screen_name": "Unit number",
        "use_template_value": true,
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
        "adminreview_if_missing": false,
        "adminreview_validation_regex": null,
        "screen_hide": false,
        "screen_name": "Actual destination",
        "use_template_value": true,
        "constant_value": null,
        "post_process_source_field": "actual_destination",
        "post_process_source_regex": null
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
        "adminreview_if_missing": false,
        "adminreview_validation_regex": null,
        "screen_hide": false,
        "screen_name": "Is Texas",
        "use_template_value": true,
        "constant_value": null,
        "post_process_source_field": "bill_to_address.city",
        "post_process_source_regex": "s/texas/1/, i.e. something to return true if \'texas\' is in post_process_source_field",
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
        "adminreview_if_missing": false,
        "adminreview_validation_regex": null,
        "screen_hide": false,
        "screen_name": "Division Code",
        "use_template_value": true,
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
        "adminreview_if_missing": false,
        "adminreview_validation_regex": null,
        "screen_hide": false,
        "screen_name": "Bill To",
        "use_template_value": true,
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
        "adminreview_if_missing": true,
        "adminreview_validation_regex": null,
        "screen_hide": false,
        "screen_name": "Shipment Direction",
        "use_template_value": true,
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
        "adminreview_if_missing": false,
        "adminreview_validation_regex": null,
        "screen_hide": false,
        "screen_name": null,
        "use_template_value": true,
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
        "adminreview_if_missing": false,
        "adminreview_validation_regex": null,
        "screen_hide": false,
        "screen_name": null,
        "use_template_value": true,
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
        "adminreview_if_missing": false,
        "adminreview_validation_regex": null,
        "screen_hide": false,
        "screen_name": null,
        "use_template_value": true,
        "constant_value": null,
        "post_process_source_field": null,
        "post_process_source_regex": null,
        "profittools_destination": "Events.de_site",
        "cargowise_destination": null,
        "compcare_destination": null,
        "notes": null
    },
    "item_description" : {
        "d3canon_name": "item_description",
        "d3canon_table_column": "t_order_line_items",
        "d3canon_column": "company_address_tms_code",
        "abbyy_source_field": "event#.location",
        "abbyy_source_regex": null,
        "available": true,
        "templateable": true,
        "adminreview_if_missing": false,
        "adminreview_validation_regex": null,
        "screen_hide": false,
        "screen_name": null,
        "use_template_value": true,
        "constant_value": null,
        "post_process_source_field": null,
        "post_process_source_regex": null,
        "profittools_destination": "Events.de_site",
        "cargowise_destination": null,
        "compcare_destination": null,
        "notes": null
    }
}
');





insert into t_fieldmaps(fieldmap_config) values('
{
    "division_code" : {
        "use_template_value": false
    },
    "bill_to_address_code" : {
        "use_template_value": true
    },
    "movecode" : {
        "use_template_value": false
    },
    "event_type" : {
        "use_template_value": true
    },
    "event_note" : {
        "use_template_value": true
    },
    "event_address_tms_code" : {
        "use_template_value": true
    }
}
');

set @ZARIZ_COMPANY_FIELDMAP_ID = (SELECT LAST_INSERT_ID());
set @ZARIZ_COMPANY_ID = (select id from t_companies where name = 'Zariz');
update t_companies set t_fieldmap_id=@ZARIZ_COMPANY_FIELDMAP_ID where id=@ZARIZ_COMPANY_ID;
