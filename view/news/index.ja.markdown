---
title: おしらせ
---
{{ title }}
==
{% for tag in tags %}
<div>
  <div>
    <a href="/news/list?tag={{ tag.name_en }}">{{ tag.name_ja }}</a>
  </div>
  {% for article in tag.articles %}
    <div>
      <a href="/{{ article.lang }}/news/{{ article.url }}">{{ article.title | raw }}</a>
    </div>
  {% endfor %}
</div>
{% endfor %}
