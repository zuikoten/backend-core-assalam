<?php
// app/Modules/AgentSystem/Requests/UploadDocumentsRequest.php
namespace App\Modules\AgentSystem\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadDocumentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ktp_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'selfie_ktp' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'ktp_photo.required' => 'Foto KTP wajib diupload',
            'ktp_photo.image' => 'Foto KTP harus berformat JPG/PNG',
            'ktp_photo.max' => 'Ukuran foto KTP maksimal 2MB',
            'selfie_ktp.required' => 'Foto selfie dengan KTP wajib diupload',
            'selfie_ktp.image' => 'Foto selfie harus berformat JPG/PNG',
            'selfie_ktp.max' => 'Ukuran foto selfie maksimal 2MB',
        ];
    }
}
