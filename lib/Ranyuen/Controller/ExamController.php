<?php

/**
 * Ranyuen web site.
 *
 * @author  Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/copyleft/gpl.html GPL-3.0+
 * @link    http://ranyuen.com/
 */

namespace Ranyuen\Controller;

use Ranyuen\Template\MainViewRenderer;
use Ranyuen\Template\ViewRenderer;
use Ranyuen\Model\ExamQuestion;
use Ranyuen\Model\ExamAnswer;
use Ranyuen\Model\ExamResult;
use Ranyuen\Model\Article;
use Ranyuen\Little\Request;
use Ranyuen\Little\Response;
use Ranyuen\Little\Router;
use Illuminate\Database\Eloquent;
use Illuminate\Database\Query;
// use Illuminate\Database\Query as Query;
/**
 *  Exam.
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class ExamController extends Controller
{
    /**
     * Renderer.
     *
     * @var Ranyuen\Template\ViewRenderer
     *
     * @Inject
     */
    protected $renderer;

    /**
     * Navigation.
     *
     * @var Ranyuen\Navigation
     *
     * @Inject
     */
    protected $nav;

    /**
     * BgImage.
     *
     * @var Ranyuen\BgImage
     *
     * @Inject
     */
    protected $bgimage;

    /**
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @Route('/play/exam/')
     */
    public function index() {
        $renderer = new MainViewRenderer($this->renderer, $this->nav, $this->bgimage, $this->config);
        $params   = $renderer->defaultParams('ja', '/play/exam/');
        $types    = ['easy', 'hard', 'expert', 'photo'];
        foreach ($types as $type) {
            $subQuery = ExamResult::selectRaw("user_name, max(points) as max_points")
                ->where('created_at', '>', '2021-12-28 23:00:00')
                ->where('type', $type)
                ->where('created_at', '>', '2021-12-28 23:00:00')
                ->groupBy('user_name');
            $query = ExamResult::selectRaw("user_name, points, created_at")
                            ->joinSub($subQuery, 'sub', function($join) {
                                $join->on('sub.user_name', '=', 'exam_result.user_name');
                                $join->on('sub.max_points', '=', 'exam_result.points');
                            })
                            ->orderBy('points', 'desc')
                            ->orderBy('created_at', 'asc')
                            // ->where('type', $type)
                            // ->where('created_at', '>', '2021-12-28 23:00:00')
                            // ->groupBy('user_name')
                            ->take(20)
                            ->get();
            ;
            $params[$type] = $query;
        }

        $article = Article::findByPath('/play/exam/');
        $content = $article->getContent('ja');

        return $renderer->renderContent($content->content, $params);
    }

    /**
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @Route('/play/exam/:type')
     */
    public function examQuestions($type)
    {
        $renderer = new MainViewRenderer($this->renderer, $this->nav, $this->bgimage, $this->config);
        $params   = $renderer->defaultParams('ja', '/play/exam/' . $type);
        $exam     = new ExamQuestion();
        $orderBy  = (($exam->getConnection()->getConfig('driver')) == 'sqlite') == 'sqlite' ? 'RANDOM()' : 'RAND()';
        $questions = ExamQuestion::where('type', $type)
                                    ->orderByRaw($orderBy)
                                    ->take(100)
                                    ->get();
        foreach ($questions as $question) {
            $question['answers'] = ExamAnswer::orderByRaw($orderBy)
                                    ->where('question_id', $question->id)
                                    ->get();
        }

        $params['questions'] = $questions;

        $article = Article::findByPath('/play/exam/' . $type);
        $content = $article->getContent('ja');

        return $renderer->renderContent($content->content, $params);
    }

    /**
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @Route('/play/exam/:type/:id')
     */
    public function practiceQuestions($type, $id)
    {
        $renderer = new MainViewRenderer($this->renderer, $this->nav, $this->bgimage, $this->config);
        $params   = $renderer->defaultParams('ja', '/play/exam/' . $type);
        $exam     = new ExamQuestion();
        $orderBy = (($exam->getConnection()->getConfig('driver')) == 'sqlite') == 'sqlite' ? 'RANDOM()' : 'RAND()';

        if ($id !== 0) {
            $questions = ExamQuestion::where('type', $type)
                                    ->skip($id === '1' ? 0 : (50 * ($id - 1)))
                                    ->take(25)
                                    ->orderByRaw($orderBy)
                                    ->get();
        } else {
            $questions = ExamQuestion::where('type', $type)
                                    ->take(25)
                                    ->orderByRaw($orderBy)
                                    ->get();
        }

        foreach ($questions as $question) {
            $question['answers'] = ExamAnswer::orderByRaw($orderBy)
                                    ->where('question_id', $question->id)
                                    ->get();
        }

        $params['questions'] = $questions;
        $params['type'] = $type;

        $article = Article::findByPath('/play/exam/' . $type . '/practice');
        $content = $article->getContent('ja');

        return $renderer->renderContent($content->content, $params);
    }

    /**
     * [registerResult description]
     * @Route('/play/exam', via=POST)
     */
    public function registerResult() {
        if (isset($_POST['exam']) && !empty($_POST['exam']['userName'])) {
            $result = $_POST['exam'];
            $examResult            = new ExamResult;
            $examResult->user_name = htmlspecialchars($result['userName']);
            $examResult->passwd    = htmlspecialchars($result['passWd']);
            $examResult->points    = $result['correctsNumber'];
            $examResult->type      = $result['type'];
            $examResult->save();

            return true;
        }

        return false;
    }
}
