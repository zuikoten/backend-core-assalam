<?php
// app/Modules/AgentSystem/Controllers/BonusController.php
namespace App\Modules\AgentSystem\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AgentSystem\Models\Agent;
use App\Modules\AgentSystem\Models\BonusTransaction;
use App\Modules\AgentSystem\Models\Pilgrim;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BonusController extends Controller
{
    public function calculatePilgrimBonus(Request $request, $pilgrimProfileId)
    {
        $pilgrim = \App\Models\PilgrimProfile::findOrFail($pilgrimProfileId);
        $agent = $pilgrim->agent;

        $bonuses = [];

        // 1. Fixed fee — selalu untuk agen yang rekrut
        if ($agent) {
            $fixedFee = Setting::get('fixed_fee_per_pilgrim', 2000000);
            $bonuses[] = BonusTransaction::create([
                'agent_id' => $agent->id,
                'pilgrim_id' => $pilgrim->id,
                'from_agent_id' => null,
                'bonus_type' => 'fixed_fee',
                'amount' => $fixedFee,
                'description' => 'Fee tetap per jamaah - ' . $pilgrim->user->name,
            ]);
        }

        // 2. Coaching fee — jika ada upline
        if ($agent && $agent->upline_id) {
            $coachingFee = Setting::get('coaching_fee_per_pilgrim', 100000);
            $bonuses[] = BonusTransaction::create([
                'agent_id' => $agent->upline_id,
                'pilgrim_id' => $pilgrim->id,
                'from_agent_id' => $agent->id,
                'bonus_type' => 'coaching_fee',
                'amount' => $coachingFee,
                'description' => 'Fee pembinaan - ' . $pilgrim->user->name,
            ]);
        }

        // 3. Region bonus — cabang wilayah (implement query dari STRUKTUR doc)
        if ($agent) {
            $regionBonusAgent = Agent::join('agent_addresses as aa', 'agents.id', '=', 'aa.agent_id')
                ->where('agents.agent_level', 'cabang')
                ->where('aa.is_domicile', true)
                ->whereColumn('aa.regency_id', DB::raw('(SELECT regency_id FROM agent_addresses WHERE agent_id = ' . $agent->id . ' AND is_domicile = true LIMIT 1)'))
                ->select('agents.id')
                ->first();

            if ($regionBonusAgent) {
                $regionBonus = Setting::get('region_bonus_per_pilgrim', 25000);
                $bonuses[] = BonusTransaction::create([
                    'agent_id' => $regionBonusAgent->id,
                    'pilgrim_id' => $pilgrim->id,
                    'from_agent_id' => $agent->id,
                    'bonus_type' => 'region_bonus',
                    'amount' => $regionBonus,
                    'description' => 'Bonus wilayah - ' . $pilgrim->user->name,
                ]);
            }
        }

        return response()->json([
            'message' => 'Bonus berhasil dihitung',
            'bonuses' => $bonuses,
        ]);
    }

    public function agentBonuses(Agent $agent)
    {
        $bonuses = BonusTransaction::with('payout')
            ->where('agent_id', $agent->id)
            ->latest()
            ->paginate(20);

        return response()->json($bonuses);
    }

    /**
     * Admin: List pending bonus payouts per agent
     */
    public function pendingPayouts()
    {
        $payouts = \App\Modules\AgentSystem\Models\AgentBonusPayout::with(['agent', 'bankAccount'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        return response()->json($payouts);
    }

    /**
     * Admin: Create payout for agent's pending bonuses
     */
    public function storePayout(Request $request, Agent $agent)
    {
        $request->validate([
            'bank_account_id' => 'required|exists:agent_bank_accounts,id',
            'notes' => 'nullable|string'
        ]);

        $pendingBonuses = BonusTransaction::where('agent_id', $agent->id)
            ->where('status', 'pending')
            ->get();

        if ($pendingBonuses->isEmpty()) {
            return response()->json(['error' => 'No pending bonuses'], 400);
        }

        $totalAmount = $pendingBonuses->sum('amount');

        $payout = \App\Modules\AgentSystem\Models\AgentBonusPayout::create([
            'agent_id' => $agent->id,
            'bank_account_id' => $request->bank_account_id,
            'amount' => $totalAmount,
            'status' => 'pending',
            'notes' => $request->notes,
            'processed_by' => auth()->id()
        ]);

        // Link all pending bonuses to this payout
        $pendingBonuses->each(function ($bonus) use ($payout) {
            $bonus->update(['payout_id' => $payout->id]);
        });

        return response()->json([
            'message' => 'Payout created successfully',
            'payout' => $payout->load('agent', 'bankAccount', 'bonusTransactions')
        ]);
    }

    /**
     * Admin: Process/confirm payout (mark as transferred)
     */
    public function processPayout(Request $request, $payoutId)
    {
        $request->validate([
            'transaction_ref' => 'nullable|string|max:100'
        ]);

        $payout = \App\Modules\AgentSystem\Models\AgentBonusPayout::findOrFail($payoutId);
        $payout->update([
            'status' => 'processed',
            'paid_at' => now(),
            'transaction_ref' => $request->transaction_ref
        ]);

        // Mark linked bonuses as paid
        $payout->bonusTransactions()->update(['status' => 'paid']);

        return response()->json([
            'message' => 'Payout processed successfully',
            'payout' => $payout->fresh()->load('agent', 'bankAccount')
        ]);
    }

    /**
     * Get agent's payout history
     */
    public function agentPayoutHistory(Agent $agent)
    {
        $payouts = \App\Modules\AgentSystem\Models\AgentBonusPayout::with(['bankAccount', 'bonusTransactions'])
            ->where('agent_id', $agent->id)
            ->latest()
            ->paginate(10);

        return response()->json($payouts);
    }
}

