<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['code', 'label', 'threshold_value', 'role_required', 'active'])]
class ApprovalThreshold extends Model
{
    protected function casts(): array
    {
        return ['threshold_value' => 'decimal:2', 'active' => 'boolean'];
    }
}
