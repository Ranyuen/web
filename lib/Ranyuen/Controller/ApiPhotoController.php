<?php
/**
 * Ranyuen web site
 */

namespace Ranyuen\Controller;

use Ranyuen\Little\Request;
use Ranyuen\Model\Photo;

/**
 * /api/photo controller
 *
 * @Route('/api')
 */
class ApiPhotoController extends Controller
{
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @Route('/photos')
     */
    public function photos(Request $req, $offset = 0, $limit = 100)
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
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @Route('/photo')
     */
    public function photo($id, $width = null, $height = null)
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
