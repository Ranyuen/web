/* global -Promise */
/* jshint node:true */
'use strict';
var Promise = require('bluebird'),
    Ssh     = require('ssh2');

function promisefy(value) {
  return new Promise(function (resolve) { resolve(value); });
}

function PromiseSsh(config) {
  this.connection = new Ssh();
  this.config = config;
}

PromiseSsh.prototype.ready = function () {
  var me = this;

  return new Promise(function (resolve) {
    me.connection.on('ready', resolve).connect(me.config);
  });
};

PromiseSsh.prototype.end = function () {
  this.connection.end();
};

PromiseSsh.prototype.execOne = function (command) {
  var me = this,
      out = {stdout: '', stderr: ''};

  return new Promise(function (resolve, reject) {
    console.log('START:: ' + command);
    me.connection.exec(command, function (err, stream) {
      if (err) {
        console.error(err);
        return reject(err);
      }
      stream.on('exit', function (code, signal) {
        console.log('EXIT:: code: ' + code + ' signal: ' + signal);
      }).on('close', function () {
        console.log('CLOSE::');
        resolve(out);
      }).on('data', function (data) {
        data = data.toString();
        out.stdout += data;
        console.log(data);
      }).stderr.on('data', function (data) {
        data = data.toString();
        out.stderr += data;
        console.error(data);
      });
    });
  });
};

PromiseSsh.prototype.exec = function (commands) {
  var me = this,
      outs = [];

  return commands.
    reduce(function (promise, command) {
      return promise.then(function () { return me.execOne(command); }).
        then(function (out) {
          outs.push(out);
          return promisefy(void 0);
        });
    }, promisefy(void 0)).
    then(function () { return promisefy(outs); });
};

function promiseSsh(config, commands) {
  var ssh = new PromiseSsh(config);

  return ssh.ready().
    then(function () { return ssh.exec(commands); }).
    then(function (outs) {
      ssh.end();
      return promisefy(outs);
    }).
    catch(function (err) {
      console.error(err);
      ssh.end();
    });
}

module.exports = promiseSsh;
