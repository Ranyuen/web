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
        $params = $renderer->defaultParams('ja', '/play/exam/');
        $types = ['easy', 'hard', 'expert'];
        foreach ($types as $type) {
            $query = ExamResult::selectRaw("user_name, max(points) as points, created_at")
                            ->orderBy('points', 'desc')
                            ->where('type', $type)
                            ->groupBy('user_name')
                            ->take(10)
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
        $params = $renderer->defaultParams('ja', '/play/exam/' . $type);

        $questions = ExamQuestion::where('type', $type)
                                    ->orderByRaw("RAND()")
                                    ->take(100)
                                    ->get();
        foreach ($questions as $question) {
            $question['answers'] = ExamAnswer::orderByRaw("RAND()")
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
        $params = $renderer->defaultParams('ja', '/play/exam/' . $type);

        if ($id === 0) {
            $questions = ExamQuestion::where('type', $type)
                                    ->skip($id === '1' ? 0 : (50 * ($id - 1)))
                                    ->take(25)
                                    ->orderByRaw("RAND()")
                                    ->get();
        } else {
            $questions = ExamQuestion::where('type', $type)
                                    ->take(25)
                                    ->orderByRaw("RAND()")
                                    ->get();
        }

        foreach ($questions as $question) {
            $question['answers'] = ExamAnswer::orderByRaw("RAND()")
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
            var_dump($result);
            $examResult            = new ExamResult;
            $examResult->user_name = htmlspecialchars($result['userName']);
            $examResult->points    = $result['correctsNumber'];
            $examResult->type      = $result['type'];
            $examResult->save();

            return true;
        }

        return false;
    }
}
