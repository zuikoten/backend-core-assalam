<?php
// app/Modules/AgentSystem/Resources/AgentResource.php
namespace App\Modules\AgentSystem\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'agent_code' => $this->agent_code,
            'level' => $this->agent_level,
            'full_name' => $this->full_name,
            'nik' => $this->nik,
            'status' => $this->user->status,
            'registration_fee_status' => $this->registration_fee_status,
            'registration_fee_amount' => $this->registration_fee_amount,
            'upline' => $this->whenLoaded('upline', new AgentResource($this->upline)),
            'downlines_count' => $this->downlines()->count(),
            'address' => $this->whenLoaded('addresses', new AgentAddressResource($this->addresses->first())),
            'documents_status' => $this->documents->keyBy('document_type')->map->only(['verification_status']),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'bank_accounts' => $this->whenLoaded('bankAccounts', function () {
                return $this->bankAccounts->map(fn($b) => [
                    'bank_name'      => $b->bank_name,
                    'account_number' => $b->account_number,
                    'account_holder' => $b->account_holder,
                    'is_primary'     => $b->is_primary,
                ]);
            }),
            // documents array dengan file_url
            'documents' => $this->whenLoaded('documents', function () {
                return $this->documents->map(function ($doc) {
                    return [
                        'id' => $doc->id,
                        'document_type' => $doc->document_type,
                        'verification_status' => $doc->verification_status,
                        'rejection_reason' => $doc->rejection_reason,
                        'file_url' => $doc->file_url,        // ← URL gambar (dari accessor)
                        'file_path' => $doc->file_path,
                        'type_label' => $doc->type_label,    // ← "Foto KTP", dll
                        'uploaded_at' => $doc->created_at,
                    ];
                });
            }),
            //data user
            'user' => $this->whenLoaded('user', function () {
                return [
                    'email' => $this->user->email,
                    'phone' => $this->user->phone,
                    'whatsapp' => $this->user->whatsapp,
                ];
            }),
            'payment_id' => $this->payments()->latest()->first()?->id,
        ];
    }
}
