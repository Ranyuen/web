---
title: New Photos (assets)
---
<script src="/assets/javascripts/layout.min.js" type="text/javascript" charset="utf-8" async defer></script>
<script src="/assets/javascripts/photo_editor.min.js" type="text/javascript" charset="utf-8" async defer></script>
<div class="wrapper" style="height: auto; margin: 20px 0 0 20px;">
  <div class="content-wrapper">
    <section class="content-header" style="margin-bottom: 50px;">
      <h1>画像アップロード (assets)</h1>
    </section>
    <section class="content">
      <form class="photos_editor" role="form" enctype="multipart/form-data" action="/admin/photos/assets" method="POST">
        <div class="photoFolder form-group">
          <label for="folder_select">ファイル選択</label>
          <input id="folder_select" class="form-controll" type="file" name="folder_select[]" multiple/>
        </div>
        <div class="form-group">
          <input type="submit" class="btn btn-primary" value="Upload" name="photos_assets_upload">
        </div>
      </form>
    </section>
  </div>
</div>
