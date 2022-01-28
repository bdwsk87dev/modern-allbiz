<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_name',
        'campaign_id',
        'customer_id',
        'link_campaign',
        'is_smart',
        'phase',
        'active',
        'last_check_date',
        'status',
        'startDate',
        'endDate',
        'last_check_date'
    ];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

}
