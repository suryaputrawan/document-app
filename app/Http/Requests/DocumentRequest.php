<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'no_surat'          => 'required|max:255|min:5|unique:documents,no_surat',
            'jenis_document'    => 'required',
            'diajukan_oleh'     => 'required',
            'disetujui_oleh'    => 'required',
            'recipient'         => ['array'],
            'recipient.*'       => ['required'],
            'approval'          => ['array'],
            'isi_document'      => 'required|min:5'
        ];
    }

    public function messages()
    {
        $messages = [];

        if ($this->get('recipient')) {
            foreach ($this->get('recipient') as $key => $val) {
                $messages["recipient.$key.required"] = "The user recipient field is required";
            }
        }

        return $messages;
    }
}
