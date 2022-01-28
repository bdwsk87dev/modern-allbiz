<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Models\Customer;

class TaskLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'customer_id',
        'campaign_id',
        'task',
        'message',
        'task_id'
    ];

    public function getCreatedAtAttribute($date) {
        $nDate = new Carbon($date);
        return $nDate->format('m-d H:i:s');
    }

    // work!
    public function customer(){
        return $this->hasOne('App\Models\Customer','customer_id','customer_id');
    }

}
