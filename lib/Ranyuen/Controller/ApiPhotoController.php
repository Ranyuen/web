<?php

/**
 * Ranyuen web site.
 *
 * @author  Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/copyleft/gpl.html GPL-3.0+
 * @link    http://ranyuen.com/
 */

namespace Ranyuen\Controller;

use Ranyuen\Little\Request;
use Ranyuen\Model\Photo;

/**
 * /api/photo controller.
 */
class ApiPhotoController extends Controller
{
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
     * @Route('/api/photos')
     */
    public function photos(Request $req, $offset = 0, $limit = 0)
    {
        $speciesName = $req->get('species_name');
        $color       = $req->get('color');
        if (is_null($speciesName)) {
            $photos = Photo::getNewPhotos();
        } else {
            if (is_null($color)) {
                $photos = Photo::getPhotosBySpeciesName($speciesName);
            } else {
                $photos = Photo::getPhotosBySpeciesNameAndColor($speciesName, $color);
            }
        }

        return $this->toJsonResponse($photos->toArray());
    }

    /**
     * Echo a photo.
     *
     * @param string $uuid     Photo UUID.
     * @param int    $width  Photo pixel width.
     * @param int    $height Photo pixel height.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @Route('/api/photo')
     */
    public function photo($uuid, $width = null, $height = null)
    {
        if (!$uuid) {
            return $this->toJsonResponse(['error' => 'id is required.'], 404);
        }
        if (!($width || $height)) {
            return $this->toJsonResponse(['error' => 'width xor height is required.'], 404);
        }
        $photo = Photo::find($uuid);
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
