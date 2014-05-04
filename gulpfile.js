'use strict';
var cp = require('child_process');
var exec = require('gulp-exec'),
    gulp = require('gulp'),
    jshint = require('gulp-jshint'),
    jshintStylish = require('jshint-stylish'),
    less = require('gulp-less'),
    Promise = require('bluebird');

/**
 * @param {string} cmd
 * @return {Promise.<string>}
 */
function childPromise(cmd) {
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

gulp.task('php-fixer', function () {
  Promise.all(['lib/'].map(function (path) {
    return childPromise('php php-cs-fixer.phar fix ' + path + ' --level=all');
  })).then(function (stdouts) {
    stdouts.forEach(function (stdout) { console.log(stdout); });
  });
});

gulp.task('php-lint', function () {
  gulp.src(['lib/**/**.php', 'test/**/**.php']).
    pipe(exec('php -l <%= file.path %>', {})).
    pipe(exec.reporter({}));
});

gulp.task('php-unit', function (done) {
  cp.exec('vendor/bin/phpunit test', function (err, stdout, stderr) {
    console.log(stdout);
    process.stderr.write(stderr);
    done(err);
  });
});

gulp.task('build', ['less']);
gulp.task('test', ['jshint', 'php-lint', 'php-fixer', 'php-unit']);
