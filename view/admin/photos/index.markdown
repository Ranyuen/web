---
title: New Photos
---
<script src="/assets/javascripts/layout.min.js" type="text/javascript" charset="utf-8" async defer></script>
<script src="/assets/javascripts/photo_editor.min.js" type="text/javascript" charset="utf-8" async defer></script>
<div class="wrapper" style="height: auto; margin: 20px 0 0 20px;">
  <div class="content-wrapper">
    <section class="content-header" style="margin-bottom: 50px;">
      <h1>花華アルバム画像アップロード</h1>
    </section>
    <section class="content">
      <form class="photos_editor" role="form" enctype="multipart/form-data" action="/admin/photos/" onsubmit="return checkRequired(this)" method="POST">
        <div class="photoFolder form-group">
          <label for="folder_select">ファイル選択</label>
          <input id="folder_select" class="form-controll" type="file" name="folder_select[]" multiple/>
        </div>
        <div class="description_ja form-group">
          <label for="description_ja_input">description_ja (*必須)</label>
          <div>
            <input id="description_ja_input" type="text" name="description_ja" size="50"/>
          </div>
        </div>
        <div class="description_en form-group">
          <label for="description_en_input">description_en (*必須)</label>
          <div>
            <input id="description_en_input" type="text" name="description_en" size="50"/>
          </div>
        </div>
        <div class="species_name form-group">
          <label for="species_name_input">species_name (*必須)</label>
          <div>
            <input id="species_name_input" type="text" name="species_name" size="50"/>
          </div>
        </div>
        <div class="product_name form-group">
          <label for="product_name_input">product_name</label>
          <div>
            <input id="product_name_input" type="text" name="product_name" size="50"/>
          </div>
        </div>
        <div class="color form-group">
          <label for="color_input">color</label>
          <div>
            <input id="color_input" type="text" name="color" size="50"/>
          </div>
        </div>
        <div class="form-group">
          <input type="submit" class="btn btn-primary" value="Upload" name="photos_upload">
        </div>
      </form>
    </section>
    <section>
        <table class="table">
          <tr>
            <th>作成日</th>
            <th>種名</th>
            <th>登録件数</th>
            <th>削除</th>
          </tr>
          {% for raw in photos_createdAt %}
          <form class="delete_photos" role="form" action="/admin/photos/delete" method="POST">
            <tr>
              <td>{{ raw.created_at }}</td>
              <td>{{ raw.species_name }}</td>
              <td>{{ raw.count }}</td>
              <td><input type="submit" class="btn btn-primary" value="削除" name="delete_photos"/><input type="hidden" name="datetime" value="{{ raw.created_at }}"></td>
            </tr>
          </form>
          {% endfor %}
        </table>
    </section>
  </div>
</div>
