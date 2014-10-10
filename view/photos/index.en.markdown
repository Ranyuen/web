---
title: Photos
---
Photos in Ranyuen
==
<?php
$controller = new \Ranyuen\Controller\ApiPhotos;
$species_name = isset($_GET['species_name']) ? $_GET['species_name'] : null;
$photos = $controller->get([
  'species_name' => $species_name,
  'limit'        => 20,
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
    <option value="" <?php if ($species_name === null) { echo 'selected'; } ?>>-change species-</option>
    <option value="all" <?php if ($species_name === 'all') { echo 'selected'; } ?>>All</option>
    <option value="Calanthe" <?php if ($species_name === 'Calanthe') { echo 'selected'; } ?>>Calanthe</option>
    <option value="Ponerorchis" <?php if ($species_name === 'Ponerorchis') { echo 'selected'; } ?>>Ponerochis</option>
    <option value="Japanease native orchid" <?php if ($species_name === 'Japanease native orchid') { echo 'selected'; } ?>>Japanease native orchid</option>
    <option value="others" <?php if ($species_name === 'others') { echo 'selected'; } ?>>Others</option>
  </select>
</form>
<div id="photo-gallery" class="photos">
<?php foreach ($photos as $photo) { ?>
  <div class="photo">
    <a href="/images/gallery/<?php $h->h($photo['id']); ?>.jpg"
      class="lightbox"
      title="<?php $h->h($photo['description_ja']); ?> Ranyuen">
      <img rel="gallery"
        src="/api/photo?format=jpeg&id=<?php $h->h($photo['id']); ?>&width=<?php $h->h($photo['thumb_width']); ?>"
        width="<?php $h->h($photo['thumb_width']); ?>"
        height="<?php $h->h($photo['thumb_height']); ?>"
        alt="<?php $h->h($photo['description_ja']); ?> Ranyuen"/>
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
