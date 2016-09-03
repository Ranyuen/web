---
title: 写真を見る
---
蘭裕園の写真を見る
==
<link href="/assets/stylesheets/photoGallery.css" rel="stylesheet"/>
<link href="/assets/stylesheets/colorbox.css" rel="stylesheet"/>
<link href="/assets/stylesheets/pagination.css" rel="stylesheet"/>

<form class="select" id="search-form" method="GET">
  <select id="search-form-species_name" name="species_name">
    <option value="" {% if species_name == null %}selected{% endif %}>-- 写真の種類を選択する --</option>
    <option value="all" {% if species_name == 'all' %}selected{% endif %}>全て見る</option>
    <option value="Calanthe" {% if species_name == 'Calanthe' %}selected{% endif %}>エビネ</option>
    <option value="Ponerorchis" {% if species_name == 'Ponerorchis' %}selected{% endif %}>アワチドリ / 夢ちどり</option>
    <option value="Japanease native orchid" {% if species_name == 'Japanease native orchid' %}selected{% endif %}>日本の野生ラン</option>
    <option value="others" {% if species_name == 'others' %}selected{% endif %}>その他</option>
  </select>
</form>

<div>
  {{ paginator | raw }}
</div>

<div id="photo-gallery" class="photos">

  {% for item in paginator %}
    <div class="photo">
      <a href="/images/gallery/{{ item.id }}.jpg"
        class="lightbox"
        target="_blank"
        title="{{ item.description_ja }} 蘭裕園 Ranyuen">
        <img rel="gallery"
          src="/api/photo?format=jpeg&id={{ item.id }}&width={{ item.thumb_width }}"
          width="{{ item.thumb_width }}"
          height="{{ item.thumb_height }}"
          alt="{{ item.description_ja }} 蘭裕園 Ranyuen"/>
      </a>
      <div class="photo-description">
        <div>{{ item.description_ja }}</div>
        <div>{{ item.description_en }}</div>
      </div>
    </div>
  {% endfor %}
</div>

<div>
  {{ paginator | raw }}
</div>

<script>
$('.pagination li a').each(function() {
  var href = $(this).attr('href');
  $(this).attr('href', 'photos/' + href);
});
</script>

<script src="/assets/javascripts/photoGallery.min.js"></script>
<script>
  window.addEventListener('DOMContentLoaded', function () {
    new PhotoGallery().init(document.getElementById("photo-gallery"));
    document.getElementById('search-form-species_name').onchange = function () {
      document.getElementById('search-form').submit();
    };



  });
</script>
