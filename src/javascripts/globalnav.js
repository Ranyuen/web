/* global window, document */
(function (global) {
'use strict';

function globalnav() {
  var i = 0, iz = 0,
      topItems = document.querySelectorAll('#globalnav > ul > li');

  for (i = 0, iz = topItems.length; i < iz; ++i) {
    topItem(topItems[i]);
  }
}

function topItem(item) {
  item.classList.add('globalnav-item');
  if (item.getElementsByTagName('ul').length > 0) {
    item.classList.add('globalnav-item_hasSubmenu');
    item.classList.add('globalnav-item_isClose');
    item.dataset.status = 'close';
    item.addEventListener('click', onClickItemHasSubmenu);
  }
}

function onClickItemHasSubmenu(evt) {
  var href= evt.target.getAttribute('href'),
      item = evt.currentTarget;

  if (href && '#' !== href) { return; }
  evt.preventDefault();
  evt.stopPropagation();
  switch (item.dataset.status) {
    case 'close':
      item.classList.remove('globalnav-item_isClose');
      item.classList.add('globalnav-item_isOpen');
      item.dataset.status = 'open';
      break;
    case 'open':
      item.classList.add('globalnav-item_isClose');
      item.classList.remove('globalnav-item_isOpen');
      item.dataset.status = 'close';
      break;
  }
}

global.globalnav = globalnav;
}(this));

window.addEventListener('DOMContentLoaded', window.globalnav);
