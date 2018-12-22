---
title: お知らせ
---
{{ title }}
==
<link rel="stylesheet" href="/assets/stylesheets/notice.css"/>
{% for article in articles %}
  <div class="news-list-content">
    <a href="{{ article.path }}">{{ article.getContent(lang).plainTitle | raw }}</a>
  </div>
{% endfor %}
