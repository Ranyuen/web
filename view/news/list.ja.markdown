---
---
{% for article in articles %}
<div>
  <a href="/{{ article.lang }}/news/{{ article.url }}">{{ article.title }}</a>
</div>
{% endfor %}
