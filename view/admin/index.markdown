---
title: Admin
---
Admin
==
Articles
--
[New article](articles/edit/0)

{% for article in articles %}
  1. [{{ article.id }} {{ article.contents[0].plainTitle }}](articles/edit/{{ article.id }})
{% endfor %}
