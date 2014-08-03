/// <reference path="Exam.ts" />
/// <reference path="helper.ts" />
var exam: Exam;
var currentQuestionNum = 0;
env = {
  lang: getItselfOrDefault(['ja', 'en'], getLocationSearch('lang')[0])
};
messages = {
  // (function(s){return s.split('').map(function(c){return '\\u' + c.charCodeAt(0).toString(16)}).join('')})('');
  'ja': {
    'Next': '\u6b21\u306e\u554f\u984c\u3078', // 次の問題へ
    'Score': '\u7d50\u679c\u306f\uff1f', // 結果は？
    'Point!': '\u70b9\uff01', // 点！
    'Correct Answer': '\u6b63\u89e3', // 正解
    'Your Choice': '\u3042\u306a\u305f\u306e\u56de\u7b54', // あなたの回答
    'Again!': '\u3082\u3046\u4e00\u5ea6\uff01' // もう一度！
  }
};
var examType = getItselfOrDefault(['easy', 'hard'], getLocationSearch('type')[0]);
new Background().setBackground(document.getElementById('exam'));
fetchExam('./assets/exam/' + examType + '.json', (_exam) => {
  exam = _exam;
  WhenQuestion.showQuestion();
}, (request) => {
  // TODO: JSONが読み込めなかった時のError処理
});

module WhenQuestion {
  /**
   * 出題用Formを表示し、EventListenerを設定する。
   */
  export function showQuestion(): void {
    if (currentQuestionNum >= 0)
      addClass(<HTMLLIElement> document.querySelectorAll('#examHeader li')[currentQuestionNum], 'done');
    document.getElementById('examBody').innerHTML =
      buildNode(exam.questions[currentQuestionNum],
                currentQuestionNum === exam.questions.length - 1);
    setListeners(exam.questions[currentQuestionNum].choiceType);
  }

  /**
   * 出題用Formを組み立てる。
   */
  function buildNode(question: Question, isLast: boolean): string {
    var actualChoiceNum = Math.floor(Math.random() * question.choices.length);
    question.actualChoice = question.choices[actualChoiceNum];
    var actualChoiceInput = node('input', {name: 'actualChoiceNum', type: 'hidden', value: actualChoiceNum});
    var buildChoiceNode = (inputType: string): string =>
      format('<ol id="examChoice">{0}</ol><form id="examForm">{1}{2}</form>', [
        question.actualChoice.statements.
          map((s) => format('<li class="choice">{0}{1}</li>', [
            node('div', {'class': inputType}),
            node('span', {}, s)
            ])).join(''),
        question.actualChoice.statements.
          map((s, i) => node('input', {name: 'choice', type: inputType, value: i + 1})).join(''),
        actualChoiceInput
        ]);
    return node('div', {id: 'examStatement'}, question.statement) +
      (() => {
        switch (question.choiceType) {
          case ChoiceType.SINGLE:
            return buildChoiceNode('radio');
          case ChoiceType.MULTI:
            return buildChoiceNode('checkbox');
          case ChoiceType.TEXT:
            return '<form id="examForm" class="show"><input name="choice" type="text" />' + actualChoiceInput + '</form>';
          default:
            throw new Error('');
        }
      })() +
      node('div', {'class': 'button'}, isLast ? __('Score') : __('Next'));
  }

  /**
   * 出題用FormのEventListenerを設定する。
   */
  function setListeners(choiceType: ChoiceType): void {
    var lis = toArray(document.querySelectorAll('#examChoice li'));
    switch (choiceType) {
      case ChoiceType.SINGLE:
        lis.forEach((li, i) => {
          (<HTMLLIElement> li).onclick = (evt) => {
            getChoiceInputs()[i].checked = true;
            refreshLI();
          };
        });
        break;
      case ChoiceType.MULTI:
        lis.forEach((li, i) => {
          (<HTMLLIElement> li).onclick = (evt) => {
            var input = getChoiceInputs()[i];
            input.checked = !input.checked;
            refreshLI();
          };
        });
        break;
      case ChoiceType.TEXT:
        break;
      default:
        throw new Error('');
    }
    (<HTMLDivElement> document.querySelector('#examBody .button')).onclick =
      (evt) => onsubmit();
    (<HTMLFormElement> document.getElementById('examForm')).onsubmit = (evt) => {
      evt.preventDefault();
      onsubmit();
    };
  }

  function refreshLI(): void {
    var lis = document.querySelectorAll('#examChoice li');
    getChoiceInputs().forEach((input, i) => {
      var li = <HTMLLIElement> lis[i];
      if (input.checked) addClass(li, 'checked');
      else removeClass(li, 'checked');
    });
  }

  function onsubmit(): void {
    var question = exam.questions[currentQuestionNum];
    if (!(question.response = getResponse(question))) return;
    currentQuestionNum += 1;
    if (currentQuestionNum === exam.questions.length) {
      var responses = JSON.stringify({
        examType: examType,
        responses: exam.questions.map((q) => {
          return {
            id: q.id,
            actualChoiceNum: q.choices.indexOf(q.actualChoice),
            response: q.response
          };
        })
      });
      // TODO: 回答をserverへ送る。
      WhenScore.showScore();
    } else {
      showQuestion();
    }
  }

  function getResponse(question: Question): any {
    var response: any;
    var inputs = getChoiceInputs();
    switch (question.choiceType) {
      case ChoiceType.SINGLE:
        for (var i = 0, iz = inputs.length; i < iz; ++i) {
          if (inputs[i].checked) {
            response = i + 1;
            break;
          }
        }
        if (response === 0) return false;
        break;
      case ChoiceType.MULTI:
        response = inputs.reduce((left: number[], right, i) => {
          if (right.checked) left.push(i + 1);
          return left;
        }, new Array<number>());
        if (response.length === 0) return false;
        break;
      case ChoiceType.TEXT:
        response = inputs[0].value.trim();
        if (response === '') return false;
        break;
      default:
        throw new Error('');
    }
    return response;
  }

  function getChoiceInputs(): HTMLInputElement[] {
    return toArray(document.querySelectorAll('#examForm input[name=choice]')).
      map((input) => <HTMLInputElement> input);
  }
}

module WhenScore {
  export function showScore(): void {
    document.getElementById('examBody').innerHTML = buildNode();
    var lis = getHeaderLis();
    exam.questions.forEach((q, i) => {
      removeClass(lis[i], 'done');
      removeClass(lis[i], 'focus');
      addClass(lis[i], q.isCorrect() ? 'correct' : 'incorrect');
    });
    addClass(lis[lis.length - 1], 'ans');
    setListeners();
  }

  function buildNode(): string {
    var score = exam.getScore();
    return node('div', {'class': 'score'}, score + ' ' + __('Point!')) +
      node('div', {'class': 'button'}, __('Again!'));
  }

  function setListeners(): void {
    getHeaderLis().forEach((li, i) => {
      if (exam.questions.length === i)
        li.onclick = (evt) => showScore();
      else
        li.onclick = (evt) => WhenAnswer.showAnswer(i);
    });
    (<HTMLDivElement> document.querySelector('#examBody .button')).onclick =
      (evt) => location.reload();
  }
}

module WhenAnswer {
  export function showAnswer(questionNum: number): void {
    document.getElementById('examBody').innerHTML =
      buildNode(exam.questions[questionNum]);
    getHeaderLis().forEach((li, i) => {
      if (i === questionNum) addClass(li, 'focus');
      else removeClass(li, 'focus');
    });
    setListeners();
  }

  function buildNode(question: Question): string {
    return node('div', {'id': 'examStatement'}, question.statement) +
      node('div', {}, __('Correct Answer') + ': ' + (() => {
        switch (question.choiceType) {
          case ChoiceType.SINGLE:
            return question.actualChoice.statements[question.actualChoice.answer - 1];
          case ChoiceType.MULTI:
            return question.actualChoice.answer.
              map((a) => question.actualChoice.statements[a - 1]).join(', ');
          case ChoiceType.TEXT:
            return question.actualChoice.answer.join(', ');
          default:
            throw new Error('');
        }
        return '';
      })()) +
      node('div', {'class': question.isCorrect() ? 'correct' : 'incorrect'}, __('Your Choice') + ': ' + (() => {
        switch (question.choiceType) {
          case ChoiceType.SINGLE:
            return question.actualChoice.statements[question.response - 1];
            break;
          case ChoiceType.MULTI:
            return question.response.
              map((r) => question.actualChoice.statements[r - 1]).join(', ');
            break;
          case ChoiceType.TEXT:
            return question.response;
          default:
            throw new Error('');
        }
        return '';
      })()) +
      node('div', {'class': 'button'}, __('Again!'));
  }

  function setListeners(): void {
    (<HTMLDivElement> document.querySelector('#examBody .button')).onclick =
      (evt) => location.reload();
  }
}

function getHeaderLis(): HTMLLIElement[] {
  return toArray(document.querySelectorAll('#examHeader li')).
    map((li) => <HTMLLIElement> li);
}

/**
 * Exam用JSON fileを取得する。
 */
function fetchExam(url: string, callback: (exam:Exam)=>void, errCallback?: (request:XMLHttpRequest)=>void) {
  var request = new XMLHttpRequest;
  request.open('GET', url);
  request.onreadystatechange = (evt) => {
    if (request.readyState === 4) {
      if (request.status === 200)
        callback(parseExamJson(request.responseText));
      else
        errCallback(request);
    }
  };
  request.send();
}

/**
 * Exam用JSON fileをparseしてExam objectを返す。
 */
function parseExamJson(json: string): Exam {
  var questions = new Array<Question>();
  var exam = JSON.parse(json);
  exam['questions'].forEach((q) => {
    var choiceType: ChoiceType;
    var choices = new Array<Choice>();
    if (q['answers'][0]['choices'] === null)
      choiceType = ChoiceType.TEXT;
    else if (typeof q['answers'][0]['ans'] === 'number')
      choiceType = ChoiceType.SINGLE;
    else if (typeof q['answers'][0]['ans'] === 'object')
      choiceType = ChoiceType.MULTI;
    else
      throw new Error('');
    q['answers'].forEach((c) => choices.push(new Choice(c['choices'], c['ans'])));
    // questions.push(new Question(q['id'], q['q'], choices, choiceType));
    choices.forEach((c) => {
      questions.push(new Question(q['id'], q['q'], [c], choiceType));
    });
  });
  questions = randamize(questions).slice(0, 10);
  return new Exam(exam['name'], questions);
}
// vim:set ft=typescript et sw=2 sts=2 ff=unix:
