<?php

namespace Ranyuen\Model;

use Illuminate\Database\Eloquent;

/**
 * ExamQuestion model.
 */
class ExamQuestion extends Eloquent\Model
{
    protected $table   = 'exam_question';
    protected $guarded = ['id'];

    public static function readFromJSON($json_file) {
        if (isset($json_file)) {
            $exams = json_decode(file_get_contents($json_file), true);
            // select type easy, hard, expert, photo
            if ($exams['name'] === '蘭検定 (初級)') {
                $type = 'easy';
            } else if ($exams['name'] === '蘭検定 (上級)') {
                $type = 'hard';
            } else if ($exams['name'] === '蘭検定 (博士編)') {
                $type = 'expert';
            } else if ($exams['name'] === '蘭検定 (写真編)') {
                $type = 'photo';
            }
            $exams = $exams['questions'];
            // register question
            foreach ($exams as $exam) {
                foreach ($exam['answers'] as $answer) {
                    $q = new ExamQuestion(
                        [
                            'question' => $exam['q'],
                            'type'     => $type
                        ]
                    );
                    $q->save();
                    ExamAnswer::registerExamAnswer($q->id, $answer);
                }
            }
        }
    }
}
