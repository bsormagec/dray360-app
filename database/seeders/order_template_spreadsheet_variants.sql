/*
actual_destination
actual_origin
bill_charge
bill_comment
bill_of_lading
bill_to_address
booking_number
carrier
container_length
contents
customer_number
cutoff_date
cutoff_time
destination_ramp
division_code
equipment_provider
equipment_type
event1_is_deliver_event
event1_is_dismount_event
event1_is_drop_event
event1_is_hook_event
event1_is_mount_event
event1_is_pickup_event
event1_location
event1_type
event2_location
event2_type
event3_location
event3_type
event4_location
event4_type
event5_location
event5_type
event6_location
event6_type
expedite
fuel_surcharge
hazmat
house_bol_hawb
line_haul
load_number
master_bol_mawb
multiline_multiorder_area_with_contents
order_type
origin_ramp
pickup_by_date
pickup_by_time
pickup_number
purchase_order_number
quantity
rate_box
reference_number
release_number
seal_number
ship_comment
shipment_designation
total_accessorial_charges
unit_number
variant_id
variant_name
vessel
voyage
weight
*/









insert into t_ocrvariants(
     created_at
    ,abbyy_variant_id
    ,abbyy_variant_name
    ,description
    ,variant_type
    ,mapping
    ,company_id_list
) values(
     CURRENT_TIMESTAMP
    ,-1
    ,'Template #1 - Imports'
    ,'Generic Order Template Spreadsheet #1 - Imports'
    ,'tabular'
    ,'{
        "expedite": {"source": "Expedite (Y/N)"},
        "hazmat": {"source": "Haz (Y/N)"},
        "carrier": {"source": "Steamship Line "},
        "container_length": {"source": "Equipment Size / Type"},
        "unit_number": {"source": "Unit #"},
        "seal_number": {"source": "Seal"},
        "reference_number": {"source": "Reference #"},
        "customer_number": {"source": "Customer #"},
        "load_number": {"source": "Load #"},
        "purchase_order_number": {"source": "PO #"},
        "release_number": {"source": "Release #"},
        "pickup_number": {"source": "Pickup #"},
        "vessel": {"source": "Vessel"},
        "voyage": {"source": "Voyage"},
        "house_bol_hawb": {"source": "House BL"},
        "master_bol_mawb": {"source": "Master BL"},
        "bill_to_address": {"source": "Bill To"},
        "bill_comment": {"source": "Bill Comments"},
        "line_haul": {"source": "Line Haul"},
        "fuel_surcharge": {"source": "FSC"},
        "event1_location": {"source": "Terminal Location"},
        "event1_note": {"source": "Terminal Notes"},
        "event2_location": {"source": "Customer Location"},
        "event2_note": {"source": "Customer Notes"},
        "ship_comment": {"source": "Ship Comments"},
        "contents": {"source": "Contents"},
        "quantity": {"source": "QTY"},
        "asdfweight": {"source": "Weight"}
        "weight": {"value": 999}
    }'
    ,'[2, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22]'
)
;

SELECT LAST_INSERT_ID() as `Imports Template` \G











Expedite
Haz
Steamship Line 
Equipment Size / Type
Reference #
Customer #
Load #
PO #
Release #
Pickup #
Vessel
Voyage
Cutoff Date
Cutoff Time
Booking Number
Bill To
Billing Comments
Line Haul
FSC
Terminal Location
Terminal Notes
Customer Location
Customer Notes
Ship Notes
Contents
QTY
Weight





insert into t_ocrvariants(
     created_at
    ,abbyy_variant_id
    ,abbyy_variant_name
    ,description
    ,variant_type
    ,mapping
    ,company_id_list
) values(
     CURRENT_TIMESTAMP
    ,-1
    ,'Template #2 - Exports'
    ,'Generic Order Template Spreadsheet #2 - Exports'
    ,'tabular'
    ,'{
        "expedite": {"source": "Expedite"},
        "hazmat": {"source": "Haz"},
        "carrier": {"source": "Steamship Line "},
        "container_length": {"source": "Equipment Size / Type"},
        "reference_number": {"source": "Reference #"},
        "customer_number": {"source": "Customer #"},
        "load_number": {"source": "Load #"},
        "purchase_order_number": {"source": "PO #"},
        "release_number": {"source": "Release #"},
        "pickup_number": {"source": "Pickup #"},
        "vessel": {"source": "Vessel"},
        "voyage": {"source": "Voyage"},
        "house_bol_hawb": {"source": "Cutoff Date"},
        "master_bol_mawb": {"source": "Cutoff Time"},
        "booking_number": {"source": "Booking Number"},
        "bill_to_address": {"source": "Bill To"},
        "bill_comment": {"source": "Billing Comments"},
        "line_haul": {"source": "Line Haul"},
        "fuel_surcharge": {"source": "FSC"},
        "event1_location": {"source": "Empty Location"},
        "event1_note": {"source": "Empty Notes"},
        "event2_location": {"source": "Customer Location"},
        "event2_note": {"source": "Customer Notes"},
        "event3_location": {"source": "Terminal Location"},
        "event3_note": {"source": "Terminal Notes"},
        "ship_comment": {"source": "Ship Notes"},
        "contents": {"source": "Contents"},
        "quantity": {"source": "QTY"},
        "weight": {"source": "Weight"}        
    }'
    ,'[2, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22]'
)
;

SELECT LAST_INSERT_ID() as `Exports Template` \G
