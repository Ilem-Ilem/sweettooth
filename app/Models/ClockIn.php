<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ClockIn extends Model
{
    public $fillable = [
        'employee_id',
        'clock_in_time',
        'clock_out_time',
        'date', 'shift'
    ];


    public function employee(){
        return $this->belongsTo(Employee::class);
    }

    public function getShift(){
        
    }
}
