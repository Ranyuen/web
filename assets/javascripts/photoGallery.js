(function (global) {
'use struct';

// {{{ Util
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
  var pre_processed_at = Date.now();

  if (!wait || wait <= 0) { wait = 1000 / 30; }
  return function (/* arguments */) {
    var current = Date.now();

    if (pre_processed_at + wait > current) { return; }
    pre_processed_at = current;
    fun.apply(null, arguments);
  };
}
// }}}

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
    columnWidth:  '.photo',
    gutter:       0,
    itemSelector: '.photo'
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
    width:      '90%'
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
  uri = URI('/api/photos/').
    addSearch(URI(location.href).search()).
    addSearch({limit: 20, offset: (this.currentPage - 1) * 20});
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
    ++ this.currentPage;
    _this.insertPhotoNodes(res.map(function (photo) {
      var thumb_width = 349,
          thumb_height = ~~(photo.height * thumb_width / photo.width);

      photo.thumb_width = thumb_width
      photo.thumb_height = thumb_height
      return photo;
    }));
  };
};

PhotoGallery.prototype.insertPhotoNodes = function (photos) {
  var photoNodes = [],
      fragment = document.createDocumentFragment();

  photos.forEach(function (photo) {
    var photoNode = document.createElement('div');

    photoNode.classList.add('photo');
    photo.alt = photo.description_ja + ' 蘭裕園 Ranyuen';
    photo.origUrl = '/Calanthe/gallery/' + photo.id + '.jpg';
    photo.thumbUrl = '/api/photo?format=jpeg&id=' + photo.id + '&width=' + photo.thumb_width;
    photoNode.innerHTML = Hogan.
      compile('<a class="lightbox" href="{{origUrl}}" title="{{alt}}"><img rel="gallery" src="{{thumbUrl}}" width="{{thumb_width}}" height="{{thumb_height}}" alt="{{alt}}"/></a><div>{{description_ja}}</div><div>{{description_en}}</div>').
      render(photo);
    fragment.appendChild(photoNode);
    photoNodes.push(photoNode);
  });
  this.rootNode.appendChild(fragment);
  this._masonry.appended(photoNodes);
  this.applied();
};

global.PhotoGallery = PhotoGallery;
})((this || 0).self || global);
