/**
 * 複数のQuestionを持つ。
 */
class Exam {
  name = '';
  questions: Question[] = [];

  constructor(name: string, questions: Question[]) {
    this.name = name;
    this.questions = questions;
  }

  getScore(): number {
    return this.questions.reduce(
      (acm, q) => { return acm + (q.isCorrect() ? 1 : 0); },
      0) * 10;
  }
}

/**
 * 複数の選択肢、userからの返答を持つ。
 */
class Question {
  id = 0;
  statement = '';
  choices: Choice[];
  choiceType: ChoiceType;
  actualChoice: Choice;
  response: any;

  constructor(id: number, statement: string, choices: Choice[],
              choiceType: ChoiceType) {
    this.id = id;
    this.statement = statement;
    this.choices = choices;
    this.choiceType = choiceType;
  }

  isCorrect(): bool {
    return this.actualChoice.isCorrect(this.response, this.choiceType);
  }
}

enum ChoiceType {
  /** 他選択肢・一選択問題: 選択肢は複数文字列、正解は単一数値 */
  SINGLE = 1,
  /** 他選択肢・多選択問題: 選択肢は複数文字列、正解は複数数値 */
  MULTI,
  /** 一文字列回答問題    : 選択肢はnull、      正解は複数文字列 */
  TEXT
}

/**
 * 選択肢、正解を持つ。
 */
class Choice {
  statements: string[] = null;
  answer: any;

  constructor(statements: string[], answer: any) {
    this.statements = statements;
    this.answer = answer;
  }

  isCorrect(response: any, choiceType: ChoiceType): bool {
    switch (choiceType) {
      case ChoiceType.SINGLE:
        return this.answer === response;
      case ChoiceType.MULTI:
        if (response.length !== this.answer.length) return false;
        return response.every((r, i) => this.answer.indexOf(r) >= 0);
        // for (var i = 0, iz = response.length; i < iz; ++i) {
        //   if (response[i] !== this.answer[i]) return false;
        // }
        // return true;
      case ChoiceType.TEXT:
        return this.answer.some((elm) => elm === response);
      default:
        throw new Error();
    }
  }
}
// vim:set ft=typescript et sw=2 sts=2 ff=unix:
