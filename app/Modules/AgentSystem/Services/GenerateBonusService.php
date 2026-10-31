<?php

namespace App\Modules\AgentSystem\Services;

use App\Modules\AgentSystem\Models\Agent;
use App\Modules\AgentSystem\Models\BonusTransaction;
use App\Models\PilgrimProfile;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateBonusService
{
    //Generate semua bonus untuk pilgrim yang baru verified
    //Logic sesuai STRUKTUR_AGEN_DAN_BONUS_ASSALAM.txt
    public static function generateForPilgrim(PilgrimProfile $pilgrim): array
    {
        return DB::transaction(function () use ($pilgrim) {
            $agent = $pilgrim->agent;
            $bonuses = [];

            // 1. Fixed fee — Rp 2.000.000 untuk agen rekrut langsung
            if ($agent) {
                $fixedFee = Setting::get('fixed_fee_per_pilgrim', 2000000);
                $bonuses[] = BonusTransaction::create([
                    'agent_id' => $agent->id,
                    'pilgrim_id' => $pilgrim->id,
                    'from_agent_id' => null,
                    'bonus_type' => 'fixed_fee',
                    'amount' => $fixedFee,
                    'description' => 'Fixed fee - ' . optional($pilgrim->user)->name,
                    'status' => 'pending',
                ]);
            }

            // 2. Coaching fee — Rp 100.000 untuk upline langsung
            if ($agent && $agent->upline_id) {
                $coachingFee = Setting::get('coaching_fee_per_pilgrim', 100000);
                $bonuses[] = BonusTransaction::create([
                    'agent_id' => $agent->upline_id,
                    'pilgrim_id' => $pilgrim->id,
                    'from_agent_id' => $agent->id,
                    'bonus_type' => 'coaching_fee',
                    'amount' => $coachingFee,
                    'description' => 'Coaching fee - ' . optional($pilgrim->user)->name,
                    'status' => 'pending',
                ]);
            }

            // 3. Region bonus — Rp 25.000 untuk cabang wilayah
            if ($agent) {
                $cabang = Agent::join('agent_addresses as aa_dom', 'agents.id', '=', 'aa_dom.agent_id')
                    ->where('agents.agent_level', 'cabang')
                    ->where('aa_dom.is_domicile', true)
                    ->whereRaw('aa_dom.regency_id = (SELECT regency_id FROM agent_addresses WHERE agent_id = ? AND is_domicile = true LIMIT 1)', [$agent->id])
                    ->select('agents.id')
                    ->first();

                if ($cabang) {
                    $regionBonus = Setting::get('region_bonus_per_pilgrim', 25000);
                    $bonuses[] = BonusTransaction::create([
                        'agent_id' => $cabang->id,
                        'pilgrim_id' => $pilgrim->id,
                        'from_agent_id' => $agent->id,
                        'bonus_type' => 'region_bonus',
                        'amount' => $regionBonus,
                        'description' => 'Region bonus - ' . optional($pilgrim->user)->name,
                        'status' => 'pending',
                    ]);
                }
            }

            Log::info('Bonus generated for pilgrim #' . $pilgrim->id . ': ' . count($bonuses) . ' records');

            return $bonuses;
        });
    }
}
