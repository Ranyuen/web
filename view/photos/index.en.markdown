---
title: Photos
---
Photos in Ranyuen
==
<link href="/assets/stylesheets/photoGallery.css" rel="stylesheet"/>
<link href="/assets/stylesheets/colorbox.css" rel="stylesheet"/>
<link href="/assets/stylesheets/pagination.css" rel="stylesheet"/>

<form id="search-form" method="GET">
  <select id="search-form-species_name" name="species_name">
    <option value="" {% if species_name == null %}selected{% endif %}>--Select species--</option>
    <option value="all" {% if species_name == 'all' %}selected{% endif %}>All</option>
    <option value="Calanthe" {% if species_name == 'Calanthe' %}selected{% endif %}>Calanthe</option>
    <option value="Ponerorchis" {% if species_name == 'Ponerorchis' %}selected{% endif %}>Ponerorchis</option>
    <option value="Japanease native orchid" {% if species_name == 'Japanease native orchid' %}selected{% endif %}>Japanese native orchids</option>
    <option value="Ranyuen style" {% if species_name == 'Ranyuen style' %}selected{% endif %}>Ranyuen style</option>
    <option value="others" {% if species_name == 'others' %}selected{% endif %}>Others</option>
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
        title="{{ item.description_en }} Ranyuen">
        <img rel="gallery"
        src="/api/photo?format=jpeg&uuid={{ item.uuid }}&width={{ item.thumb_width }}"
        width="{{ item.thumb_width }}"
        height="{{ item.thumb_height }}"
        alt="{{ item.description_en }} Ranyuen"/>
      </a>
      <div class="photo-description">
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
    document.getElementById('search-form-color').onchange = function () {
      document.getElementById('search-form-color').submit();
    };
  });
</script>
