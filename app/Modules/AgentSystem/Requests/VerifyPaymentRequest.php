<?php
// app/Modules/AgentSystem/Requests/VerifyPaymentRequest.php
namespace App\Modules\AgentSystem\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return request()->user()?->isAdmin();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['success', 'failed'])],
        ];
    }
}
