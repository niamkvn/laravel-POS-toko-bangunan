<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['prefix', 'date', 'counter'])]
class NumberSequence extends Model
{
    //
}
