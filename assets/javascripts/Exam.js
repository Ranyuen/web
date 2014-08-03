/**
* 複数のQuestionを持つ。
*/
var Exam = (function () {
    function Exam(name, questions) {
        this.name = '';
        this.questions = [];
        this.name = name;
        this.questions = questions;
    }
    Exam.prototype.getScore = function () {
        return this.questions.reduce(function (acm, q) {
            return acm + (q.isCorrect() ? 1 : 0);
        }, 0) * 10;
    };
    return Exam;
})();

/**
* 複数の選択肢、userからの返答を持つ。
*/
var Question = (function () {
    function Question(id, statement, choices, choiceType) {
        this.id = 0;
        this.statement = '';
        this.id = id;
        this.statement = statement;
        this.choices = choices;
        this.choiceType = choiceType;
    }
    Question.prototype.isCorrect = function () {
        return this.actualChoice.isCorrect(this.response, this.choiceType);
    };
    return Question;
})();

var ChoiceType;
(function (ChoiceType) {
    /** 他選択肢・一選択問題: 選択肢は複数文字列、正解は単一数値 */
    ChoiceType[ChoiceType["SINGLE"] = 1] = "SINGLE";

    /** 他選択肢・多選択問題: 選択肢は複数文字列、正解は複数数値 */
    ChoiceType[ChoiceType["MULTI"] = 2] = "MULTI";

    /** 一文字列回答問題    : 選択肢はnull、      正解は複数文字列 */
    ChoiceType[ChoiceType["TEXT"] = 3] = "TEXT";
})(ChoiceType || (ChoiceType = {}));

/**
* 選択肢、正解を持つ。
*/
var Choice = (function () {
    function Choice(statements, answer) {
        this.statements = null;
        this.statements = statements;
        this.answer = answer;
    }
    Choice.prototype.isCorrect = function (response, choiceType) {
        var _this = this;
        switch (choiceType) {
            case 1 /* SINGLE */:
                return this.answer === response;
            case 2 /* MULTI */:
                if (response.length !== this.answer.length)
                    return false;
                return response.every(function (r, i) {
                    return _this.answer.indexOf(r) >= 0;
                });

            case 3 /* TEXT */:
                return this.answer.some(function (elm) {
                    return elm === response;
                });
            default:
                throw new Error();
        }
    };
    return Choice;
})();
// vim:set ft=typescript et sw=2 sts=2 ff=unix:
