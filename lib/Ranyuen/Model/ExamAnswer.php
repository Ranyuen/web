<?php

namespace Ranyuen\Model;

use Illuminate\Database\Eloquent;

/**
 * ExanAnswer Model
 */
class ExamAnswer extends Eloquent\Model
{
    protected $table   = 'exam_answer';
    protected $guarded = ['id'];

    public function question() {

        return $this->belongsTo(ExamQuestion::class);
    }

    public static function registerExamAnswer($_id, array $answer) {
        if (! empty($answer)) {
            foreach ($answer['choices'] as $key => $choice) {
                $a = new ExamAnswer(
                    [
                        'question_id' => $_id,
                        'choice'      => $choice,
                        'is_correct'  => ($key + 1) === $answer['ans'] ? 1 : 0
                    ]
                );
                $a->save();
            }
        }
    }
}
