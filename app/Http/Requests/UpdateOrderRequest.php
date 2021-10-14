<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Validation rules
     * Important: Don't add since we don't want to allow those fields to be edited:
     * - ocr_data
     * - request_id
     * - bill_to_address_raw_text
     * - port_ramp_of_origin_address_raw_text
     * - port_ramp_of_destination_address_raw_text
     * @return array
     */
    public function rules()
    {
        $order = $this->route('order');

        return [
            'shipment_designation' => 'sometimes|nullable',
            'shipment_direction' => 'sometimes|nullable',
            'one_way' => 'sometimes|nullable',
            'unit_number' => [
                'sometimes',
                'nullable',
                Rule::unique('t_orders')
                    ->ignoreModel($order)
                    ->where('request_id', $order->request_id),
            ],
            'equipment_size' => 'sometimes|nullable',
            'hazardous' => 'sometimes|nullable',
            'reference_number' => 'sometimes|nullable',
            'seal_number' => 'sometimes|nullable',
            'vessel' => 'sometimes|nullable',
            'voyage' => 'sometimes|nullable',
            'master_bol_mawb' => 'sometimes|nullable',
            'house_bol_hawb' => 'sometimes|nullable',
            'booking_number' => 'sometimes|nullable',
            'bill_of_lading' => 'sometimes|nullable',
            'bill_to_address_id' => 'sometimes|nullable|exists:t_addresses,id',
            'port_ramp_of_origin_address_id' => 'sometimes|nullable|exists:t_addresses,id',
            'port_ramp_of_destination_address_id' => 'sometimes|nullable|exists:t_addresses,id',
            'pickup_number' => 'sometimes|nullable',
            'pickup_by_date' => 'sometimes|nullable',
            'pickup_by_time' => 'sometimes|nullable',
            'cutoff_date' => 'sometimes|nullable',
            'cutoff_time' => 'sometimes|nullable',
            'bill_to_address_verified' => 'sometimes|nullable',
            'port_ramp_of_origin_address_verified' => 'sometimes|nullable',
            'port_ramp_of_destination_address_verified' => 'sometimes|nullable',
            'variant_id' => 'sometimes|nullable',
            'variant_name' => 'sometimes|nullable',
            't_tms_provider_id' => 'sometimes|nullable|exists:t_tms_providers,id',
            'tms_shipment_id' => 'sometimes|nullable',
            'carrier' => 'sometimes|nullable',
            'bill_charge' => 'sometimes|nullable',
            'bill_comment' => 'sometimes|nullable',
            'line_haul' => 'sometimes|nullable',
            'rate_box' => 'sometimes|nullable',
            'fuel_surcharge' => 'sometimes|nullable',
            'total_accessorial_charges' => 'sometimes|nullable',
            'equipment_provider' => 'sometimes|nullable',
            'actual_destination' => 'sometimes|nullable',
            'actual_origin' => 'sometimes|nullable',
            'customer_number' => 'sometimes|nullable',
            'expedite' => 'sometimes|nullable',
            'load_number' => 'sometimes|nullable',
            'purchase_order_number' => 'sometimes|nullable',
            'release_number' => 'sometimes|nullable',
            'ship_comment' => 'sometimes|nullable',
            'division_code' => 'sometimes|nullable',
            't_equipment_type_id' => 'sometimes|nullable|exists:t_equipment_types,id',
            'chassis_equipment_type_id' => 'sometimes|nullable|exists:t_equipment_types,id',
            'equipment_type_verified' => 'sometimes|nullable',
            'chassis_equipment_type_verified' => 'sometimes|nullable',
            'tms_submission_datetime' => 'sometimes|nullable',
            'tms_cancelled_datetime' => 'sometimes|nullable',
            'interchange_count' => 'sometimes|nullable',
            'interchange_err_count' => 'sometimes|nullable',
            'tms_template_id' => 'sometimes|nullable',
            'tms_template_dictid' => 'sometimes|nullable',
            'itgcontainer_dictid' => 'sometimes|nullable',
            'itgcontainer_dictid_verified' => 'sometimes|nullable',
            'tms_template_dictid_verified' => 'sometimes|nullable',
            'is_hidden' => 'sometimes|nullable',
            'carrier_dictid' => 'sometimes|nullable',
            'carrier_dictid_verified' => 'sometimes|nullable',
            'vessel_dictid' => 'sometimes|nullable',
            'vessel_dictid_verified' => 'sometimes|nullable',
            'cc_loadtype_dictid' => 'sometimes|nullable',
            'cc_loadtype_dictid_verified' => 'sometimes|nullable',
            'cc_orderstatus' => 'sometimes|nullable',
            'cc_haulclass_dictid' => 'sometimes|nullable',
            'cc_haulclass_dictid_verified' => 'sometimes|nullable',
            'cc_orderclass_dictid' => 'sometimes|nullable',
            'cc_orderclass_dictid_verified' => 'sometimes|nullable',
            'cc_loadedempty_dictid' => 'sometimes|nullable',
            'cc_loadedempty_dictid_verified' => 'sometimes|nullable',
            'termdiv_dictid' => 'sometimes|nullable',
            'termdiv_dictid_verified' => 'sometimes|nullable',
            'cc_containersize_dictid' => 'sometimes|nullable',
            'cc_containersize_dictid_verified' => 'sometimes|nullable',
            'cc_containertype_dictid' => 'sometimes|nullable',
            'cc_containertype_dictid_verified' => 'sometimes|nullable',
            'eta_date' => 'sometimes|nullable',
            'eta_time' => 'sometimes|nullable',
            'temperature' => 'sometimes|nullable',
            'required_equipment' => 'sometimes|nullable',
            'ssrr_location_address_id' => 'sometimes|nullable',
            'ssrr_location_address_verified' => 'sometimes|nullable',
            'custom1' => 'sometimes|nullable',
            'custom2' => 'sometimes|nullable',
            'custom3' => 'sometimes|nullable',
            'custom4' => 'sometimes|nullable',
            'custom5' => 'sometimes|nullable',
            'custom6' => 'sometimes|nullable',
            'custom7' => 'sometimes|nullable',
            'custom8' => 'sometimes|nullable',
            'custom9' => 'sometimes|nullable',
            'custom10' => 'sometimes|nullable',
            'pt_ref1_text' => 'sometimes|nullable',
            'pt_ref2_text' => 'sometimes|nullable',
            'pt_ref3_text' => 'sometimes|nullable',
            'pt_ref1_type' => 'sometimes|nullable',
            'pt_ref2_type' => 'sometimes|nullable',
            'pt_ref3_type' => 'sometimes|nullable',
            'pt_equipmenttype_container_dictid' => 'sometimes|nullable',
            'pt_equipmenttype_container_dictid_verified' => 'sometimes|nullable',
            'pt_equipmenttype_chassis_dictid' => 'sometimes|nullable',
            'pt_equipmenttype_chassis_dictid_verified' => 'sometimes|nullable',
        ];
    }

    public function messages()
    {
        return [
            'unit_number.unique' => 'The unit number already exists in this request',
        ];
    }
}
