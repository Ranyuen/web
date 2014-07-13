'use strict';
var cp = require('child_process');
var exec = require('gulp-exec'),
    gulp = require('gulp'),
    jshint = require('gulp-jshint'),
    jshintStylish = require('jshint-stylish'),
    less = require('gulp-less'),
    Promise = require('bluebird'),
    uglify = require('gulp-uglify'),
    concat = require('gulp-concat');

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

gulp.task('uglifyjs_layout', function () {
  gulp.src(['assets/bower_components/jquery/dist/jquery.min.js',
            'assets/javascripts/messageForDeveloperFromRanyuen.js'
          ]).
    pipe(concat('layout_js_min.js')).
    pipe(uglify()).
    pipe(gulp.dest('assets/javascripts'));
});

gulp.task('uglifyjs_photo', function () {
  gulp.src(['assets/bower_components/colorbox/jquery.colorbox-min.js',
            'assets/bower_components/colorbox/i18n/jquery.colorbox-ja.js',
            'assets/bower_components/masonry/dist/masonry.pkgd.min.js',
            'assets/bower_components/uri.js/src/URI.min.js',
            'assets/bower_components/hogan/web/builds/3.0.2/hogan-3.0.2.min.js',
            'assets/javascripts/photoGallery.js'
            ]).
  pipe(concat('photo_js_min.js')).
  pipe(uglify()).
  pipe(gulp.dest('assets/javascripts'));
})

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

gulp.task('build', ['less', 'uglifyjs_layout', 'uglifyjs_photo']);
gulp.task('test', ['jshint', 'php-lint', 'php-fixer', 'php-unit']);
