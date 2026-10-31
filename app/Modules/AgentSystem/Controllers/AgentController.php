<?php
// app/Modules/AgentSystem/Controllers/AgentController.php
namespace App\Modules\AgentSystem\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AgentSystem\Requests\RegisterAgentRequest;
use App\Modules\AgentSystem\Requests\UpdateAgentAddressRequest;
use App\Modules\AgentSystem\Requests\UpdateAgentBankRequest;
use App\Modules\AgentSystem\Requests\UploadDocumentsRequest;
use App\Modules\AgentSystem\Resources\AgentResource;
use App\Modules\AgentSystem\Models\Agent;
use App\Modules\AgentSystem\Models\AgentAddress;
use App\Modules\AgentSystem\Models\AgentBankAccount;
use App\Modules\AgentSystem\Models\AgentDocument;
use App\Modules\AgentSystem\Models\Payment;
use App\Models\Region;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AgentController extends Controller
{
    // STEP 1: Basic Registration
    public function register(RegisterAgentRequest $request)
    {
        return DB::transaction(function () use ($request) {
            // Create user (tidak lagi menggunakan field 'role' yang sudah dihapus)
            $user = User::create([
                'email' => $request->email,
                'phone' => $request->phone,
                'whatsapp' => $request->whatsapp,
                'password' => Hash::make($request->password),
                'status' => 'pending'
            ]);

            // Assign role 'agent' ke user via user_roles pivot table
            $user->assignRole('agent');

            // Get upline with feature flag validation
            $uplineRule = config('business_rules.agent_requires_upline')
                ? 'required|exists:agents,id'
                : 'nullable|exists:agents,id';

            $uplineId = null;
            if ($request->upline_agent_code) {
                $upline = Agent::where('agent_code', $request->upline_agent_code)->firstOrFail();
                $uplineId = $upline->id;
            }

            // Create agent
            $registrationFee = Setting::get('registration_fee_' . $request->agent_level, 0);
            $agent = $user->agent()->create([
                'agent_level' => $request->agent_level,
                'nik' => $request->nik,
                'job' => $request->job,
                'company_name' => $request->company_name,
                'upline_id' => $uplineId,
                'registration_fee_amount' => $registrationFee,
                'registration_fee_status' => 'pending',
            ]);

            // Create API token for the new user to continue Steps 2-5
            $token = $user->createToken('registration-token')->plainTextToken;

            return response()->json([
                'message' => 'Registrasi berhasil. Silakan lanjut ke step berikutnya.',
                'agent' => new AgentResource($agent->load(['user', 'upline', 'addresses'])),
                'token' => $token,
            ]);
        });
    }

    // STEP 2: Upload Documents (KTP + Selfie)
    public function uploadDocuments(UploadDocumentsRequest $request, Agent $agent)
    {
        $this->authorizeForUser($request->user(), $agent);

        $fileTypes = ['ktp_photo', 'selfie_ktp'];
        $uploadPath = 'agents/' . $agent->agent_code;

        foreach ($fileTypes as $type) {
            $file = $request->file($type);
            if (!$file) continue;
            $path = $file->store($uploadPath, 'public');

            AgentDocument::updateOrCreate(
                ['agent_id' => $agent->id, 'document_type' => $type],
                [
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]
            );
        }

        return response()->json([
            'message' => 'Dokumen berhasil diupload',
            'documents' => $agent->documents->keyBy('document_type')
        ]);
    }

    // STEP 3: Update Address
    public function updateAddress(UpdateAgentAddressRequest $request, Agent $agent)
    {
        $this->authorizeForUser($request->user(), $agent);

        AgentAddress::updateOrCreate(
            ['agent_id' => $agent->id],
            [
                'province_id' => $request->province_id,
                'regency_id' => $request->regency_id,
                'district_id' => $request->district_id,
                'village' => $request->village,
                'full_address' => $request->full_address,
                'postal_code' => $request->postal_code,
            ]
        );

        return new AgentResource($agent->load(['addresses.province', 'addresses.regency', 'addresses.district']));
    }

    // STEP 4: Update Bank Account
    public function updateBank(UpdateAgentBankRequest $request, Agent $agent)
    {
        $this->authorizeForUser($request->user(), $agent);

        AgentBankAccount::updateOrCreate(
            ['agent_id' => $agent->id, 'is_primary' => true],
            [
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_holder' => $request->account_holder,
            ]
        );

        return response()->json(['message' => 'Rekening bank berhasil disimpan']);
    }

    // STEP 5: Upload Payment Proof
    public function uploadPaymentProof(Request $request, Agent $agent)
    {
        $this->authorizeForUser($request->user(), $agent);

        $request->validate([
            'transfer_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $file = $request->file('transfer_proof');
        $path = $file->store('payments/' . $agent->agent_code, 'public');

        $document = AgentDocument::updateOrCreate(
            ['agent_id' => $agent->id, 'document_type' => 'transfer_proof'],
            [
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]
        );

        // Create payment record
        Payment::create([
            'agent_id' => $agent->id,
            'payment_type' => 'registration_fee',
            'amount' => $agent->registration_fee_amount,
            'payment_method' => 'transfer',
            'proof_document_id' => $document->id,
            'status' => 'pending',
        ]);

        $agent->update(['registration_fee_status' => 'paid']);

        return response()->json([
            'message' => 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.',
            'payment_status' => 'pending'
        ]);
    }

    // Get current agent status
    public function status(Agent $agent)
    {
        $this->authorizeForUser(request()->user(), $agent);

        return new AgentResource($agent->load([
            'user',
            'addresses',
            'documents',
            'bankAccounts',
            'payments' => fn($q) => $q->latest()
        ]));
    }

    /**
     * Authorize user untuk mengakses agent tertentu
     * Menggunakan relasi agent dari user via user->agent (bukan agent_id di users)
     */
    private function authorizeForUser($user, Agent $agent)
    {
        // Cek apakah user adalah admin (sekarang pengecekan dari roles table)
        if ($user->isAdmin()) {
            return true;
        }

        // Cek apakah user memiliki agent yang sama dengan agent yang diakses
        //Gunakan $user->agent?->id karena agent adalah relasi One-to-One
        if ($user->agent && $user->agent->id !== $agent->id) {
            abort(403, 'Unauthorized');
        }
    }
}
