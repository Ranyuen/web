/* global -Promise */
/* jshint node:true */
'use strict';
var http = require('http');
var Promise = require('bluebird'),
    jsdom = require('jsdom'),
    URI = require('URIjs');

if (!Array.from) {
  Array.from = function (obj) { return [].slice.call(obj); };
}

function Check404() {
  this.host = '';
  this.results = {};
}

Check404.prototype.start = function (url) {
  url = URI(url);
  this.host = url.protocol() + '://' + url.host();
  this.results = {};
  return this._check(url.pathname(), '//');
};

Check404.prototype._check = function (url, at) {
  var me = this;

  if (me.results[url] !== void 0) { return; }
  this.results[url] = false;
  return new Promise(function (resolve, reject) {
    http.get(me.host + url, function (res) {
      var body = '';

      if (res.statusCode !== 200) {
        console.error('NG ' + url + ' at ' + at);
        return reject(res);
      }
      res.setEncoding('utf8');
      res.on('data', function (chunk) { body += chunk; }).
        on('error', function (err) { reject(err); }).
        on('end', function () {
          if (!/^text\/html/.test(res.headers['content-type'])) { return resolve(); }
          jsdom.env(body, [], function (errors, window) {
            var hrefs;

            if (errors) {
              console.error('NG ' + url + "\tat\t" + at);
              return reject(errors);
            }
            me.results[url] = true;
            hrefs = Array.from(window.document.getElementsByTagName('a')).
              map(function (a) { return [a.href, a.getAttribute('href')]; }).
              filter(function (href) { return href[1] && !/^\w+?:/.test(href[1]); }).
              map(function (href) { return href[0]; }).
              map(function (href) { return URI(href).absoluteTo(url).pathname(); });
            Promise.all(hrefs.map(function (href) { return me._check(href, url); })).
              then(function () { resolve() }).
              catch(function (err) { reject(err); });
          });
        });
    }).on('error', function (err) {
      console.error('NG ' + url + ' at ' + at);
      reject(err);
    });
  }).then(function () { console.log('OK ' + url); });
};

module.exports = Check404;
