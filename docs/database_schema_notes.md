# Dray 360 Database Schema

This document is where overall intentions and particular complexities of the data model can be described. 

It is not intended to document every individual column and table, most of which are self-explanatory and need no futher comment. 






## Table/Column Notes




### t_companies

In this data model a "company" is a customer of Dray360. Each company is uniquely identified by name and id. Those name's are hardcoded into the application and must not be allowed to change. .

#### name & id

These are hardcoded in `app/Models/Company.php` and reference by constant in `app/Console/Commands/ImportProfitToolsAddresses.php` among other places. 

At this point in time, the `name` must be unique, it must not change, and it must not be assigned to a different `id` than first assigned. The `name/id` combination must be identical between prod/dev/staging environments. 

If at some point in the future a "display name" is desired, different than "name", then add a new column for that purpose. 




### t_ocrvariants

By rights this table would be called `t_datasource_variants` but when it was originally created the only variant type we processed was 'ocr' hence the name. Renaming it is fine if someone wants to do that at some point.

#### variant_type

This indicates what type of variant is being described. Valid values are:

1. NULL (implies 'ocr')
1. ocr
1. edi
1. tabular (includes CSV, XLSX, and possibly HTML tables; any flat colum/row data)

#### classification

JSON data structure: {
    "required_header_field_list": [
        "field1",
        "field2",
        "etc."
    ], 
    "forbidden_header_field_list": [
        "fieldA",
        "fieldB",
        "etc."
    ]    
}


#### mapping

JSON data structure: {
    need to define this
    ===================
    list of header=fieldname, 
    list of header=fixedvalue
}


#### company_id_list

This is a json array that stores list of companies that can use this datafile variant. If NULL or empty it means all companies can use the variant.

JSON data structure: [
    company_id_1,
    company_id_2,
    etc.
]

Note that to search the company_id_list for a given company_id, use the `json_contains` function, like this:

````sql
select * from t_ocrvariants where json_contains(t_company_id_list, '2','$');
````

Which will find company_id=2, for example. note that the single-quotes are required for integer values. String values would be searched with embedded double-quotes, like `'"two"'`







### t_equipment_types

#### line_prefix_list

This is another JSON array that stores a list of text container code prefixes to help find and identify equipment types. It can be null of no line prefixes are known for that particular equipment.

JSON data structure: [
    line_prefix_1,
    line_prefix_2,
    etc.
]

See `company_id_list` for sample SQL queries.

