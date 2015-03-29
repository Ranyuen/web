/* global URI, Masonry, Hogan, global */
(function (global) {
/* jshint browser:true, jquery:true, unused:false, sub:true */
'use strict';

var debounce = global.debounce;

function PhotoGallery() {
  this.rootNode = null;
  this.currentPage = 1;
  this._masonry = null;
  this._lockLoadingNextPage = false;
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
  window.onscroll = debounce(this.onscroll.bind(this));
  this.applied();
};

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

PhotoGallery.prototype.onscroll = function () {
  if (document.documentElement.clientHeight >
      this._lastPhotoNode.getBoundingClientRect().top) {
    this.loadNextpage();
  }
};

PhotoGallery.prototype.loadNextpage = function () {
  var req, uri,
      _this = this;

  if (this._lockLoadingNextPage) { return; }
  this._lockLoadingNextPage = true;
  uri = URI('/api/photos').
    addSearch(URI(location.href).search(true)).
    addSearch({limit: 20, offset: this.currentPage * 20}).
    toString();
  req = new XMLHttpRequest();
  req.open('GET', uri);
  req.send();
  req.onreadystatechange = function () {
    var res;

    if (req.readyState !== 4) { return; }
    _this._lockLoadingNextPage = false;
    if (req.status !== 200) { return console.error([req.status, req]); }
    try {
      res = JSON.parse(req.responseText);
    } catch (err) {
      return console.error([err, req.responseText]);
    }
    ++ _this.currentPage;
    _this.insertPhotoNodes(res.map(function (photo) {
      var thumbWidth = 349,
          thumbHeight = ~~(photo.height * thumbWidth / photo.width);

      photo['thumb_width'] = thumbWidth;
      photo['thumb_height'] = thumbHeight;
      return photo;
    }));
  };
};

PhotoGallery.prototype.insertPhotoNodes = function (photos) {
  var photoNodes = [],
      fragment = document.createDocumentFragment();

  photos.forEach(function (photo) {
    // jscs:disable maximumLineLength
    /* jshint maxlen:1000 */
    var photoNode = document.createElement('div');

    photoNode.classList.add('photo');
    photo.alt = photo['description_ja'] + ' 蘭裕園 Ranyuen';
    photo.origUrl = '/images/gallery/' + photo.id + '.jpg';
    photo.thumbUrl = '/api/photo?format=jpeg&id=' + photo.id + '&width=' + photo['thumb_width'];
    photoNode.innerHTML = Hogan.
      compile('<a class="lightbox" href="{{origUrl}}" title="{{alt}}"><img rel="gallery" src="{{thumbUrl}}" width="{{thumb_width}}" height="{{thumb_height}}" alt="{{alt}}"/></a><div>{{description_ja}}</div><div>{{description_en}}</div>').
      render(photo);
    fragment.appendChild(photoNode);
    photoNodes.push(photoNode);
  });
  this.rootNode.appendChild(fragment);
  this._masonry.appended(photoNodes);
  this.applied();
  // jscs:enable
};

global['PhotoGallery'] = PhotoGallery;
})((this || 0).self || global);
