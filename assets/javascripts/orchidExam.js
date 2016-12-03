"use strict";function getCorrectAnswers(){var e=questions.map(function(e){for(var r in e.answers)if(e.answers[r].is_correct)return e.answers[r].choice});return e}function checkAnswers(){for(var e=[],r=getCorrectAnswers(),t=1,a=questions.length;a>=t;t++)$("input[name=group"+t+"]:checked").val()?e.push($("input[name=group"+t+"]:checked").val()):e.push("未選択");$(".thum").css({filter:"brightness(35%)",border:"solid 2px red",margin:"-2px"});var n=e.map(function(e,t){var a;return"photo"==exam.type?($("#"+r[t]).css({filter:"brightness(100%)"}),e===r[t]?(++exam.correctsNumber,a='<div class="is_correct"><span class="red">◯ 正解!</span>'):a='<div class="is_correct"><span class="blue">× 不正解</span>'):e===r[t]?(++exam.correctsNumber,a='<div style="margin-top: 15px;"><span style="color: red; font-weight: bold;">◯</span> あなたの回答 : <span style="color: red;">'+e+"</span><div>"):a='<div style="margin-top: 15px;"><span style="color: blue; font-weight: bold;">×</span> あなたの回答 : '+e+'</div><div style="color: red;">　正解 : '+r[t]+"</div>",a});for(var t in n){var s=+t+1;$("#true_or_false"+s).html(n[t]),$("description"+s).html(questions[t].description)}makeRecord(),$("#t_userName").val()&&(exam.userName=sanitaize($("#t_userName").val()),registResult()),"photo"===exam.type?($("#chk").remove(),$(".radio").remove(),$("#examHeader").remove()):($(".choices").remove(),$("#chk").remove(),$("#examHeader").remove()),createLinks()}function registResult(){$.ajax({type:"POST",url:"/play/exam",data:{exam:exam}})}function makeRecord(){$("#record").show();var e={},r=null;return r="easy"===exam.type?"初級編":"hard"===exam.type?"上級編":"expert"===exam.type?"博士編":"写真編",exam.record=exam.point*exam.correctsNumber,100!==exam.questionNumber?exam.record>=90?e.lisence=r+"1級":exam.record>=80?e.lisence=r+"2級":exam.record>=70?e.lisence=r+"3級":exam.record>=60?e.lisence=r+"4級":exam.record>=50?e.lisence=r+"5級":exam.record>=40?e.lisence=r+"6級":exam.record>=30?e.lisence=r+"7級":exam.record>=20?e.lisence=r+"8級":exam.record>=10?e.lisence=r+"9級":e.lisence=r+"10級":exam.record>=90?e.lisence=r+"十段":exam.record>=80?e.lisence=r+"九段":exam.record>=70?e.lisence=r+"八段":exam.record>=60?e.lisence=r+"七段":exam.record>=50?e.lisence=r+"六段":exam.record>=40?e.lisence=r+"五段":exam.record>=30?e.lisence=r+"四段":exam.record>=20?e.lisence=r+"三段":exam.record>=10?e.lisence=r+"二段":e.lisence=r+"一段",exam.record=exam.record+"点",$("#record").html('<div style="text-align: center; font-weight: bold;">あなたの検定結果は '+e.lisence+' !</div><div style="text-align: center; font-weight: bold;">今回の点数は '+exam.record+" でした。</div>"),exam.record}function createLinks(){$(".links").html('<div style="text-align :center; margin-bottom: 20px;"><input type="button" value="もう1度挑戦する" onclick="location.reload()" class="btn"></div><div style="text-align :center;"><a href="/play/exam/">ラン検定トップへ</div>')}function finish(){exam.time=0}function toMs(e){if(!(0>e)){var r="",t=e/60|0,a=e%60;return r=0!=t?t+"分"+a+"秒":a+"秒"}}function sanitaize(e){return e.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;").replace(/'/g,"&#39;")}var exam={},init=$(function(){exam.questionNumber=questions.length,exam.time=25===exam.questionNumber?900:3600,exam.point=25===exam.questionNumber?4:1,exam.correctsNumber=0,exam.record=0,exam.type=questions[0].type,exam.userName=null;var e=setInterval(function(){0===exam.time&&(clearInterval(e),checkAnswers()),exam.time--,$("#time").text(toMs(exam.time))},1e3)});
//# sourceMappingURL=orchidExam.js.map