<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalBusinessConfiguration extends Model
{
    protected $fillable = ['company_name', 'logo_upload', 'contact_details', 'business_type', 'storage_settings', 'subscription_plan'];

    protected $casts = [
        'contact_details' => 'array',
        'business_type' => 'array',
        'storage_settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
