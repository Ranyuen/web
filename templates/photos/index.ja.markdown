---
title: 写真を見る
---
蘭裕園の写真を見る
==
<?php
$controller = new \Ranyuen\Controller\ApiPhotos;
$species_name = isset($_GET['species_name']) ? $_GET['species_name'] : null;
$photos = $controller->render('GET', [], [
  'species_name' => $species_name,
  'limit' => 20
]);
$photos = array_map(function ($photo) {
  $thumb_width = 349;
  $thumb_height = floor($photo['height'] * $thumb_width / $photo['width']);
  $photo['thumb_width'] = $thumb_width;
  $photo['thumb_height'] = $thumb_height;
  return $photo;
}, $photos);
?>
<style>
  .photos .photo {
    background: #f0f5f0;
    float: left;
    margin: 0.6%;
    width: 32%;
  }
</style>
<form method="GET">
  <select name="species_name">
    <option value="all" <?php if ($species_name === 'all') { echo 'selected'; } ?>>全て見る</option>
    <option value="Calanthe" <?php if ($species_name === 'Calanthe') { echo 'selected'; } ?>>エビネ</option>
    <option value="Ponerorchis" <?php if ($species_name === 'Ponerorchis') { echo 'selected'; } ?>>アワチドリ/夢ちどり</option>
  </select>
  <input type="submit" value="検索" />
</form>
<div id="photo-gallery" class="photos">
<?php foreach ($photos as $photo) { ?>
  <div class="photo">
    <a href="/Calanthe/gallery/<?php $h->h($photo['id']); ?>.jpg"
      class="lightbox"
      title="<?php $h->h($photo['description_ja']); ?> 蘭裕園 Ranyuen">
      <img rel="gallery"
        src="/api/photo?format=jpeg&id=<?php $h->h($photo['id']); ?>&width=<?php $h->h($photo['thumb_width']); ?>"
        width="<?php $h->h($photo['thumb_width']); ?>"
        height="<?php $h->h($photo['thumb_height']); ?>"
        alt="<?php $h->h($photo['description_ja']); ?> 蘭裕園 Ranyuen"/>
    </a>
    <div>
      <div><?php $h->h($photo['description_ja']); ?></div>
      <div><?php $h->h($photo['description_en']); ?></div>
    </div>
  </div>
<?php } ?>
</div>
<script src="/assets/bower_components/colorbox/jquery.colorbox-min.js"></script>
<link href="/assets/bower_components/colorbox/example1/colorbox.css" rel="stylesheet" />
<script src="/assets/bower_components/colorbox/i18n/jquery.colorbox-ja.js"></script>
<script src="/assets/bower_components/masonry/dist/masonry.pkgd.min.js"></script>
<script src="/assets/bower_components/uri.js/src/URI.min.js"></script>
<script src="/assets/bower_components/hogan/web/builds/3.0.2/hogan-3.0.2.min.js"></script>
<script src="/assets/javascripts/photoGallery.js"></script>
<script>
  window.addEventListener('DOMContentLoaded', function () {
    new PhotoGallery().init(document.getElementById("photo-gallery"));
  });
</script>
