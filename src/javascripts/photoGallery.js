/* global URI, Masonry, Hogan, global */
(function (global) {
/* jshint browser:true, jquery:true, unused:false, sub:true */
'use strict';

function PhotoGallery() {
  this.rootNode = null;
  this.currentPage = 1;
  this._masonry = null;
  this._lastPhotoNode = null;
}

PhotoGallery.prototype.init = function (rootNode) {
  this.rootNode = rootNode;
  this._masonry = new Masonry(rootNode, {
    gutter:             8,
    isFitWidth:         false,
    itemSelector:       '.photo',
    hiddenStyle:        { opacity: 0 },
    visibleStyle:       { opacity: 1 },
    transitionDuration: '0.8s',
  });
  this.applied();
};

//* select_list
PhotoGallery.prototype.applied = function () {
  $('.lightbox').colorbox({
    rel:        'gallery',
    fixed:      true,
    height:     '90%',
    transition: 'elastic',
    speed:      360,
    width:      '90%',
  });
  this._lastPhotoNode = this.rootNode.querySelector('.photo:last-of-type');
};

global['PhotoGallery'] = PhotoGallery;
})((this || 0).self || global);
