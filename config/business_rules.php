<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Business Rules Configuration
    |--------------------------------------------------------------------------
    |
    | Feature flags dan aturan bisnis yang bisa diubah via .env
    |
    */

    'agent_requires_upline' => env('AGENT_REQUIRES_UPLINE', false),

];
