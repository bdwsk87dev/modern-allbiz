<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'account_id',
        'customer_id',
        'can_manage_clients',
        'active',
        'last_check_date'
    ];

    public function campaigns()
    {
        return $this->hasMany('App\Models\Campaign');
    }

    public function account()
    {
        //return $this->belongsTo('App\Models\Account', 'customer_id', 'owner_key');
        return $this->belongsTo('App\Models\Account', 'account_id', 'id');
    }

    // work!
    public function taskLogs(){
        return $this->hasMany('App\Models\TaskLog','customer_id','customer_id');
    }

}

