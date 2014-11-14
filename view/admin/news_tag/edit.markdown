---
title: New news tag
---
<form action="/admin/news_tag/update/{{ tag.id }}" method="POST">
  <div>
    <label for="news_tag_name_ja">Name (ja)</label>
    <input id="news_tag_name_ja" name="name_ja" value="{{ tag.name_ja }}" type="text" required size="20"/>
  </div>
  <div>
    <label for="news_tag_name_en">Name (en)</label>
    <input id="news_tag_name_en" name="name_en" value="{{ tag.name_en }}" type="text" required size="20"/>
  </div>
  <div>
    <input value="Update" type="submit"/>
  </div>
  <input name="_METHOD" value="PUT" type="hidden"/>
</form>
<form action="/admin/news_tag/destroy/{{ tag.id }}" method="POST">
  <div>
    <input value="Delete" type="submit"/>
  </div>
  <input name="_METHOD" value="DELETE" type="hidden"/>
</form>
