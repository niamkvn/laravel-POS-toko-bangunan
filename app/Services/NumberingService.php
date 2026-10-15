<?php

namespace App\Services;

use App\Models\NumberSequence;
use Illuminate\Support\Facades\DB;

class NumberingService
{
    public function next(string $prefix): string
    {
        $date = now()->format('Ymd');

        return DB::transaction(function () use ($prefix, $date) {
            $sequence = NumberSequence::where('prefix', $prefix)->where('date', $date)->lockForUpdate()->first();

            if (! $sequence) {
                $sequence = NumberSequence::create(['prefix' => $prefix, 'date' => $date, 'counter' => 0]);
            }

            $sequence->increment('counter');

            return sprintf('%s-%s-%04d', $prefix, $date, $sequence->counter);
        });
    }
}
