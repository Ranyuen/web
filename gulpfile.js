'use strict';
var cp = require('child_process');
var exec = require('gulp-exec'),
    gulp = require('gulp'),
    jshint = require('gulp-jshint'),
    jshintStylish = require('jshint-stylish'),
    less = require('gulp-less'),
    Promise = require('bluebird'),
    uglify = require('gulp-uglifyjs'),
    concat = require('gulp-concat');

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

gulp.task('uglifyjs', function () {
  gulp.src([
      'src/bower_components/jquery/dist/jquery.min.js',
      'src/javascripts/messageForDeveloperFromRanyuen.js'
    ]).
    pipe(concat('layout.min.js')).
    pipe(uglify({
      outSourceMap: true,
      output: {},
      compress: { unsafe: true }
    })).
    pipe(gulp.dest('assets/javascripts'));
  gulp.src([
      'src/bower_components/colorbox/jquery.colorbox-min.js',
      'src/bower_components/colorbox/i18n/jquery.colorbox-ja.js',
      'src/bower_components/masonry/dist/masonry.pkgd.min.js',
      'src/bower_components/uri.js/src/URI.min.js',
      'src/bower_components/hogan/web/builds/3.0.2/hogan-3.0.2.min.js',
      'src/javascripts/photoGallery.js'
    ]).
    pipe(concat('photoGallery.min.js')).
    pipe(uglify({
      outSourceMap: true,
      output:       {},
      compress:     { unsafe: true }
    })).
    pipe(gulp.dest('assets/javascripts'));
});

gulp.task('jshint', function () {
  return gulp.src(['*.js', 'src/javascripts/*.js']).
    pipe(jshint()).
    pipe(jshint.reporter(jshintStylish));
});

gulp.task('less', function () {
  return gulp.src(['src/stylesheets/layout.less']).
    pipe(less({
      compress:  true,
      sourceMap: true
    })).
    pipe(gulp.dest('assets/stylesheets'));
});

gulp.task('php-fixer', function (done) {
  Promise.all(['lib/'].map(function (path) {
    return promiseProcess('php php-cs-fixer.phar fix ' + path + ' --level=all');
  })).then(function (stdouts) {
    stdouts.forEach(function (stdout) { console.log(stdout); });
    done();
  }).catch(function (err) { done(err); });
});

gulp.task('php-lint', function () {
  return gulp.src(['lib/**/**.php', 'test/**/**.php']).
    pipe(exec('php -l <%= file.path %>', {})).
    pipe(exec.reporter({}));
});

gulp.task('php-unit', function (done) {
  return cp.exec('vendor/bin/phpunit test', function (err, stdout, stderr) {
    console.log(stdout);
    process.stderr.write(stderr);
    done(err);
  });
});

gulp.task('build', ['less', 'uglifyjs']);
gulp.task('test', ['jshint', 'php-lint', 'php-fixer', 'php-unit']);
