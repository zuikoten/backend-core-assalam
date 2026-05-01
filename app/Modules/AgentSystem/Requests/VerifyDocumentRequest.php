<?php
// app/Modules/AgentSystem/Requests/VerifyDocumentRequest.php
namespace App\Modules\AgentSystem\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return request()->user()?->isAdmin();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['approved', 'rejected'])],
            'reason' => 'required_if:status,rejected|string|max:500',
        ];
    }
}
