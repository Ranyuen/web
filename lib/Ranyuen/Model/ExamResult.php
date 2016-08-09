<?php

namespace Ranyuen\Model;

use Illuminate\Database\Eloquent;

/**
 * ExanAnswer Model
 */
class ExamResult extends Eloquent\Model
{
    protected $table   = 'exam_result';
    protected $guarded = ['id'];
}
