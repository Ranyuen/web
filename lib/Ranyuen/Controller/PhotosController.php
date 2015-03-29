<?php

/**
 * Ranyuen web site.
 *
 * @author  Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/copyleft/gpl.html GPL-3.0+
 * @link    http://ranyuen.com/
 */

namespace Ranyuen\Controller;

use Ranyuen\App;
use Ranyuen\Little\Request;
use Ranyuen\Model\Photo;
use Ranyuen\Template\MainViewRenderer;
use Ranyuen\Template\ViewRenderer;

/**
 * /photos controller.
 *
 * @Route('/photos')
 */
class PhotosController extends Controller
{
    /**
     * @Route('/')
     */
    public function index(App $app, Request $req, $lang, ViewRenderer $renderer, $nav, $bgimage, $config) {
        $controller = $app->container->newInstance('Ranyuen\Controller\PhotosController');
        $speciesName = $req->get('species_name');
        $photos = $controller->indexJson($req, 0, 20);
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
        $renderer = new MainViewRenderer($renderer, $nav, $bgimage, $config);
        $params = $renderer->defaultParams($lang, $req->getPathInfo());
        $params['species_name'] = $speciesName;
        $params['photos']       = $photos;

        return $renderer->render("photos/index.$lang", $params);
    }

    /**
     * List photos.
     *
     * @param Request $req    HTTP request.
     * @param int     $offset Offset.
     * @param int     $limit  Limit.
     *
     * @return Response
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @Route('/list.json')
     */
    public function indexJson(Request $req, $offset = 0, $limit = 100)
    {
        $speciesName = $req->get('species_name');
        if (is_null($speciesName)) {
            $photos = Photo::getRandomPhotos($offset, $limit);
        } else {
            $photos = Photo::getPhotosBySpeciesName($speciesName, $offset, $limit);
        }

        return $this->toJsonResponse($photos->toArray());
    }

    /**
     * Echo a photo.
     *
     * @param string $id     Photo ID.
     * @param int    $width  Photo pixel width.
     * @param int    $height Photo pixel height.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @Route('/show.jpg')
     */
    public function showImage($id, $width = null, $height = null)
    {
        if (!$id) {
            return $this->toJsonResponse(['error' => 'id is required.'], 404);
        }
        if (!($width || $height)) {
            return $this->toJsonResponse(['error' => 'width xor height is required.'], 404);
        }
        $photo = Photo::find($id);
        $photo->loadImageSize();
        list($width, $height) = $this->calcSize($photo, $width, $height);
        $photo->renderResized($width, $height);
    }

    private function calcSize(Photo $photo, $newWidth, $newHeight)
    {
        if ($newWidth && $newHeight) {
            $newHeight = min(
                floor($photo->height * $newWidth / $photo->width),
                $newHeight
            );
            $newWidth = min(
                floor($photo->width * $newHeight / $photo->height),
                $newWidth
            );
        } elseif ($newWidth) {
            $newHeight = floor($photo->height * $newWidth / $photo->width);
        } else {
            $newWidth = floor($photo->width * $newHeight / $photo->height);
        }

        return [$newWidth, $newHeight];
    }
}
