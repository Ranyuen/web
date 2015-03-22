---
title: ぬりえ
---
<link rel="stylesheet" href="assets/stylesheets/playColoring.css" />
<script type="text/javascript">
function closePrint(iframe) {
  document.body.removeChild(iframe);
}

function setPrint(url) {
  var img,
      iframe = this,
      win    = this.contentWindow,
      doc    = this.contentDocument;
  doc.body.innerHTML = url;
  img = doc.createElement('img');
  img.src = url;
  img.style.width = '100%';
  doc.body.appendChild(img);
  img.onload = function () {
    win.onbeforeunload = closePrint.bind(null, iframe);
    win.onafterprint = closePrint.bind(null, iframe);
    win.focus();
    if (doc.execCommand) {
      doc.execCommand('print', false, null);
    } else {
      win.print();
    }
  };
}

function printPage(url) {
  var oHiddFrame = document.createElement("iframe");
  oHiddFrame.onload = function () {
    setPrint.call(this, url);
  };
  oHiddFrame.style.visibility = "hidden";
  oHiddFrame.style.position = "fixed";
  oHiddFrame.style.right = "0";
  oHiddFrame.style.bottom = "0";
  oHiddFrame.src = url;
  document.body.appendChild(oHiddFrame);
}
</script>
ぬりえ　～こころのはな～
==
<p><em>プリンターで、印刷したい画像をクリックしてね！</em></p>
<div class="coloring">
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_001.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_001.jpg" alt="塗り絵001">
      </span>
    </p>
  </div>
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_002.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_002.jpg" alt="塗り絵002">
      </span>
    </p>
  </div>
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_003.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_003.jpg" alt="塗り絵003">
      </span>
    </p>
  </div>
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_004.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_004.jpg" alt="塗り絵004">
      </span>
    </p>
  </div>
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_005.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_005.jpg" alt="塗り絵005">
      </span>
    </p>
  </div>
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_006.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_006.jpg" alt="塗り絵006">
      </span>
    </p>
  </div>
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_007.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_007.jpg" alt="塗り絵007">
      </span>
    </p>
  </div>
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_008.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_008.jpg" alt="塗り絵008">
      </span>
    </p>
  </div>
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_009.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_009.jpg" alt="塗り絵009">
      </span>
    </p>
  </div>
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_010.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_010.jpg" alt="塗り絵010">
      </span>
    </p>
  </div>
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_011.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_011.jpg" alt="塗り絵011">
      </span>
    </p>
  </div>
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_012.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_012.jpg" alt="塗り絵012">
      </span>
    </p>
  </div>
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_013.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_013.jpg" alt="塗り絵013">
      </span>
    </p>
  </div>
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_014.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_014.jpg" alt="塗り絵014">
      </span>
    </p>
  </div>
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_015.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_015.jpg" alt="塗り絵015">
      </span>
    </p>
  </div>
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_016.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_016.jpg" alt="塗り絵016">
      </span>
    </p>
  </div>
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_017.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_017.jpg" alt="塗り絵017">
      </span>
    </p>
  </div>
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_018.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_018.jpg" alt="塗り絵018">
      </span>
    </p>
  </div>
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_019.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_019.jpg" alt="塗り絵019">
      </span>
    </p>
  </div>
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_020.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_020.jpg" alt="塗り絵020">
      </span>
    </p>
  </div>
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_021.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_021.jpg" alt="塗り絵021">
      </span>
    </p>
  </div>
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_022.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_022.jpg" alt="塗り絵022">
      </span>
    </p>
  </div>
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_023.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_023.jpg" alt="塗り絵023">
      </span>
    </p>
  </div>
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_024.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_024.jpg" alt="塗り絵024">
      </span>
    </p>
  </div>
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_025.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_025.jpg" alt="塗り絵025">
      </span>
    </p>
  </div>
  <div class="coloring-square">
    <p>
      <span onclick="printPage('/assets/images/play_coloring_026.jpg');">
        <img class="coloring-img" src="/assets/images/play_coloring_026.jpg" alt="塗り絵026">
      </span>
    </p>
  </div>
</div>