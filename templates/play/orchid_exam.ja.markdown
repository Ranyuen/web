---
title: ラン検定
---
<style>
  #examChanger {
    background: black;
    padding: 0;
  }
  #examChanger li {
    display: inline-block;
    height: 1.8em;
  }
  #examChanger li a {
    background: black;
    border-radius: 4px;
    color: white;
    cursor: pointer;
    display: block;
    font-weight: bold;
    height: 100%;
    line-height: 1.8em;
    padding: 0 0.4em;
    text-align: center;
    text-decoration: none;
  }
  #examChanger li a:hover {
    background: #eee;
    color: inherit;
  }
  #examChanger li a.active {
    background: red;
    color: white;
    cursor: default;
  }
</style>
<link rel="stylesheet" href="assets/stylesheets/exam.css" />

ラン検定
==
<ul id="examChanger">
  <li><a class="easy" onclick="changeExam('easy')">初級編 (はじめの一歩)</a></li>
  <li><a class="hard" onclick="changeExam('hard')">上級編</a></li>
</ul>
<div id="exam">
  <div id="examHeader">
    <ol>
      <li>1</li><li>2</li><li>3</li><li>4</li><li>5</li><li>6</li><li>7</li><li>8</li><li>9</li><li>10</li><li>Ans</li>
    </ol>
  </div>
  <div id="examBody"></div>
</div>
<script src="/assets/javascripts/Exam.js"></script>
<script src="/assets/javascripts/helper.js"></script>
<script src="/assets/javascripts/view_exam.js"></script>
<script>
function changeExam(examName) {
  location.href = URI(location.href).
    removeSearch('type').
    addSearch('type', examName).
    toString();
}
function prepareExam(examName) {
  var nodes = document.querySelectorAll('#examChanger a');
  var i, iz;

  for (i = 0, iz = nodes.length; i < iz; ++i) {
    if (nodes[i].className.match(examName)) {
      if (nodes[i].className.match('active')) { return };
      nodes[i].className += ' active';
    } else {
      nodes[i].className = nodes[i].className.replace(/\sactive/g, '');
    }
  }
}
prepareExam(getItselfOrDefault(['easy', 'hard'], getLocationSearch('type')[0]));
</script>
