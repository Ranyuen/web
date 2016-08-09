/* global -Promise */
/* jshint node:true */
'use strict';
var cp = require('child_process'),
    fs = require('fs');
var Promise       = require('bluebird'),
    gulp          = require('gulp'),
    concat        = require('gulp-concat'),
    jscs          = require('gulp-jscs'),
    jshint        = require('gulp-jshint'),
    less          = require('gulp-less'),
    uglify        = require('gulp-uglifyjs'),
    jshintStylish = require('jshint-stylish'),
    merge         = require('merge-stream');
var promiseSsh = require('./lib/promiseSsh');
var sshConfig = {
      host     : 'ranyuen.sakura.ne.jp',
      port     : '22',
      username : 'ranyuen',
      password : process.env.SSH_PASSWORD,
    };

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

gulp.task('backup-db', function () {
  return promiseSsh(
    sshConfig,
    ['mysqldump -Q -h mysql495.db.sakura.ne.jp -uranyuen -p' +
      process.env.DB_PASSWORD +
      ' --default-character-set=UTF8 --set-charset ranyuen_production']
  ).then(function (outs) {
    var sql = new Buffer(outs[0].stdout, 'utf8'),
        fileName = 'logs/ranyuen_production-' + new Date().toISOString() + '.sql';

    return new Promise(function (resolve, reject) {
      fs.open(fileName, 'wx', function (err, file) {
        if (err) { return reject(err); }
        fs.write(file, sql, 0, sql.length, 0, function (err) {
          if (err) { return reject(err); }
          resolve();
        });
      });
    });
  });
});

gulp.task('backup-images', function () {
  return promiseProcess('rsync -av ranyuen@ranyuen.sakura.ne.jp:~/www/images .');
});

gulp.task('copy-assets', function () {
  return gulp.src('src/bower_components/colorbox/example1/**').
    pipe(gulp.dest('assets/stylesheets'));
});

gulp.task('deploy', function () {
  var commands = [
        'cd ~/www; git pull --ff-only origin master',
        'cd ~/www; php composer.phar install --no-dev',
        'cd ~/www; set SERVER_ENV=production; vendor/bin/phpmig migrate',
      ];

  return promiseSsh(sshConfig, commands);
});

gulp.task('jscs', function () {
  return gulp.src(['*.js', 'src/javascripts/**/**.js', 'lib/**/**.js']).
    pipe(jscs());
});

gulp.task('jshint', function () {
  return gulp.src(['*.js', 'src/javascripts/**/**.js', 'lib/**/**.js']).
    pipe(jshint()).
    pipe(jshint.reporter(jshintStylish));
});

gulp.task('js-test', ['jscs', 'jshint']);

gulp.task('less', function () {
  return gulp.src([
      'src/stylesheets/article_editor.less',
      'src/stylesheets/authors.less',
      'src/stylesheets/calanthe.less',
      'src/stylesheets/layout.less',
      'src/stylesheets/news.less',
      'src/stylesheets/news_column.less',
      'src/stylesheets/photoGallery.less',
      'src/stylesheets/playColoring.less',
      'src/stylesheets/playMenu.less',
      'src/stylesheets/ponerorchis.less',
      'src/stylesheets/columns.less',
      'src/stylesheets/sitemap.less',
      'src/stylesheets/orchidExam.less',
    ]).
    pipe(less({
      compress:  true,
      sourceMap: true,
    })).
    pipe(gulp.dest('assets/stylesheets'));
});

gulp.task('php-test', function () {
  return promiseProcess('vendor/bin/phing test');
});

gulp.task('uglifyjs', function () {
  return merge([
    {
      src: [
        'src/bower_components/jquery/dist/jquery.min.js',
        'src/bower_components/uri.js/src/URI.min.js',
        'src/javascripts/baselib.js',
        'src/javascripts/messageForDeveloperFromRanyuen.js',
        'src/javascripts/foldNav.js',
        'src/javascripts/activeTab.js',
        'src/javascripts/playColoring.js',
      ],
      dest: 'layout.min.js'
    },
    {
      src: [
        'src/bower_components/colorbox/jquery.colorbox-min.js',
        'src/bower_components/colorbox/i18n/jquery.colorbox-ja.js',
        'src/bower_components/hogan/web/builds/3.0.2/hogan-3.0.2.min.js',
        'src/bower_components/masonry/dist/masonry.pkgd.min.js',
        'src/javascripts/photoGallery.js',
      ],
      dest: 'photoGallery.min.js'
    },
    {
      src: [
        'src/javascripts/changeTab.js',
      ],
      dest: 'changeTab.min.js'
    },
    {
      src: [
        'src/javascripts/baselib.js',
        'src/javascripts/article_editor.js',
      ],
      dest: 'article_editor.min.js'
    },
    {
      src: [
        'src/javascripts/orchidExam.js'
      ],
      dest :'orchidExam.js'
    }
  ].map(function (set) {
    return gulp.src(set.src).
      pipe(concat(set.dest)).
      pipe(uglify({
        outSourceMap : true,
        output       : {},
        compress     : { unsafe : true },
      })).
      pipe(gulp.dest('assets/javascripts'));
  }));
});

gulp.task('backup', ['backup-db', 'backup-images']);
gulp.task('build', ['copy-assets', 'less', 'uglifyjs']);
gulp.task('test', ['js-test', 'php-test']);
