<?php
namespace Ranyuen\Controller;

class ApiPhoto
{
    public function render($method, array $uri_params, array $request_params)
    {
        if (!isset($request_params['id'])) {
            return [ 'error' => 'id is required.', 'status' => 404 ];
        }
        $filename = "Calanthe/gallery/{$request_params['id']}.jpg";
        $image = imagecreatefromjpeg($filename);
        list($width, $height) = getimagesize($filename);
        $mini_width = 349;
        $mini_height = floor($height * $mini_width / $width);
        $mini_image = imagecreatetruecolor($mini_width, $mini_height);
        imagecopyresampled($mini_image, $image,
            0, 0, 0, 0,
            $mini_width, $mini_height, $width, $height);
        imagedestroy($image);
        header('Content-Type: image/jpeg');
        imagejpeg($mini_image, null, 95);
        imagedestroy($mini_image);
    }
}
