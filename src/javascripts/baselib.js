/* global global */
(function (global) {
/* jshint browser:true, sub:true, maxstatements:1000 */
'use strict';

if (!Array.from) {
  Array.from = function (obj) {
    return [].slice.call(obj);
  };
}

if (!Date.now) {
  Date.now = function () {
    new Date().getTime();
  };
}

if (!String.prototype.startsWith) {
  Object.defineProperty(String.prototype, 'startsWith', {
    enumerable   : false,
    configurable : false,
    writable     : false,
    value        : function (searchString, position) {
      position = position || 0;
      return this.lastIndexOf(searchString, position) === position;
    }
  });
}

if (!global.requestAnimationFrame) {
  global.requestanimationframe =
    global.webkitRequestAnimationFrame ||
    global.mozRequestAnimationFrame ||
    global.msRequestAnimationFrame ||
    global.oRequestAnimationFrame ||
    global.khtmlRequestAnimationFrame;
}

function setImmediate(fun) {
  if (global.setImmediate) {
    return global.setImmediate(fun);
  }
  if (global.requestAnimationFrame) {
    return global.requestAnimationFrame(fun);
  }
  return setTimeout(fun, 0);
}

/**
 * @param {function(...:any<T>):any} fun
 * @param {number?}                  wait ms. Default uses setImmediate().
 *
 * @return {function(...:any<T>):void}
 */
function throttle(fun, wait) {
  var doseUseTick = false,
      isThrottled = false;

  if (!wait) {
    doseUseTick = true;
  }
  if (!wait || wait <= 0) {
    wait = 1000 / 30;
  }
  return function (/* arguments */) {
    if (isThrottled) {
      return;
    }
    isThrottled = true;
    fun.apply(this, arguments);
    if (doseUseTick) {
      setImmediate(function () {
        isThrottled = false;
      });
    } else {
      setTimeout(function () {
        isThrottled = false;
      }, wait);
    }
  };
}

/**
* @param {function(...:any):any} fun
* @param {number=} wait
* @return {function(...:any):any}
 */
function debounce(fun, wait) {
  var preProcessedAt = Date.now();

  if (!wait || wait <= 0) { wait = 1000 / 30; }
  return function (/* arguments */) {
    var current = Date.now();

    if (preProcessedAt + wait > current) { return; }
    preProcessedAt = current;
    fun.apply(null, arguments);
  };
}

global['throttle'] = throttle;
global['debounce'] = debounce;
}((this || 0).self || global));
