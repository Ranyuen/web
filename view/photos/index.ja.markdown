---
title: 花華アルバム
---
花華アルバム
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
    <option value="Ranyuen style" {% if species_name == 'Ranyuen style' %}selected{% endif %}>蘭裕園スタイル</option>
  </select>
</form>
<form class="select" id="search-form-color" method="GET">
  <input type="hidden" name="species_name" value="{{ species_name }}">
  <select id="search-form-color" name="color">
    {% if species_name == 'Calanthe' %}
      <option value="">select colors</option>}
      {% for color in colors %}
        <option value="{{ color.color }}" {% if select_color == color.color %}selected{% endif %}>{{ color.color }}</option>
      {% endfor %}
    {% else %}
      <option value="">----</option>}
    {% endif %}
  </select>
</form>
<div style="clear: both;">
  {{ paginator | raw }}
</div>
<div id="photo-gallery" class="photos">
  {% for item in paginator %}
    <div class="photo">
      <a href="/images/gallery/{{ item.uuid }}.jpg"
        class="lightbox"
        target="_blank"
        title="{{ item.description_ja }} 蘭裕園 Ranyuen">
        <img rel="gallery"
          src="/api/photo?format=jpeg&uuid={{ item.uuid }}&width={{ item.thumb_width }}"
          width="{{ item.thumb_width }}"
          height="{{ item.thumb_height }}"
          alt="{{ item.description_ja }} 蘭裕園 Ranyuen"/>
      </a>
      <div class="photo-description">
        <span>{{ item.description_ja }}</span> /
        <span>{{ item.description_en }}</span>
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
    document.getElementById('search-form-color').onchange = function () {
      document.getElementById('search-form-color').submit();
    };
  });
</script>
