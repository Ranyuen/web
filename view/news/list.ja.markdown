---
title: おしらせ
---
{{ title }}
==
{% for article in articles %}
  <div>
    <a href="{{ article.path }}">{{ article.getContent(lang).plainTitle | raw }}</a>
  </div>
{% endfor %}
