<?php
// app/Modules/AgentSystem/Requests/RegisterAgentRequest.php
namespace App\Modules\AgentSystem\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterAgentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'agent_level' => ['required', Rule::in(['cabang', 'premium'])],
            'full_name' => 'required|string|max:255',
            'nik' => 'required|string|size:16|unique:agents,nik',
            'phone' => 'required|string|regex:/^08[0-9]{8,12}$/|unique:users,phone',
            'whatsapp' => 'required|string|regex:/^08[0-9]{8,12}$/|unique:users,whatsapp',
            'email' => 'required|email:rfc,dns|unique:users,email',
            'birth_date' => 'nullable|date|before:today',
            'gender' => ['nullable', Rule::in(['L', 'P'])],
            'job' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'upline_agent_code' => 'nullable|string|exists:agents,agent_code',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Format nomor HP tidak valid (contoh: 081234567890)',
            'whatsapp.regex' => 'Format WhatsApp tidak valid (contoh: 081234567890)',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'nik.size' => 'NIK harus 16 digit',
        ];
    }
}
