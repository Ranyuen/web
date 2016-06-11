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
    win.print();
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
