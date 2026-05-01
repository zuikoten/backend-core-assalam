<?php
// app/Modules/AgentSystem/Requests/UpdateAgentAddressRequest.php
namespace App\Modules\AgentSystem\Requests;

use App\Models\Region;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAgentAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'province_id' => [
                'required',
                Rule::exists('regions', 'id')->where('region_type', 'province'),
            ],
            'regency_id' => [
                'required',
                Rule::exists('regions', 'id')->where('region_type', 'regency'),
            ],
            'district_id' => [
                'required',
                Rule::exists('regions', 'id')->where('region_type', 'district'),
            ],
            'village' => 'nullable|string|max:255',
            'full_address' => 'required|string|max:500',
            'postal_code' => 'nullable|string|size:5',
        ];
    }

    public function messages(): array
    {
        return [
            'province_id.required' => 'Provinsi wajib dipilih',
            'regency_id.required' => 'Kabupaten/Kota wajib dipilih',
            'district_id.required' => 'Kecamatan wajib dipilih',
            'full_address.required' => 'Alamat lengkap wajib diisi',
        ];
    }
}
