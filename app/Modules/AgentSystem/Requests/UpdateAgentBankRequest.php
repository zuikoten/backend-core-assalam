<?php
// app/Modules/AgentSystem/Requests/UpdateAgentBankRequest.php
namespace App\Modules\AgentSystem\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAgentBankRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_holder' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'bank_name.required' => 'Nama bank wajib diisi',
            'account_number.required' => 'Nomor rekening wajib diisi',
            'account_holder.required' => 'Nama pemilik rekening wajib diisi',
        ];
    }
}
