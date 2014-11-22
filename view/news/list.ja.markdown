---
---
{% for article in articles %}
<div>
  <a href="/{{ article.lang }}/news/{{ article.url }}">{{ article.title | raw }}</a>
</div>
{% endfor %}
