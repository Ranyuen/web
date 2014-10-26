/* global -Promise */
/* jshint node:true */
'use strict';
var cp = require('child_process');
var Promise       = require('bluebird'),
    gulp          = require('gulp'),
    concat        = require('gulp-concat'),
    exec          = require('gulp-exec'),
    jshint        = require('gulp-jshint'),
    less          = require('gulp-less'),
    ssh           = require('gulp-ssh'),
    merge         = require('merge-stream'),
    runSequence   = require('run-sequence'),
    uglify        = require('gulp-uglifyjs'),
    jshintStylish = require('jshint-stylish');

/**
 * @param {string} cmd
 * @return {Promise.<string>} stdout
 */
function promiseProcess(cmd) {
  return new Promise(function (resolve, reject) {
    cp.exec(cmd, function (err, stdout, stderr) {
      if (err) {
        console.log(stdout);
        console.error(stderr);
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

gulp.task('deploy', function () {
  return ssh.exec({
    command: [
      'cd ~/www; git pull origin master',
      'cd ~/www; php composer.phar install --no-dev',
      'cd ~/www; SERVER_ENV=production vendor/bin/phpmig migrate',
    ],
    sshConfig: {
      host:     'ranyuen.sakura.ne.jp',
      port:     '22',
      username: 'ranyuen',
      password: process.env.SSH_PASSWORD,
    }
  });
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
    // promiseProcess('rake nav:nav'),
    promiseProcess('rake nav:sitemap'),
  ]).then(function (outs) {
    outs.forEach(function (out) { console.log(out); });
  });
});

gulp.task('php-cs', function () {
  return Promise.all([
    '-l .',
    'lib/',
    'test/',
    'view/',
  ].map(function (path) {
    return promiseProcess('vendor/bin/phpcs --standard=PEAR,Zend --extensions=php ' + path);
  }));
});

gulp.task('php-fixer', function () {
  return Promise.all([
    'index.php',
    'phpmig.php',
    'config/',
    'lib/',
    'test/',
    'view/',
  ].map(function (path) {
    return promiseProcess('vendor/bin/php-cs-fixer fix ' + path + ' --level=all');
  }));
});

gulp.task('php-lint', function () {
  return gulp.src([
      '*.php',
      'config/**/**.php',
      'lib/**/**.php',
      'test/**/**.php',
      'view/**/**.php'
    ]).
    pipe(exec('php -l <%= file.path %>', {})).
    pipe(exec.reporter({stdout: false}));
});

gulp.task('php-test', function (done) {
  runSequence('php-lint', 'php-fixer', 'php-unit', done);
});

gulp.task('php-unit', function () {
  return promiseProcess('vendor/bin/phpunit test --strict').
    then(function (out) { console.log(out); });
});

gulp.task('uglifyjs', function () {
  var layout, photoGallery,
      uglifyOption = {
        outSourceMap: true,
        output:       {},
        compress:     { unsafe: true },
      };

  layout = gulp.src([
      'src/bower_components/jquery/dist/jquery.min.js',
      'src/bower_components/uri.js/src/URI.min.js',
      'src/javascripts/messageForDeveloperFromRanyuen.js',
    ]).
    pipe(concat('layout.min.js')).
    pipe(uglify(uglifyOption)).
    pipe(gulp.dest('assets/javascripts'));
  photoGallery = gulp.src([
      'src/bower_components/colorbox/jquery.colorbox-min.js',
      'src/bower_components/colorbox/i18n/jquery.colorbox-ja.js',
      'src/bower_components/hogan/web/builds/3.0.2/hogan-3.0.2.min.js',
      'src/bower_components/masonry/dist/masonry.pkgd.min.js',
      'src/javascripts/photoGallery.js',
    ]).
    pipe(concat('photoGallery.min.js')).
    pipe(uglify(uglifyOption)).
    pipe(gulp.dest('assets/javascripts'));
  return merge(layout, photoGallery);
});

gulp.task('build', ['copy-assets', 'less', 'uglifyjs', 'nav']);
gulp.task('test', ['jshint', 'php-test']);
