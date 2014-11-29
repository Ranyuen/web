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
    uglify        = require('gulp-uglifyjs'),
    jshintStylish = require('jshint-stylish'),
    merge         = require('merge-stream'),
    runSequence   = require('run-sequence');
var Check404   = require('./lib/Check404'),
    promiseSsh = require('./lib/promiseSsh');

/**
 * @param {string} cmd
 * @param {boolean=} doseIgnoreError
 * @return {Promise.<string>} stdout
 */
function promiseProcess(cmd, doseIgnoreError) {
  doseIgnoreError = !!doseIgnoreError;
  return new Promise(function (resolve, reject) {
    cp.exec(cmd, function (err, stdout, stderr) {
      if (err && !doseIgnoreError) {
        console.log(stdout);
        console.error(stderr);
        return reject(err);
      }
      resolve(stdout);
    });
  });
}

gulp.task('check404', function () {
  return new Check404().start('http://localhost:' + process.env.PORT + '/');
});

gulp.task('copy-assets', function () {
  return gulp.src('src/bower_components/colorbox/example1/**').
    pipe(gulp.dest('assets/stylesheets'));
});

gulp.task('deploy', function () {
  var sshConfig = {
        host:     'ranyuen.sakura.ne.jp',
        port:     '22',
        username: 'ranyuen',
        password: process.env.SSH_PASSWORD,
      },
      commands = [
        'cd ~/www; git pull --ff-only origin master',
        'cd ~/www; php composer.phar install --no-dev',
        'cd ~/www; set SERVER_ENV=production; vendor/bin/phpmig migrate',
      ];

  return promiseSsh(sshConfig, commands);
});

gulp.task('jshint', function () {
  return gulp.src(['*.js', 'src/javascripts/**/**.js', 'lib/**/**.js']).
    pipe(jshint()).
    pipe(jshint.reporter(jshintStylish));
});

gulp.task('less', function () {
  return gulp.src([
      'src/stylesheets/layout.less',
      'src/stylesheets/photoGallery.less',
      'src/stylesheets/playMenu.less'
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

gulp.task('php-fixer', function () {
  return Promise.all([
    promiseProcess('vendor/bin/php-cs-fixer fix index.php', true),
    promiseProcess('vendor/bin/php-cs-fixer fix phpmig.php', true),
    promiseProcess('vendor/bin/php-cs-fixer fix bin/', true),
    promiseProcess('vendor/bin/php-cs-fixer fix config/', true),
    promiseProcess('vendor/bin/php-cs-fixer fix lib/', true),
    promiseProcess('vendor/bin/php-cs-fixer fix tests/', true),
    promiseProcess('vendor/bin/php-cs-fixer fix view/', true),
  ]);
});

gulp.task('php-lint', function () {
  return gulp.src([
      '*.php',
      'bin/**/**.php',
      'config/**/**.php',
      'lib/**/**.php',
      'tests/**/**.php',
      'view/**/**.php',
    ]).
    pipe(exec('php -l <%= file.path %>', {})).
    pipe(exec.reporter({stdout: false}));
});

gulp.task('php-metrics', function () {
  return promiseProcess('vendor/bin/phpcs --standard=phpcs.xml --extensions=php lib/').
    then(promiseProcess('vendor/bin/phpmd lib/ text phpmd.xml'));
});

gulp.task('php-test', function (done) {
  runSequence('php-lint', 'php-fixer', 'php-metrics', 'php-unit', done);
});

gulp.task('php-unit', function () {
  return promiseProcess('vendor/bin/phpunit').
    then(function (out) { console.log(out); });
});

gulp.task('uglifyjs', function () {
  var layout, photoGallery, changeTab,
      uglifyOption = {
        outSourceMap: true,
        output:       {},
        compress:     { unsafe: true },
      };

  layout = gulp.src([
      'src/bower_components/jquery/dist/jquery.min.js',
      'src/bower_components/uri.js/src/URI.min.js',
      'src/javascripts/polyfill.js',
      'src/javascripts/messageForDeveloperFromRanyuen.js',
      'src/javascripts/globalnav.js',
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
  changeTab = gulp.src([
      'src/javascripts/changeTab.js',
    ]).
    pipe(concat('changeTab.min.js')).
    pipe(uglify(uglifyOption)).
    pipe(gulp.dest('assets/javascripts'));
  return merge(layout, photoGallery, changeTab);
});

gulp.task('build', ['copy-assets', 'less', 'uglifyjs', 'nav']);
gulp.task('test', ['jshint', 'php-test']);
