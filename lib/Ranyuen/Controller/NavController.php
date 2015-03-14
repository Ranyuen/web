<?php
/**
 * Ranyuen web site.
 */

namespace Ranyuen\Controller;

use Ranyuen\App;
use Ranyuen\FrozenResponse;
use Ranyuen\Little\Request;
use Ranyuen\Little\Response;

/**
 * Static pages.
 */
class NavController extends Controller
{
    /**
     * @var Ranyuen\Template\ViewRenderer
     * @Inject
     */
    private $renderer;

    /**
     * @param App     $app  Application.
     * @param Request $req  HTTP request.
     * @param string  $lang Request lang.
     *
     * @return string
     *
     * @Route('/photos/')
     */
    public function photos(App $app, Request $req, $lang)
    {
        $controller = $app->container
            ->newInstance('Ranyuen\Controller\ApiPhotoController');
        $speciesName = $req->get('species_name');
        $photos = $controller->photos($req, 0, 20);
        $photos = array_map(
            function ($photo) {
                $thumbWidth = 349;
                $thumbHeight = floor($photo['height'] * $thumbWidth / $photo['width']);
                $photo['thumb_width']  = $thumbWidth;
                $photo['thumb_height'] = $thumbHeight;

                return $photo;
            },
            json_decode($photos->getContent(), true)
        );

        return $this->render(
            $lang,
            '/photos/',
            [
                'species_name' => $speciesName,
                'photos'       => $photos,
            ]
        );
    }

    /**
     * @param Request $req  HTTP request.
     * @param string  $lang Language.
     *
     * @return string
     *
     * @Route(error=404)
     */
    public function notFound(Request $req, $lang)
    {
        foreach ($this->config['redirect'] as $from => $to) {
            if ($req->getRequestUri() === $from) {
                return new Response('', 301, ['Location' => $to]);
            }
        }

        return $this->render($lang, $req->getPathInfo());
    }

    private function render($lang, $templateName, array $params = [])
    {
        $params = array_merge(
            $params,
            $this->getDefaultParams($lang, $templateName)
        );
        if ('/' === $templateName[strlen($templateName) - 1]) {
            $templateName .= 'index';
        }
        $res = $this->renderer->render("$templateName.$lang", $params);
        if (false === $res) {
            $res = $this->renderer->render("error404.$lang", $params);

            return new Response($res, 404);
        }

        return new FrozenResponse($res, 200);
    }
}
