'use strict';
var gulp = require('gulp'),
    jshint = require('gulp-jshint'),
    jshintStylish = require('jshint-stylish'),
    less = require('gulp-less');

gulp.task('jshint', function () {
  gulp.src(['*.js', 'assets/javascripts/*.js']).
    pipe(jshint()).
    pipe(jshint.reporter(jshintStylish));
});

gulp.task('less', function () {
  gulp.src(['assets/stylesheets/layout.less']).
    pipe(less({
      compress:  true,
      sourceMap: true
    })).
    pipe(gulp.dest('assets/stylesheets'));
});

gulp.task('build', ['less']);
gulp.task('test', ['jshint']);
