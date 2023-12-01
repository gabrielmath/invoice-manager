<?php

namespace App\Http\Requests;

use App\Rules\DocumentValidation;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'value'                => ['required', 'min:0.01'],
            'sender_document'      => ['required', 'string', new DocumentValidation],
            'sender_name'          => ['required', 'min:2', 'max:100'],
            'transporter_document' => ['required', 'string', new DocumentValidation],
            'transporter_name'     => ['required', 'min:2', 'max:100'],
        ];
    }

    /**
     * Prepare request for validation
     *
     * @return void
     */
    public function prepareForValidation(): void
    {
        $this->merge([
            'value'                => $this->valor,
            'sender_document'      => $this->cnpj_remetente,
            'sender_name'          => $this->nome_remetente,
            'transporter_document' => $this->cnpj_transportador,
            'transporter_name'     => $this->nome_transportador
        ]);
    }
}
