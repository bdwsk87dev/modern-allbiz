<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class DailyTaskLogs extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'customer_id',
        'campaign_id',
        'message',
        'task_id'
    ];

    public function getCreatedAtAttribute($date) {
        $nDate = new Carbon($date);
        return $nDate->format('m-d H:i:s');
    }
}
