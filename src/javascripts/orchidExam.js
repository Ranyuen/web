'use strict';

var exam = {};

var init = $(function () {
  exam.questionNumber = questions.length;
  exam.time           = exam.questionNumber === 25 ? 900 : 3600;
  exam.point          = exam.questionNumber === 25 ? 4 : 1;
  exam.correctsNumber = 0;
  exam.record         = 0;
  exam.type           = questions[0].type;
  exam.userName       = null;
  var countDown = setInterval(function() {
    if (exam.time === 0) {
      clearInterval(countDown);
      checkAnswers();
    }
    exam.time--;
    $('#time').text(toMs(exam.time));
  }, 1000);
});

function getCorrectAnswers() {
  var collectAnswers = questions.map(function(element) {
    for(var i in element.answers) {
      if (element.answers[i].is_correct) {

        return element.answers[i].choice;
      }
    }
  });

  return collectAnswers;
}

function checkAnswers() {
  var selectedAnswers = [];
  var collectAnswers  = getCorrectAnswers();
  for (var i = 1, len = questions.length; i <= len; i++) {
    if ($('input[name=group' + i +']:checked').val()) {
      selectedAnswers.push($('input[name=group' + i +']:checked').val());
    } else {
      selectedAnswers.push('未選択');
    }
  }
  $('.thum').css({
    'filter': 'brightness(35%)',
    'border': 'solid 2px red',
    'margin': '-2px'
  });
  var correction = selectedAnswers.map(function(element, index) {
    var html;
    if (exam.type == 'photo') {
      $('#' + collectAnswers[index]).css({
        'filter': 'brightness(100%)'
      });
      if (element === collectAnswers[index]) {
        ++exam.correctsNumber;
        html = '<div class="is_correct"><span class="red">◯ 正解!</span>';
      } else {
        html = '<div class="is_correct"><span class="blue">× 不正解</span>';
      }
    } else {
      if (element === collectAnswers[index]) {
        ++exam.correctsNumber;
        html = '<div style="margin-top: 15px;"><span style="color: red; font-weight: bold;">◯</span> あなたの回答 : <span style="color: red;">' + element + '</span><div>';
      } else {
        html = '<div style="margin-top: 15px;"><span style="color: blue; font-weight: bold;">×</span> あなたの回答 : ' + element + '</div><div style="color: red;">　正解 : ' + collectAnswers[index] + '</div>';
      }
    }
    return html;
  });

  for(var i in correction) {
    var _i = Number(i) + 1;
    $('#true_or_false' + _i).html(correction[i]);
    $('description' + _i).html(questions[i].description);

  }
  makeRecord();
  if ($('#t_userName').val()) {
    exam.userName = sanitaize($('#t_userName').val());
    registResult();
  } else {

  }
  if (exam.type === 'photo') {
    $('#chk').remove();
    $('.radio').remove();
    $('#examHeader').remove();
  } else {
    $('.choices').remove();
    $('#chk').remove();
    $('#examHeader').remove();
  }
  createLinks();
}

function registResult() {
  $.ajax({
    type: "POST",
    url: "/play/exam",
    data: {
      exam : exam,
    },
  });
}

function makeRecord() {
  $('#record').show();
  var record = {};
  var type   = null;
  if (exam.type === 'easy') {
    type = '初級編';
  } else if (exam.type === 'hard') {
    type = '上級編';
  } else if (exam.type === 'expert') {
    type = '博士編';
  } else {
    type = '写真編';
  }
  exam.record = exam.point * exam.correctsNumber;
  if (exam.questionNumber !== 100) {
    if (exam.record >= 90) {
      record.lisence = type + '1級';
    } else if (exam.record >= 80) {
      record.lisence = type + '2級';
    } else if (exam.record >= 70) {
      record.lisence = type + '3級';
    } else if (exam.record >= 60) {
      record.lisence = type + '4級';
    } else if (exam.record >= 50) {
      record.lisence = type + '5級';
    } else if (exam.record >= 40) {
      record.lisence = type + '6級';
    } else if (exam.record >= 30) {
      record.lisence = type + '7級';
    } else if (exam.record >= 20) {
      record.lisence = type + '8級';
    } else if (exam.record >= 10) {
      record.lisence = type + '9級';
    } else {
      record.lisence = type + '10級';
    }
  } else {
    if (exam.record >= 90) {
      record.lisence = type + '十段';
    } else if (exam.record >= 80) {
      record.lisence = type + '九段';
    } else if (exam.record >= 70) {
      record.lisence = type + '八段';
    } else if (exam.record >= 60) {
      record.lisence = type + '七段';
    } else if (exam.record >= 50) {
      record.lisence = type + '六段';
    } else if (exam.record >= 40) {
      record.lisence = type + '五段';
    } else if (exam.record >= 30) {
      record.lisence = type + '四段';
    } else if (exam.record >= 20) {
      record.lisence = type + '三段';
    } else if (exam.record >= 10) {
      record.lisence = type + '二段';
    } else {
      record.lisence = type + '一段';
    }
  }
  exam.record = exam.record + '点';
  $('#record').html(
    '<div style="text-align: center; font-weight: bold;">あなたの検定結果は ' + record.lisence + ' !</div>' +
    '<div style="text-align: center; font-weight: bold;">今回の点数は ' + exam.record + ' でした。</div>'
  );

  return exam.record;
}

function createLinks() {
  $('.links').html(
    '<div style="text-align :center; margin-bottom: 20px;"><input type="button" value="もう1度挑戦する" onclick="location.reload()" class="btn"></div>' +
    '<div style="text-align :center;"><a href="/play/exam/">ラン検定トップへ</div>'
  );
}

function finish() {
  exam.time = 0;
}

function toMs(time) {
  if (time < 0) { return };
  var ms = '';
  var m  = time / 60 | 0;
  var s  = time % 60;
  if (m != 0) {
    ms = m + '分' + s + '秒';
  } else {
    ms = s + '秒';
  }

  return ms;
}

function sanitaize(userName) {

  return userName.replace(/&/g, '&amp;')
                 .replace(/</g, '&lt;')
                 .replace(/>/g, '&gt;')
                 .replace(/"/g, '&quot;')
                 .replace(/'/g, '&#39;');
}
