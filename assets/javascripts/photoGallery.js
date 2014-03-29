(function (global) {
  /* jshint browser: true */
  /* global Promise, URI, $ */
  'use strict';

  // {{{ Model
  /**
   * @param {string} path
   * @param {Object.<string,Object>=} data
   * @param {String=} method
   * @return {Promise}
   */
  function getJson(path, data, method) {
    var request = new XMLHttpRequest(), form = [];

    /**
     * @param {function(string,string)} appendFun
     * @param {Object.<string,Object>} data
     */
    function appendData(appendFun, data) {
      var keys, key, value, i = 0, iz = 0;

      if (data) {
        keys = Object.keys(data);
        for (i = 0, iz = keys.length; i < iz; ++i) {
          key = keys[i];
          value = data[key];
          if (typeof value !== 'string' && !(value instanceof String)) {
            value = JSON.stringify(value);
          }
          appendFun(key, value);
        }
      }
    }

    if (!method) { method = 'GET'; }
    method = method.toUpperCase();
    if (method !== 'GET') {
      appendData(function (k, v) {
        form.push(encodeURIComponent(k) + '=' + encodeURIComponent(v));
      }, data);
      request.open(method, path, true);
      request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    } else {
      path = URI(path);
      appendData(function (k, v) { path.addSearch(k, v); }, data);
      request.open('GET', path.toString(), true);
    }
    request.send(form.join('&'));
    return new Promise(function (resolve, reject) {
      request.onreadystatechange = function () {
        var response;

        if (request.readyState === 4) {
          if (request.status !== 200) {
            return reject(new Error(request.responseText));
          }
          try {
            response = JSON.parse(request.responseText);
          } catch (err) {
            return reject(err);
          }
          resolve(response);
        }
      };
    });
  }

  /**
   *
   * @param {string} path
   * @param {?Object.<string,Object>} data
   * @param {Function} ModelClass
   * @param {?string} key
   * @param {string=} method GET|POST|PUT|DELETE
   * @returns {Promise}
   */
  function getResource(path, data, ModelClass, key, method) {
    return new Promise(function (resolve, reject) {
      getJson(path, data, method).then(function (response) {
        var tmp;

        if (!response) {
          response = null;
        } else if (Array.isArray(response)) {
          response = response.map(function (elm) {
            return new ModelClass().fromHash(elm);
          });
        } else {
          response = new ModelClass().fromHash(response);
        }
        if (key) {
          tmp = {};
          tmp[key] = response;
          response = tmp;
        }
        resolve(response);
      }, function (err) { reject(err); });
    });
  }

  /**
   * http://c4se.hatenablog.com/entry/2014/03/25/012252
   *
   * @param {function(Object...):Object} _super
   * @param {function(Object...):Object} _constructor
   * @return {function(Object...):Object}
   */
  function _extends(_super, _constructor) {
    var _class = null, toString;

    _class = function () {
      if (!(this instanceof _class)) {
        return new (Function.prototype.bind.apply(_class, [ null ]
              .concat(Array.from(arguments))))();
      }
      _super.apply(this, arguments);
      return _constructor.apply(this, arguments);
    };
    _class.prototype = Object.create(_super.prototype);
    _class.prototype.constructor = _class;
    toString = Function.prototype.toString;
    Function.prototype.toString = function () {
      if (this === _class) {
        return _constructor.toString();
      }
      return toString.call(this);
    };
    return _class;
  }

  /**
   * @constructor
   */
  function Model() { }

  /**
   */
  Model._extends = function (_constructor) {
    return _extends(Model, _constructor);
  };

  /**
   */
  Model.getJson = getJson;

  /**
   */
  Model.getResource = getResource;

  /**
   * @param {Object.
   *            <string,Object>} hash
   * @return {Model}
   */
  Model.prototype.fromHash = function (hash) {
    var keys, key, i = 0, iz = 0, isNull = true;

    keys = Object.keys(hash);
    for (i = 0, iz = keys.length; i < iz; ++i) {
      key = keys[i];
      if (this[key] !== void 0) {
        isNull = false;
        this[key] = hash[key];
      }
    }
    return isNull ? null : this;
  };

  /**
   */
  Model.prototype.getResource = function (path, data, key, method) {
    return getResource(path, data, this.constructor, key, method);
  };
  // }}}

  // {{{ App
  /**
   * @param (function(Object...):Object} fun
   * @param {Object} _this
   * @param {Object.<string,Object>} args
   * @return {function(Object...):Object}
   */
  function inject(fun, _this, args) {
    var params;

    params = fun.toString().
      replace(/\r?\n/g, ' ').
      match(/function[^\(]*\(([^\)]*)\)/)[1].
      split(',').
      map(function (param) { return param.trim(); });
    return function () {
      var i = 0, iz = 0, j = 0, _arguments = [];

      j = 0;
      for (i = 0, iz = params.length; i < iz; ++i) {
        if (args[params[i]] !== void 0) {
          _arguments[i] = args[params[i]];
        } else {
          _arguments[i] = arguments[j];
          ++j;
        }
      }
      fun.apply(_this, _arguments);
    };
  }

  /**
   * Singleton.
   *
   * @constructor
   */
  var App = function () {
    var me = this;

    if (!(this instanceof App)) { return new App(); }
    App = function () { return me; };
    this._routes = {};
    this.baseUrl = document.getElementsByTagName('base')[0].getAttribute('href');
  };

  /**
   * @returns {Promise}
   */
  App.prototype.init = function () {
    var me = this;

    $(document).on('click', 'a', function (evt) {
      var path = evt.target.getAttribute('href');

      if (URI(path).absoluteTo('/').host() !== location.hostname) { return; }
      evt.preventDefault();
      me.loadPage(path);
    });
    return this.loadPage();
  };

  /**
   * @param {string} path
   * @returns {Promise}
   */
  App.prototype.loadPage = function (path, title) {
    var me = this, controller;

    if (title === void 0) { title = ''; }
    if (path) {
      history.pushState(JSON.stringify({
        title : document.title
      }), title, path);
    }
    if (!path) { path = '/' + location.pathname.substring(this.baseUrl.length); }
    controller = this._matchRoute(path);
    return new Promise(function (resolve, reject) {
      try {
        controller.call();
      } catch (err) {
        return reject(err);
      }
      resolve(me);
    });
  };
  // window.onpopstate = function (evt) {
  //   var state;

  //   evt.preventDefault();
  //   state = JSON.parse(evt.state);
  //   new App().loadPage(null, state);
  // };


  /**
   * @param {string} path
   * @param {function(...)} controller
   * @returns {App}
   */
  App.prototype.route = function (path, controller) {
    this._routes[path] = controller;
    return this;
  };

  /**
   * @param {?String} path
   * @returns {function()}
   */
  App.prototype._matchRoute = function (path) {
    var i = 0, iz = 0, keys, key;

    function match(key, path) {
      var i = 0, iz = 0, params = {}, regex, matches, paramMatches;

      matches = key.match(/:\w+/g);
      if (!matches) {
        return false;
      }
      regex = new RegExp('^' + key.replace(/[\/]/, function (_1) {
        return '\\' + _1;
      }).replace(/:\w+/, '([^/]+)') + '$');
      paramMatches = path.match(regex);
      if (!paramMatches) {
        return false;
      }
      for (i = 0, iz = matches.length; i < iz; ++i) {
        params[matches[i].substring(1)] = paramMatches[i + 1];
      }
      return [ key, params ];
    }

    function fillGetParams(params) {
      params = location.search.substring(1).split('&').
        reduce(function (params, param) {
        if (!param) { return params; }
        param = param.split('=');
        params[param[0]] = param[1];
      }, params);
      params.params = Object.keys(params).reduce(function (accm, key) {
        accm[key] = params[key];
        return accm;
      }, {});
      return params;
    }

    if (this._routes[path]) {
      return inject(this._routes[path], this, fillGetParams({}));
    }
    keys = Object.keys(this._routes);
    for (i = 0, iz = keys.length; i < iz; ++i) {
      key = match(keys[i], path);
      if (key) {
        return inject(this._routes[key[0]], this, fillGetParams(key[1]));
      }
    }
    return this._routes['default'] || function () {
      throw new Error('Implements default controller.');
    };
  };
  // }}}

  var Photo = Model._extends(function  () {
    /* jshint camelcase: false */
    this.id = '';
    this.description_ja = '';
    this.description_en = '';
    this.color_r = 0;
    this.color_g = 0;
    this.color_b = 0;
    this.color_h = 0;
    this.color_s = 0;
    this.color_v = 0;
    this.species_name = '';
    this.product_name = '';
    this.width = 0;
    this.height = 0;
  });

App.prototype.photos = function (limit, offset) {
  return new Photo().getResource('/api/photos', {limit: limit, offset: offset});
};

  new App().route('/photos/', function (params) {
    document.getElementById('photo-gallery').innerHTML = '<pg-photos/>';
  });

  new App().route('/photos/:id', function (id, params) {
  });

  global.App = App;
  global.Model = Model;
  global.Photo = Photo;
  new App().init();

}((this || 0).self || global));
// vim:set fdm=marker:
