'use strict';

window.onload = function() {
  document.getElementById("btnUpload").disabled = true;
}

function checkRequired (elm) {
  var required = ['description_ja', 'description_en', 'species_name'];
  for (var i = 0; i < required.length; i++) {
    var obj = elm.elements[required[i]];
    if(obj.value === ''){
      alert('未入力の必須入力項目があります');
      return false;
    }
  }
}
