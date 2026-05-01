<?php
// app/Modules/AgentSystem/Controllers/AdminController.php
namespace App\Modules\AgentSystem\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AgentSystem\Requests\VerifyDocumentRequest;
use App\Modules\AgentSystem\Requests\VerifyPaymentRequest;
use App\Modules\AgentSystem\Resources\AgentResource;
use App\Modules\AgentSystem\Models\Agent;
use App\Modules\AgentSystem\Models\AgentDocument;
use App\Modules\AgentSystem\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // 📊 DASHBOARD STATS
    public function dashboard()
    {
        return response()->json([
            'stats' => [
                'pending_agents' => Agent::whereHas('user', fn($q) => $q->where('status', 'pending'))->count(),
                'approved_agents' => Agent::whereHas('user', fn($q) => $q->where('status', 'active'))->count(),
                'pending_payments' => Payment::where('status', 'pending')->count(),
                'pending_documents' => AgentDocument::where('verification_status', 'pending')->count(),
                'total_revenue' => Payment::where('status', 'success')->where('payment_type', 'registration_fee')->sum('amount'),
            ],
            'recent_agents' => AgentResource::collection(
                Agent::with(['user', 'documents'])
                    ->whereHas('user', fn($q) => $q->whereIn('status', ['pending', 'approved']))
                    ->latest()
                    ->limit(10)
                    ->get()
            ),
            'recent_payments' => Payment::with(['agent.user', 'document'])
                ->where('status', 'pending')
                ->latest()
                ->limit(5)
                ->get()
        ]);
    }

    // 📋 LIST PENDING AGENTS
    public function pendingAgents(Request $request)
    {
        $query = Agent::with([
            'user',
            'documents' => fn($q) => $q->select(['agent_id', 'document_type', 'verification_status']),
            'payments' => fn($q) => $q->latest()->limit(1),
            'addresses',
            'upline.user'
        ])->whereHas('user', fn($q) => $q->where('status', 'pending'));

        if ($request->search) {
            $query->where(
                fn($q) =>
                $q->where('full_name', 'like', "%{$request->search}%")
                    ->orWhere('nik', 'like', "%{$request->search}%")
            );
        }

        $agents = $query->latest()->paginate(15);

        return response()->json([
            'data' => AgentResource::collection($agents),
            'pagination' => [
                'current_page' => $agents->currentPage(),
                'last_page' => $agents->lastPage(),
                'per_page' => $agents->perPage(),
                'total' => $agents->total(),
            ]
        ]);
    }

    // ✅ VERIFY DOCUMENTS
    public function verifyDocuments(VerifyDocumentRequest $request, AgentDocument $document)
    {
        $document->update([
            'verification_status' => $request->status,
            'rejection_reason' => $request->status === 'rejected' ? $request->reason : null,
        ]);

        // Auto-approve agent if all documents approved + payment verified
        $agent = $document->agent->loadCount('documents');
        $allDocsApproved = $agent->documents->every(
            fn($doc) =>
            $doc->verification_status === 'approved'
        );

        if ($allDocsApproved && $agent->registration_fee_status === 'verified') {
            $agent->user->update(['status' => 'active']);
            $agent->update(['registration_fee_status' => 'verified']);
        }

        return response()->json([
            'message' => $request->status === 'approved'
                ? 'Dokumen berhasil disetujui'
                : 'Dokumen ditolak',
            'document' => $document->fresh(),
            'agent_status' => $agent->user->status
        ]);
    }

    // 💰 VERIFY PAYMENT
    public function verifyPayment(VerifyPaymentRequest $request, Payment $payment)
    {
        $payment->update(['status' => $request->status]);

        if ($request->status === 'success') {
            $agent = $payment->agent;
            $agent->update(['registration_fee_status' => 'verified']);

            // Check if all requirements met for activation
            $allDocsApproved = $agent->documents->every(
                fn($doc) =>
                $doc->verification_status === 'approved'
            );

            if ($allDocsApproved) {
                $agent->user->update(['status' => 'active']);
            }
        }

        return response()->json([
            'message' => $request->status === 'success'
                ? 'Pembayaran berhasil diverifikasi'
                : 'Pembayaran ditolak',
            'payment' => $payment->fresh(),
            'agent_status' => $payment->agent->user->status
        ]);
    }

    // 👤 APPROVE/REJECT AGENT
    public function updateAgentStatus(Request $request, Agent $agent)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'reason' => 'nullable|string|max:500',
        ]);

        $agent->user->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => $request->status === 'approved'
                ? 'Agen berhasil disetujui'
                : 'Agen ditolak',
            'agent' => new AgentResource($agent->fresh()->load('user')),
        ]);
    }

    // 📈 AGENT REPORTS
    public function agentReports(Request $request)
    {
        $level = $request->get('level', 'all');
        $status = $request->get('status', 'active');

        $query = Agent::withCount([
            'downlines',
            'payments as payment_total' => fn($q) => $q->where('status', 'success'),
            'bonusTransactions as bonus_total' => fn($q) => $q->where('status', 'paid')
        ]);

        if ($level !== 'all') {
            $query->where('agent_level', $level);
        }

        if ($status !== 'all') {
            $query->whereHas('user', fn($q) => $q->where('status', $status));
        }

        $agents = $query->latest()->paginate(20);

        return response()->json($agents);
    }
}
