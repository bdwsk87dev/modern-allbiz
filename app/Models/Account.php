<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\Encryptable;

class Account extends Model
{
    use HasFactory;
    use Encryptable;

    protected $fillable = [
        'account_name',
        'client_customer_id',
        'client_id',
        'description',
        'developer_token',
        'client_id',
        'client_secret',
        'refresh_token',
        'active',
        'email',
        'testing',
        'seven_days'
    ];

    protected $encryptable = [
        'client_id',
        'developer_token',
        'client_secret',
        'refresh_token'
    ];

    public function customers()
    {
        return $this->hasMany('App\Models\Customer');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }


}
