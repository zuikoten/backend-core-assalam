<?php
// app/Modules/AgentSystem/Resources/AgentAddressResource.php
namespace App\Modules\AgentSystem\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgentAddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'complete_address' => $this->complete_address,
            'province' => $this->province?->name,
            'regency' => $this->regency?->name,
            'district' => $this->district?->name,
            'village' => $this->village,
            'postal_code' => $this->postal_code,
        ];
    }
}
