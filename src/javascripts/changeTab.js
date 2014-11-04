/* global window, document */
(function () {
'use strict';

var tab;

window.onload = function() {
  tab.setup = {
    tabs: document.getElementById('tab').getElementsByTagName('li'),
    pages: [
      document.getElementById('spring'),
      document.getElementById('summer'),
      document.getElementById('autumn'),
      document.getElementById('winter'),
    ]
  };
  tab.init();
};

tab = {
  init: function () {
    var i, iz,
        tabs = this.setup.tabs,
        pages = this.setup.pages;

    function onclick() {
      tab.showpage(this);
      return false;
    }

    for (i = 0, iz = pages.length; i < iz; i++) {
      if (i !== 0) {
        pages[i].style.display = 'none';
      }
      tabs[i].onclick = onclick;
    }
  },

  showpage: function(obj) {
    var i, iz, num,
        tabs = this.setup.tabs,
        pages = this.setup.pages;

    for (i = 0, iz = tabs.length; i < iz; ++i) {
      num = i;
      if (tabs[i] === obj) {
        break;
      }
    }
    for (i = 0, iz = pages.length; i < iz; ++i) {
      if (i === num) {
        pages[num].style.display = 'block';
        tabs[num].className = 'selected';
      } else {
        pages[i].style.display = 'none';
        tabs[i].className = null;
      }
    }
  }
};

}());
