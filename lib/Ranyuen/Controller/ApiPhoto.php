<?php
namespace Ranyuen\Controller;

use \Ranyuen\Model\Photo;

class ApiPhoto
{
    public function render($method, array $uri_params, array $request_params)
    {
        if (!isset($request_params['id'])) {
            return [ 'error' => ':id is required.', 'status' => 404 ];
        }
        $id = $request_params['id'];
        $width = isset($request_params['width']) ? $request_params['width'] : null;
        $height = isset($request_params['height']) ?
            $request_params['height'] :
            null;
        if (!($width || $height)) {
            return [ 'error' => ':width or :height is required.', 'status' => 404 ];
        }
        $photo = Photo::create([ 'id' => $id ]);
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
