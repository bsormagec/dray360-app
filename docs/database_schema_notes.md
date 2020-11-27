# Dray 360 Database Schema

This document is where overall intentions and particular complexities of the data model can be described. 

It is not intended to document every individual column and table, most of which are self-explanatory and need no futher comment. 






## Table/Column Notes





#######################

### t_job_state_changes

Every state change for a request/order combination (or request/null when orders don't exist yet) is logged here. 

#### status_metadata

This JSON type column records different information for each `status` type. I will be updating this document to record all possible states here. It is currently documented in a Draw.io diagram and not very accessible.

##### status_metadata.order_id_list (ocr-post-processing-error and ocr-post-processing-complete)

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





#######################

### t_job_latest_state

For a given request/order combination (or request/null when orders don't exist yet) the latest row inserted into the `t_job_state_changes` table will be recorded on this table. 

Note that this "latest state" is recorded as written to the database, not as dated. Ergo, in the odd case where an earlier status change is written to the database later than an actual subsequent status change (due to some kind of asynchronous process delay) then the earlier timestamp will be recorded as the "latest state". This is an hypothetical problem only, and has not been seen in practice, but I mention it here in case it ever does happen.

As of November 2020, the only thing that writes to the t_job_latest_state table is an automated mysql trigger called `t_job_state_change_updated`

#### company_id and order_id 

These columns are nullable and _not_ foreign key references to the t_companies/t_orders table. This is intentional, because we can record order_id's for orders that were rolled-back and not actually committed to the database.




#######################



### t_orders


#### interchange_count

A given order-creation request (ocr or datafile) can create 0 to <n> orders. Each order will record in this `interchange_count` column the total number of orders created by its order-creation request batch.

#### interchange_err_count

If there were errors in an order-creation batch that resulted in some orders being created but others failing to be created, the total number of failures will be recorded in this `interchange_count_errors` on every order that did get created. Note that this value cannot be recorded on the failed orders because they weren't created and don't exist, heh.






#######################

### t_companies

In this data model a "company" is a customer of Dray360. Each company is uniquely identified by name and id. Those name's are hardcoded into the application and must not be allowed to change. .

#### name & id

These are hardcoded in `app/Models/Company.php` and reference by constant in `app/Console/Commands/ImportProfitToolsAddresses.php` among other places. 

At this point in time, the `name` must be unique, it must not change, and it must not be assigned to a different `id` than first assigned. The `name/id` combination must be identical between prod/dev/staging environments. 

If at some point in the future a "display name" is desired, different than "name", then add a new column for that purpose. 

#### company_config:json

Used for company-specific (i.e. never inherited and overridden by specific tenants/domains/users) configuration parameters. The idea is to not add a plethora of boolean columns to the `t_companies` table for every conceivable flag.

JSON data structure: 
```json
{
    "profit_tools_send_quantity_and_weight": true,
    "profit_tools_set_container_to_unknown": true,
    "profit_tools_enable_templates": true
}
```


#### configuration:json

Used for inheritable UI-specific configurations. This field is made available to the frontend. It overrides the default `tenants.configuation:json` configuration with any identical properties, and can itself be overridden by `users.configuration:json`. 

JSON data structure: 
```json
{
    "profit_tools_enable_templates": true
}
````




### t_ocrvariants

By rights this table would be called `t_datasource_variants` but when it was originally created the only variant type we processed was 'ocr' hence the name. Renaming it is fine if someone wants to do that at some point.

#### variant_type

This indicates what type of variant is being described. Valid values are:

1. NULL (implies 'ocr')
1. ocr
1. edi
1. tabular (includes CSV, XLSX, and possibly HTML tables; any flat colum/row data)

#### classification

JSON data structure: 
```json
{
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
```


#### mapping

JSON data structure: 
```pseudo-json
{
    need to define this
    ===================
    list of header=fieldname, 
    list of header=fixedvalue
}
```


#### company_id_list

This is a json array that stores list of companies that can use this datafile variant. If NULL or empty it means all companies can use the variant.

The reason for this column and to *not* to use the `t_company_ocrvariant_ocrrules` table with its `t_company_id`/`t_variant_id` columns to associate a company with the variant is because: the `company_id_list` gets used in conjunction with the aforementioned `classification` column to identify whether a given datafile should be represented by a particular variant; rules don't come into it.

JSON data structure: 
````json-list
[
    company_id_1,
    company_id_2,
    etc.
]
````

Note that to search the company_id_list for a given company_id, use the `json_contains` function, like this:

````sql
select * from t_ocrvariants where json_contains(t_company_id_list, '2','$');
````

Which will find company_id=2, for example. note that the single-quotes are required for integer values. String values would be searched with embedded double-quotes, like `'"two"'`






#######################

### t_equipment_types

#### line_prefix_list

This is another JSON array that stores a list of text container code prefixes to help find and identify equipment types. It can be null of no line prefixes are known for that particular equipment.

JSON data structure: 
````json-list
[
    line_prefix_1,
    line_prefix_2,
    etc.
]
````

See `company_id_list` and `status_metadata.order_id_list` for sample SQL queries.

