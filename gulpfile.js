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
var sshConfig = {
      host:     'ranyuen.sakura.ne.jp',
      port:     '22',
      username: 'ranyuen',
      password: process.env.SSH_PASSWORD,
    };

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
  var connection = ssh({
        sshConfig:    sshConfig,
        ignoreErrors: true,
      });

  connection.ignoreErrors = true;
  return connection.exec([
      'cd ~/www ; git pull origin master >&2 /dev/null',
      'cd ~/www ; php composer.phar install --no-dev',
      // 'cd ~/www ; set SERVER_ENV=production ; vendor/bin/phpmig migrate',
    ], {
      filePath: 'deploy.log',
    }).
    pipe(gulp.dest('logs'));
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

gulp.task('php-fixer', function () {
  return Promise.all([
    'vendor/bin/php-cs-fixer fix index.php',
    'vendor/bin/php-cs-fixer fix phpmig.php',
    'vendor/bin/php-cs-fixer fix config/',
    'vendor/bin/php-cs-fixer fix lib/',
    'vendor/bin/php-cs-fixer fix tests/',
    'vendor/bin/php-cs-fixer fix view/',
  ]);
});

gulp.task('php-lint', function () {
  return gulp.src([
      '*.php',
      'config/**/**.php',
      'lib/**/**.php',
      'tests/**/**.php',
      'view/**/**.php',
    ]).
    pipe(exec('php -l <%= file.path %>', {})).
    pipe(exec.reporter({stdout: false}));
});

gulp.task('php-metrics', function () {
  return Promise.all([
      promiseProcess('vendor/bin/phpcs --standard=phpcs.xml --extensions=php -l .'),
      promiseProcess('vendor/bin/phpcs --standard=phpcs.xml --extensions=php lib/'),
      promiseProcess('vendor/bin/phpcs --standard=phpcs.xml --extensions=php view/'),
    ]).then(Promise.all([
      promiseProcess('vendor/bin/phpmd lib/ text phpmd.xml'),
      promiseProcess('vendor/bin/phpmd view/ text phpmd.xml'),
    ]));
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
  changeTab = gulp.src([
    'src/javascripts/changeTab.js'
    ]).
    pipe(concat('changeTab.min.js')).
    pipe(uglify(uglifyOption)).
    pipe(gulp.dest('assets/javascripts'));
  return merge(layout, photoGallery, changeTab);
});

gulp.task('build', ['copy-assets', 'less', 'uglifyjs', 'nav']);
gulp.task('test', ['jshint', 'php-test']);
