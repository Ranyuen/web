'use strict';
var cp = require('child_process'),
    fs = require('fs');
var Promise       = require('bluebird'),
    gulp          = require('gulp'),
    concat        = require('gulp-concat'),
    exec          = require('gulp-exec'),
    jshint        = require('gulp-jshint'),
    less          = require('gulp-less'),
    merge         = require('merge-stream'),
    runSequence   = require('run-sequence'),
    uglify        = require('gulp-uglifyjs'),
    jshintStylish = require('jshint-stylish');

/**
 * @param {string} cmd
 * @return {Promise.<string>}
 */
function promiseProcess(cmd) {
  return new Promise(function (resolve, reject) {
    cp.exec(cmd, function (err, stdout, stderr) {
      if (err) {
        process.stderr.write(stderr);
        return reject(err);
      }
      resolve(stdout);
    });
  });
}

gulp.task('copy-assets', function () {
  return gulp.src('src/bower_components/colorbox/example1/**').
    pipe(gulp.dest('assets/stylesheets'));
});

gulp.task('uglifyjs', function () {
  var layout, photoGallery;

  layout = gulp.src([
      'src/bower_components/jquery/dist/jquery.min.js',
      'src/javascripts/messageForDeveloperFromRanyuen.js',
    ]).
    pipe(concat('layout.min.js')).
    pipe(uglify({
      outSourceMap: true,
      output:       {},
      compress:     { unsafe: true },
    })).
    pipe(gulp.dest('assets/javascripts'));
  photoGallery = gulp.src([
      'src/bower_components/colorbox/jquery.colorbox-min.js',
      'src/bower_components/colorbox/i18n/jquery.colorbox-ja.js',
      'src/bower_components/masonry/dist/masonry.pkgd.min.js',
      'src/bower_components/uri.js/src/URI.min.js',
      'src/bower_components/hogan/web/builds/3.0.2/hogan-3.0.2.min.js',
      'src/javascripts/photoGallery.js',
    ]).
    pipe(concat('photoGallery.min.js')).
    pipe(uglify({
      outSourceMap: true,
      output:       {},
      compress:     { unsafe: true },
    })).
    pipe(gulp.dest('assets/javascripts'));
  return merge(layout, photoGallery);
});

gulp.task('jshint', function () {
  return gulp.src(['*.js', 'src/javascripts/*.js']).
    pipe(jshint()).
    pipe(jshint.reporter(jshintStylish));
});

gulp.task('less', function () {
  return gulp.src([
      'src/stylesheets/layout.less',
      'src/stylesheets/photoGallery.less',
    ]).
    pipe(less({
      compress:  true,
      sourceMap: true,
    })).
    pipe(gulp.dest('assets/stylesheets'));
});

gulp.task('nav', function () {
  return Promise.all([
    promiseProcess('rake nav:nav'),
    promiseProcess('rake nav:sitemap'),
  ]).then(function (outs) {
    outs.forEach(function (out) { console.log(out); });
  });
});

gulp.task('php-fixer', function () {
  return Promise.all([
    'lib/',
  ].map(function (path) {
    return promiseProcess('php php-cs-fixer.phar fix ' + path + ' --level=all');
  })).then(function (outs) {
    outs.forEach(function (out) { console.log(out); });
  });
});

gulp.task('php-lint', function () {
  return gulp.src(['lib/**/**.php', 'test/**/**.php']).
    pipe(exec('php -l <%= file.path %>', {})).
    pipe(exec.reporter({}));
});

gulp.task('php-test', function (done) {
  runSequence('php-lint', 'php-fixer', 'php-unit', done);
});

gulp.task('php-unit', function () {
  return promiseProcess('vendor/bin/phpunit test').
    then(function (out) { console.log(out); });
});

gulp.task('build', ['copy-assets', 'less', 'uglifyjs', 'nav']);
gulp.task('test', ['jshint', 'php-test']);
