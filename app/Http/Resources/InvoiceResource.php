<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'invoice';


    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'numero'             => $this->number,
            'data_emissao'       => $this->issue_date->format('d/m/Y'),
            'valor'              => $this->money_value,
            'cnpj_remetente'     => $this->sender_doc,
            'nome_remetente'     => $this->sender_name,
            'cnpj_transportador' => $this->transporter_doc,
            'nome_transportador' => $this->transporter_name,
        ];
    }
}
