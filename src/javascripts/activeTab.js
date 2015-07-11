$(function () {
  'use strict';
  $('nav ul li a').each(function () {
    var href = $(this).attr('href');
    if (location.href.match(href)) {
      if (href !== '/') {
        $(this).addClass('activate');
      }
    } else {
      $(this).removeClass('activate');
    }
  });
});
