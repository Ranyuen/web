/* global window, document */
(function (global) {
'use strict';

/**
 * @param {HTMLNavElement} nav
 */
function foldNav(nav) {
  var i        = 0,
      iz       = 0,
      topItems = nav.firstElementChild.children;

  nav.classList.add('foldnav');
  for (i = 0, iz = topItems.length; i < iz; ++i) {
    setupItem(topItems[i]);
  }
}

/**
 * @param {HTMLLiElement} item
 */
function setupItem(item) {
  item.classList.add('foldnav_item');
  if (item.querySelector('ul')) {
    item.classList.add('foldnav_item-hasSubmenu');
    item.classList.add('foldnav_item-isClose');
    item.addEventListener('click', onClickItemHasSubmenu, true);
    Array.from(item.querySelector('ul').children).filter(function (node) {
      return 'li' === node.tagName.toLowerCase();
    }).forEach(function (li) {
      setupItem(li);
    });
  }
}

/**
 * @param {MouseEvent} evt
 */
function onClickItemHasSubmenu(evt) {
  var href = evt.target.getAttribute('href'),
      item = evt.currentTarget;

  if (href && '#' !== href) {
    return true;
  }
  evt.preventDefault();
  evt.stopPropagation();
  if (item.classList.contains('foldnav_item-isClose')) {
      // Open submenu.
      item.classList.remove('foldnav_item-isClose');
      item.classList.add('foldnav_item-isOpen');
  } else {
      // Close submenu.
      item.classList.add('foldnav_item-isClose');
      item.classList.remove('foldnav_item-isOpen');
  }
}

global.foldNav = foldNav;
}((this || 0).self || global));

window.addEventListener('DOMContentLoaded', function () {
  foldNav(document.getElementById('globalnav'));
  foldNav(document.getElementById('sidenav'));
});
