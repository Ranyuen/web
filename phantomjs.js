(function () {
  /* global phantom, document, require */
  'use strict';
  var tests = {};

  tests['The site is moving.'] = function (done) {
    var page = require('webpage').create();

    page.open('http://127.0.0.1/', function (status) {
      if (status !== 'success') { return done(new Error(status)); }
      done();
    });
  };


  /**
   * @constructor
   */
  function TracePage() {
    this.cache = {};
  }

  /**
   * @param {string} path Absolute URL.
   * @param {function(?Error)} done
   */
  TracePage.prototype.trace = function (path, done) {
    var me = this, urls = [],
        page, start, hostname, wait;

    if (this.cache[path] || /{{.+}}/.test(path)) { return done(); }
    this.cache[path] = {};
    hostname = path.match(/https?:\/\/([^\/]+)\//)[1];
    page = require('webpage').create();
    start = Date.now();
    page.onError = function (message) { console.error(message); };
    page.onResourceError = function (resourceError) {
      done(new Error('Load ' + resourceError.url + ' has failed: ' + resourceError.errorString));
    };
    page.onCallback = function (data) {
      if (data.error) {
        done(new Error('Load ' + data.url + ' has failed: ' + data.status));
      }
    };
    page.onConsoleMessage = function (url) {
      var i = 0, iz = 0;

      function callback(err) {
        --wait;
        if (err) { return done(err); }
        if (wait <= 0) { done(); }
      }

      if (url === 'end') {
        page.close();
        console.log('OK ' + path);
        urls = urls.filter(function (url) {
          return url &&
            (url.match(/https?:\/\/([^\/]+)\//) || [])[1] === hostname &&
            !/\.(?!html?)\w+$/.test(url);
        });
        wait = urls.length;
        for (i = 0, iz = urls.length; i < iz; ++i) {
          me.trace(urls[i], callback);
        }
        return;
      }
      if (/https?:\/\/[^\/]+\//.test(url)) { urls.push(url); }
    };
    page.open(path, function (status) {
      me.cache[path].time = Date.now() - start;
      if (status !== 'success') {
        return done(new Error('Load ' + path + ' has failed: ' + status));
      }
      page.evaluate(function () {
        var i = 0, iz = 0, nodes, url, parser;

        parser = document.createElement('a');
        nodes = document.querySelectorAll('a');
        for (i = 0, iz = nodes.length; i < iz; ++i) {
          url = nodes[i].href;
          if (url) {
            parser.href = url;
            console.log(parser.href);
          }
        }
        console.log('end');
      });
    });
  };

  /**
   * @override
   * @return {string}
   */
  TracePage.prototype.toString = function () {
    return Object.keys(this.cache).map(function (url) {
      return url + '\n' +
        '  Time: ' + (this.cache[url].time / 1000) + 's';
    }, this).join('\n');
  };

  tests['The site has no 404.'] = function (done) {
    var tracer = new TracePage();

    tracer.trace('http://127.0.0.1/', function (err) {
      console.log(tracer);
      done(err);
    });
  };


  Object.keys(tests).reverse().reduce(function (deffer, testName) {
    var test;

    test = tests[testName];
    return function (result) {
      if (result instanceof Error) {
        console.error(result);
        phantom.exit();
      }
      if (result) { console.log(result); }
      console.log('Test: ' + testName);
      test(deffer);
    };
  }, function (result) {
    if (result instanceof Error) {
      console.error(result);
      phantom.exit();
    }
    if (result) { console.log(result); }
    phantom.exit();
  })();

}());
