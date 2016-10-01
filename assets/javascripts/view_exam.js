/// <reference path="Exam.ts" />
/// <reference path="helper.ts" />
var exam;
var currentQuestionNum = 0;
env = {
    lang: getItselfOrDefault(['ja', 'en'], getLocationSearch('lang')[0])
};
messages = {
    // (function(s){return s.split('').map(function(c){return '\\u' + c.charCodeAt(0).toString(16)}).join('')})('');
    'ja': {
        'Next': '\u6b21\u306e\u554f\u984c\u3078',
        'Score': '\u7d50\u679c\u306f\uff1f',
        'Point!': '\u70b9\uff01',
        'Correct Answer': '\u6b63\u89e3',
        'Your Choice': '\u3042\u306a\u305f\u306e\u56de\u7b54',
        'Again!': '\u3082\u3046\u4e00\u5ea6\uff01'
    }
};
var examType = getItselfOrDefault(['easy', 'hard', 'expert'], getLocationSearch('type')[0]);
new Background().setBackground(document.getElementById('exam'));
fetchExam('./assets/exam/' + examType + '.json', function (_exam) {
    exam = _exam;
    WhenQuestion.showQuestion();
}, function (request) {
    // TODO: JSONが読み込めなかった時のError処理
});

var WhenQuestion;
(function (WhenQuestion) {
    /**
    * 出題用Formを表示し、EventListenerを設定する。
    */
    function showQuestion() {
        if (currentQuestionNum >= 0)
            addClass(document.querySelectorAll('#examHeader li')[currentQuestionNum], 'done');
        document.getElementById('examBody').innerHTML = buildNode(exam.questions[currentQuestionNum], currentQuestionNum === exam.questions.length - 1);
        setListeners(exam.questions[currentQuestionNum].choiceType);
    }
    WhenQuestion.showQuestion = showQuestion;

    /**
    * 出題用Formを組み立てる。
    */
    function buildNode(question, isLast) {
        var actualChoiceNum = Math.floor(Math.random() * question.choices.length);
        question.actualChoice = question.choices[actualChoiceNum];
        var actualChoiceInput = node('input', { name: 'actualChoiceNum', type: 'hidden', value: actualChoiceNum });
        var buildChoiceNode = function (inputType) {
            return format('<ol id="examChoice">{0}</ol><form id="examForm">{1}{2}</form>', [
                question.actualChoice.statements.map(function (s) {
                    return format('<li class="choice">{0}{1}</li>', [
                        node('div', { 'class': inputType }),
                        node('span', {}, s)
                    ]);
                }).join(''),
                question.actualChoice.statements.map(function (s, i) {
                    return node('input', { name: 'choice', type: inputType, value: i + 1 });
                }).join(''),
                actualChoiceInput
            ]);
        };
        return node('div', { id: 'examStatement' }, question.statement) + (function () {
            switch (question.choiceType) {
                case 1 /* SINGLE */:
                    return buildChoiceNode('radio');
                case 2 /* MULTI */:
                    return buildChoiceNode('checkbox');
                case 3 /* TEXT */:
                    return '<form id="examForm" class="show"><input name="choice" type="text" />' + actualChoiceInput + '</form>';
                default:
                    throw new Error('');
            }
        })() + node('div', { 'class': 'button' }, isLast ? __('Score') : __('Next'));
    }

    /**
    * 出題用FormのEventListenerを設定する。
    */
    function setListeners(choiceType) {
        var lis = toArray(document.querySelectorAll('#examChoice li'));
        switch (choiceType) {
            case 1 /* SINGLE */:
                lis.forEach(function (li, i) {
                    li.onclick = function (evt) {
                        getChoiceInputs()[i].checked = true;
                        refreshLI();
                    };
                });
                break;
            case 2 /* MULTI */:
                lis.forEach(function (li, i) {
                    li.onclick = function (evt) {
                        var input = getChoiceInputs()[i];
                        input.checked = !input.checked;
                        refreshLI();
                    };
                });
                break;
            case 3 /* TEXT */:
                break;
            default:
                throw new Error('');
        }
        document.querySelector('#examBody .button').onclick = function (evt) {
            return onsubmit();
        };
        document.getElementById('examForm').onsubmit = function (evt) {
            evt.preventDefault();
            onsubmit();
        };
    }

    function refreshLI() {
        var lis = document.querySelectorAll('#examChoice li');
        getChoiceInputs().forEach(function (input, i) {
            var li = lis[i];
            if (input.checked)
                addClass(li, 'checked');
            else
                removeClass(li, 'checked');
        });
    }

    function onsubmit() {
        var question = exam.questions[currentQuestionNum];
        if (!(question.response = getResponse(question)))
            return;
        currentQuestionNum += 1;
        if (currentQuestionNum === exam.questions.length) {
            var responses = JSON.stringify({
                examType: examType,
                responses: exam.questions.map(function (q) {
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

    function getResponse(question) {
        var response;
        var inputs = getChoiceInputs();
        switch (question.choiceType) {
            case 1 /* SINGLE */:
                for (var i = 0, iz = inputs.length; i < iz; ++i) {
                    if (inputs[i].checked) {
                        response = i + 1;
                        break;
                    }
                }
                if (response === 0)
                    return false;
                break;
            case 2 /* MULTI */:
                response = inputs.reduce(function (left, right, i) {
                    if (right.checked)
                        left.push(i + 1);
                    return left;
                }, new Array());
                if (response.length === 0)
                    return false;
                break;
            case 3 /* TEXT */:
                response = inputs[0].value.trim();
                if (response === '')
                    return false;
                break;
            default:
                throw new Error('');
        }
        return response;
    }

    function getChoiceInputs() {
        return toArray(document.querySelectorAll('#examForm input[name=choice]')).map(function (input) {
            return input;
        });
    }
})(WhenQuestion || (WhenQuestion = {}));

var WhenScore;
(function (WhenScore) {
    function showScore() {
        document.getElementById('examBody').innerHTML = buildNode();
        var lis = getHeaderLis();
        exam.questions.forEach(function (q, i) {
            removeClass(lis[i], 'done');
            removeClass(lis[i], 'focus');
            addClass(lis[i], q.isCorrect() ? 'correct' : 'incorrect');
        });
        addClass(lis[lis.length - 1], 'ans');
        setListeners();
    }
    WhenScore.showScore = showScore;

    function buildNode() {
        var score = exam.getScore();
        return node('div', { 'class': 'score' }, score + ' ' + __('Point!')) + node('div', { 'class': 'button' }, __('Again!'));
    }

    function setListeners() {
        getHeaderLis().forEach(function (li, i) {
            if (exam.questions.length === i)
                li.onclick = function (evt) {
                    return showScore();
                };
            else
                li.onclick = function (evt) {
                    return WhenAnswer.showAnswer(i);
                };
        });
        document.querySelector('#examBody .button').onclick = function (evt) {
            return location.reload();
        };
    }
})(WhenScore || (WhenScore = {}));

var WhenAnswer;
(function (WhenAnswer) {
    function showAnswer(questionNum) {
        document.getElementById('examBody').innerHTML = buildNode(exam.questions[questionNum]);
        getHeaderLis().forEach(function (li, i) {
            if (i === questionNum)
                addClass(li, 'focus');
            else
                removeClass(li, 'focus');
        });
        setListeners();
    }
    WhenAnswer.showAnswer = showAnswer;

    function buildNode(question) {
        return node('div', { 'id': 'examStatement' }, question.statement) + node('div', {}, __('Correct Answer') + ': ' + (function () {
            switch (question.choiceType) {
                case 1 /* SINGLE */:
                    return question.actualChoice.statements[question.actualChoice.answer - 1];
                case 2 /* MULTI */:
                    return question.actualChoice.answer.map(function (a) {
                        return question.actualChoice.statements[a - 1];
                    }).join(', ');
                case 3 /* TEXT */:
                    return question.actualChoice.answer.join(', ');
                default:
                    throw new Error('');
            }
            return '';
        })()) + node('div', { 'class': question.isCorrect() ? 'correct' : 'incorrect' }, __('Your Choice') + ': ' + (function () {
            switch (question.choiceType) {
                case 1 /* SINGLE */:
                    return question.actualChoice.statements[question.response - 1];
                    break;
                case 2 /* MULTI */:
                    return question.response.map(function (r) {
                        return question.actualChoice.statements[r - 1];
                    }).join(', ');
                    break;
                case 3 /* TEXT */:
                    return question.response;
                default:
                    throw new Error('');
            }
            return '';
        })()) + node('div', { 'class': 'button' }, __('Again!'));
    }

    function setListeners() {
        document.querySelector('#examBody .button').onclick = function (evt) {
            return location.reload();
        };
    }
})(WhenAnswer || (WhenAnswer = {}));

function getHeaderLis() {
    return toArray(document.querySelectorAll('#examHeader li')).map(function (li) {
        return li;
    });
}

/**
* Exam用JSON fileを取得する。
*/
function fetchExam(url, callback, errCallback) {
    var request = new XMLHttpRequest;
    request.open('GET', url);
    request.onreadystatechange = function (evt) {
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
function parseExamJson(json) {
    var questions = new Array();
    var exam = JSON.parse(json);
    exam['questions'].forEach(function (q) {
        var choiceType;
        var choices = new Array();
        if (q['answers'][0]['choices'] === null)
            choiceType = 3 /* TEXT */;
        else if (typeof q['answers'][0]['ans'] === 'number')
            choiceType = 1 /* SINGLE */;
        else if (typeof q['answers'][0]['ans'] === 'object')
            choiceType = 2 /* MULTI */;
        else
            throw new Error('');
        q['answers'].forEach(function (c) {
            return choices.push(new Choice(c['choices'], c['ans']));
        });

        // questions.push(new Question(q['id'], q['q'], choices, choiceType));
        choices.forEach(function (c) {
            questions.push(new Question(q['id'], q['q'], [c], choiceType));
        });
    });
    questions = randamize(questions).slice(0, 10);
    return new Exam(exam['name'], questions);
}
// vim:set ft=typescript et sw=2 sts=2 ff=unix:
