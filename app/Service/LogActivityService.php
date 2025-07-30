<?php

namespace App\Service;

use App\Models\LogActivity;

class LogActivityService
{
    public static function write($act, string|null $desc = null) {
        LogActivity::create([
            'user_id' => auth()->user()->id,
            'ip' => request()->ip(),
            'action' => $act,
            'desc' => $desc
        ]);
    }
}