<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CounterpartyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'inn' => $this->inn,
            'name' => $this->name,
            'short_name' => $this->short_name,
            'type' => $this->type,
            'city' => $this->city,
            'legal_address' => $this->legal_address,
            'actual_address' => $this->actual_address,
            'address_matches' => $this->address_matches,
            'ogrn' => $this->ogrn,
            'kpp' => $this->kpp,
            'ati_code' => $this->ati_code,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'edo_identifier' => $this->edo_identifier,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // whenLoaded — чтобы не тянуть контакты/счета лишним запросом там,
            // где они не нужны (например, в списке index() их можно не грузить).
            'contacts' => ContactResource::collection($this->whenLoaded('contacts')),
            'bank_accounts' => BankAccountResource::collection($this->whenLoaded('bankAccounts')),
        ];
    }
}
