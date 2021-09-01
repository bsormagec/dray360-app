<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFieldMapRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $companyId = $this->get('company_id');
        $variantId = $this->get('variant_id');
        $tmsProviderId = $this->get('tms_provider_id');

        if (! $companyId && ! $variantId && ! $tmsProviderId) {
            return $this->user()->isAbleTo('field-maps-create');
        } elseif ($variantId && $companyId) {
            return $this->user()->isAbleTo('company-field-maps-create');
        } elseif ($companyId) {
            return $this->user()->isAbleTo('company-field-maps-create');
        } elseif ($variantId) {
            return $this->user()->isAbleTo('variant-field-maps-create');
        } elseif ($tmsProviderId) {
            return $this->user()->isAbleTo('tms-field-maps-create');
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company_id' => ['sometimes', 'nullable', 'exists:t_companies,id'],
            'variant_id' => ['sometimes', 'nullable', 'exists:t_ocrvariants,id'],
            'tms_provider_id' => ['sometimes', 'nullable', 'exists:t_tms_providers,id'],
            'fieldmap_config' => ['required', 'array'],
        ];
    }
}
