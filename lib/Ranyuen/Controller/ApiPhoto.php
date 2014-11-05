<?php
/**
 * Ranyuen web site
 */
namespace Ranyuen\Controller;

use \Ranyuen\Model\Photo;

/**
 * /api/photo controller
 */
class ApiPhoto
{
    /**
     * @param array $params Request params
     *
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function get(array $params)
    {
        if (!isset($params['id'])) {
            return [ 'error' => ':id is required.', 'status' => 404 ];
        }
        $id = $params['id'];
        $width = isset($params['width']) ? $params['width'] : null;
        $height = isset($params['height']) ?
            $params['height'] :
            null;
        if (!($width || $height)) {
            return [ 'error' => ':width or :height is required.', 'status' => 404 ];
        }
        $photo = Photo::find($id);
        $photo->loadImageSize();
        list($width, $height) = $this->calcSize($photo, $width, $height);
        $photo->renderResized($width, $height);
    }

    private function calcSize($photo, $newWidth, $newHeight)
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
