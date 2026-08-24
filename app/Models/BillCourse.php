<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillCourse extends Model
{
    protected $table = 'bill_course';

    protected $fillable = [
        'bill_id',
        'course_id',
        'price',
    ];
}
