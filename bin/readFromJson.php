#!/usr/bin/env php
<?php
require 'vendor/autoload.php';

use Ranyuen\Model\ExamQuestion;

(new Ranyuen\App())->container['db'];

// ExamQuestion::readFromJSON('assets/exam/easy.json');
// ExamQuestion::readFromJSON('assets/exam/hard.json');
// ExamQuestion::readFromJSON('assets/exam/expert.json');
// ExamQuestion::readFromJSON('assets/exam/photo.json');
ExamQuestion::readFromJSON('assets/exam/20161217_photo.json');
