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
<form id="search-form" method="GET">
  <select id="search-form-species_name" name="species_name">
    <option value="" <?php if ($species_name === null) { echo 'selected'; } ?>>--種類を選ぶ--</option>
    <option value="all" <?php if ($species_name === 'all') { echo 'selected'; } ?>>全て見る</option>
    <option value="Calanthe" <?php if ($species_name === 'Calanthe') { echo 'selected'; } ?>>エビネ</option>
    <option value="Ponerorchis" <?php if ($species_name === 'Ponerorchis') { echo 'selected'; } ?>>アワチドリ/夢ちどり</option>
    <option value="Japanease native orchid" <?php if ($species_name === 'Japanease native orchid') { echo 'selected'; } ?>>日本の野生ラン</option>
    <option value="others" <?php if ($species_name === 'others') { echo 'selected'; } ?>>その他</option>
  </select>
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
<link href="/assets/stylesheets/photoGallery.css" rel="stylesheet"/>
<link href="/assets/stylesheets/colorbox.css" rel="stylesheet"/>
<script src="/assets/javascripts/photoGallery.min.js"></script>
<script>
  window.addEventListener('DOMContentLoaded', function () {
    new PhotoGallery().init(document.getElementById("photo-gallery"));
    document.getElementById('search-form-species_name').onchange = function () {
      document.getElementById('search-form').submit();
    };
  });
</script>
