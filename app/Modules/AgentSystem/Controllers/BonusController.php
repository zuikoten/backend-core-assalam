<?php
// app/Modules/AgentSystem/Controllers/BonusController.php
namespace App\Modules\AgentSystem\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\AgentSystem\Models\Agent;
use App\Modules\AgentSystem\Models\BonusTransaction;
use App\Modules\AgentSystem\Models\Pilgrim;
use App\Models\Setting;
use Illuminate\Http\Request;

class BonusController extends Controller
{
    public function calculatePilgrimBonus(Request $request, Pilgrim $pilgrim)
    {
        $agent = $pilgrim->agent;
        $branch = $pilgrim->branch;

        $bonuses = [];

        // 1. Fixed fee untuk agent premium
        if ($agent && $agent->agent_level === 'premium') {
            $fixedFee = Setting::get('fixed_fee_per_pilgrim', 2000000);
            $bonuses[] = BonusTransaction::create([
                'agent_id' => $agent->id,
                'pilgrim_count' => 1,
                'bonus_type' => 'fixed_fee',
                'amount' => $fixedFee,
                'description' => 'Fee tetap per jama\'ah - ' . $pilgrim->full_name,
            ]);
        }

        // 2. Coaching fee untuk cabang/upline
        if ($branch || $agent->upline_id) {
            $coachingFee = Setting::get('coaching_fee_per_pilgrim', 100000);
            $coachAgent = $branch ?? Agent::find($agent->upline_id);

            $bonuses[] = BonusTransaction::create([
                'agent_id' => $coachAgent->id,
                'pilgrim_count' => 1,
                'bonus_type' => 'coaching_fee',
                'amount' => $coachingFee,
                'description' => 'Fee pembinaan - ' . $pilgrim->full_name,
            ]);
        }

        // 3. Region bonus untuk cabang wilayah
        $regionBonus = Setting::get('region_bonus_per_pilgrim', 25000);
        // Logic region bonus...

        return response()->json([
            'message' => 'Bonus berhasil dihitung',
            'bonuses' => $bonuses,
        ]);
    }

    public function agentBonuses(Agent $agent)
    {
        $bonuses = BonusTransaction::with('payment')
            ->where('agent_id', $agent->id)
            ->latest()
            ->paginate(20);

        return response()->json($bonuses);
    }
}
