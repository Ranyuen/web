---
title: 写真を見る
---
蘭裕園の写真を見る
==
<?php
$controller = new \Ranyuen\Controller\ApiPhotos;
$photos = $controller->render('GET', [], [ 'limit' => 100 ]);
?>
<link href="../../assets/bower_components/colorbox/example1/colorbox.css" rel="stylesheet">
<script src="../../assets/bower_components/colorbox/jquery.colorbox.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $("a[rel='image_pop']").colorbox({
          fixed: true,
          height: "90%",
          transition: "fade",
          speed: 300,
          width: "90%"
        });
        //$(".example").colorbox();
    });
</script>
<style>
.photos .photo {
  background: #f0f5f0;
  float: left;
  margin: 0.6%;
  min-height: 230px;
  width: 32%;
}
</style>
<div class="photos">
<?php foreach ($photos as $photo) { ?>
  <div class="photo">
    <a href="/Calanthe/gallery/<?php $h->h($photo['id']); ?>.jpg" rel="image_pop" title="<?php $h->h($photo['description_ja']); ?> 蘭裕園">
      <img src="/api/photo?format=jpeg&id=<?php $h->h($photo['id']); ?>" alt="<?php $h->h($photo['description_ja']); ?> 蘭裕園"/>
    </a>
    <div>
      <div><?php $h->h($photo['description_ja']); ?></div>
      <div><?php $h->h($photo['description_en']); ?></div>
    </div>
  </div>
<?php } ?>
</div>
<!--
<script src="/assets/bower_components/masonry/dist/masonry.pkgd.min.js"></script>
<script>
new Masonry(document.getElementsByClassName('photos')[0], {
  columnWidth: '.photo',
  gutter: 0,
  ite../../assets/bower_componentselector: '.photo'
});
</script>
-->
