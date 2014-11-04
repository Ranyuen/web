/* global global */
(function (global) {
/* jshint browser:true, sub:true */
'use strict';

if (!Date.now) {
  Date.now = function () { new Date().getTime(); };
}

if (!Array.from) {
  Array.from = function (obj) { return [].slice.call(obj); };
}

if (!global.requestAnimationFrame) {
  global.requestanimationframe =
    global.webkitRequestAnimationFrame ||
    global.mozRequestAnimationFrame ||
    global.msRequestAnimationFrame ||
    global.oRequestAnimationFrame ||
    global.khtmlRequestAnimationFrame;
}

/**
* @param {function(...:any):any} fun
* @param {number=} wait
* @return {function(...:any):any}
*/
function throttle(fun, wait) {
  var isThrottled = false;

  if (!wait || wait <= 0) { wait = 1000 / 30; }
  return function (/* arguments */) {
    if (!isThrottled) {
      setTimeout(function () {
        isThrottled = false;
        fun.apply(null, arguments);
      }, wait);
      isThrottled = true;
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
