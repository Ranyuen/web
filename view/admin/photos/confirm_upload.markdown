---
title: Uploaded images
---
Uploaded images
==
{{ uploaded|length }} files upload!

{% for image in uploaded %}
<figure>
{{ image | echoImg('20%', 'auto') | raw }}
<figcaption>{{ image }}</figcaption>
</figure>
{% endfor %}

[admin/photos/assets](/admin/photos/assets)

