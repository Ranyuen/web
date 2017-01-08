$(function () {
  'use strict';
  $('nav ul li a').each(function () {
    var href = $(this).attr('href');
    if (location.href.match(href)) {
      if (href !== '/' && href !== '/en/') {
        $(this).addClass('activate');
      }
    } else {
      $(this).removeClass('activate');
    }
  });
  $('#sidenav ul li a').each(function () {
    // 現在のページのドメイン名を取得
    var dname = location.href.match(/^https?:\/\/[^\/]+/);
    // ローカルナビのURLを取得しドメイン名と結合
    var dhref = dname + $(this).attr('href');
    // 現在のページのURLを取得
    var lhref = location.href;
    // 比較し一致する場合にactivateクラスを付加
    if (dhref === lhref) {
      $(this).addClass('activate');
    }
  });
});
