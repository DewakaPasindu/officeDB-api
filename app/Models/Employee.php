<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';
    protected $primaryKey = 'emp_id';

    protected $fillable = [
        'emp_name',
        'email',
        'dept_id',
        'salary',
        'city'
    ];

    public $timestamps = false;
}
