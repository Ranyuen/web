#!/usr/bin/env php
<?php
require 'vendor/autoload.php';

use Ranyuen\Model\ExamQuestion;

(new Ranyuen\App())->container['db'];

// ExamQuestion::readFromJSON('assets/exam/easy.json');
// ExamQuestion::readFromJSON('assets/exam/hard.json');
// ExamQuestion::readFromJSON('assets/exam/expert.json');
// ExamQuestion::readFromJSON('assets/exam/photo.json');
// ExamQuestion::readFromJSON('assets/exam/20161217_photo.json');
// ExamQuestion::readFromJSON('assets/exam/20161224_photo.json');
// ExamQuestion::readFromJSON('assets/exam/20170311_photo.json');
// ExamQuestion::readFromJSON('assets/exam/20170311_2_photo.json');
// ExamQuestion::readFromJSON('assets/exam/20180929_photo.json');
// ExamQuestion::readFromJSON('assets/exam/20181117_photo.json');
// ExamQuestion::readFromJSON('assets/exam/20181118_photo.json');
// ExamQuestion::readFromJSON('assets/exam/20181119_photo.json');
ExamQuestion::readFromJSON('assets/exam/20211227_photo.json');