<?php

namespace App\Services\Apis;

use App\Models\Company;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Exceptions\BlackflyException;

class Blackfly
{
    protected string $url;
    protected string $token;
    protected Company $company;

    public function __construct(Company $company)
    {
        $this->url = Str::finish(config('services.blackfly.url'), '/');
        $this->company = $company;
        $this->token = $company->blackfly_token;
    }

    public function executeSql(string $sql): array
    {
        $response = Http::withToken($this->token)
            ->post("{$this->url}execute_sql/", [
                "sql" => $sql
            ]);

        if ($response->failed() || ! is_array($response->json())) {
            throw new BlackflyException('execute_sql', $response->body(), $response->status());
        }

        return $response->json();
    }

    public function getTemplates(): array
    {
        return $this->executeSql("select ds_id, ds_ref1_text from dba.disp_ship where ds_status='A' and ds_ref1_text>'' order by ds_id");
    }

    public function getEquipmentTypes(): array
    {
        return $this->executeSql("select 'id' = Id, 'line' = Line, 'type' = Type, 'equipmentlength' = equipmentlength, 'scac' = scac, 'lineprefix' = lineprefix from dba.EquipmentLeaseType where leasestatus = '1' order by Id");
    }
}
