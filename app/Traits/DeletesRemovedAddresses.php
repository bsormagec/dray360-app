<?php

namespace App\Traits;

use App\Models\Address;
use Illuminate\Support\Collection;
use App\Models\CompanyAddressTMSCode;

trait DeletesRemovedAddresses
{
    protected function deleteAddressesRemovedInTheResponse(Collection $requestCodes, $companyId, $tmsProviderId)
    {
        $codesToDelete = CompanyAddressTMSCode::query()
            ->forCompanyTmsProvider($companyId, $tmsProviderId)
            ->pluck('company_address_tms_code')
            ->reject(fn ($item) => $requestCodes->contains($item))
            ->values();

        if ($codesToDelete->count() == 0) {
            return;
        }

        // Address::whereIn('id', function ($query) use ($codesToDelete, $companyId, $tmsProviderId) {
        //     $query->select('t_address_id')
        //         ->from('t_company_address_tms_code')
        //         ->whereIn('company_address_tms_code', $codesToDelete)
        //         ->where([
        //             't_company_id' => $companyId,
        //             't_tms_provider_id' => $tmsProviderId,
        //         ]);
        // })->delete();
        CompanyAddressTMSCode::query()
            ->whereIn('company_address_tms_code', $codesToDelete)
            ->where([
                't_company_id' => $companyId,
                't_tms_provider_id' => $tmsProviderId,
            ])
            ->delete();
    }
}
