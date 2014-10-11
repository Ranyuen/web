<?php
namespace Ranyuen\Controller;

use \Ranyuen\Model\Photo;

class ApiPhoto
{
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
        if ($width && $height) {
            $_height = floor($photo->height * $width / $photo->width);
            $_width = floor($photo->width * $height / $photo->height);
            $height = min($_height, $height);
            $width = min($_width, $width);
        } elseif ($width) {
            $height = floor($photo->height * $width / $photo->width);
        } else {
            $width = floor($photo->width * $height / $photo->height);
        }
        $photo->renderResized($width, $height);
    }
}
