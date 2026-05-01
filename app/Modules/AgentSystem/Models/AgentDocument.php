<?php
// app/Modules/AgentSystem/Models/AgentDocument.php
namespace App\Modules\AgentSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AgentDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'document_type',
        'file_path',
        'file_size',
        'mime_type',
        'verification_status',
        'rejection_reason'
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    const TYPES = [
        'ktp_photo' => 'Foto KTP',
        'selfie_ktp' => 'Selfie dengan KTP',
        'transfer_proof' => 'Bukti Transfer'
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function getTypeLabelAttribute()
    {
        return self::TYPES[$this->document_type] ?? $this->document_type;
    }

    public function getFileUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    public function scopePending($query)
    {
        return $query->where('verification_status', 'pending');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }
}
