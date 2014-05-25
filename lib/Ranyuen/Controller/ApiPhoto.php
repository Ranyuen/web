<?php
namespace Ranyuen\Controller;

class ApiPhoto 
{
	public function render($method, array $uri_params, array $request_params)
    {
    	if (!isset($request_params['id'])) {
    		return [ 'error' => 'id is required.', 'status' => 404 ];
    	}
		$photo_path = 'Calanthe/gallery/' . $request_params['id'] . '.jpg';
		list($width, $height) = getimagesize($photo_path);
		$new_width = 349;
		$new_height = $height * ($new_width / $width);
		$color_r = imagecreatetruecolor($new_width, $new_height);
		$image = imagecreatefromjpeg($photo_path);
		imagecopyresampled($color_r, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		header('Content-Type: image/jpeg');
		imagejpeg($color_r, null, 100);
	}
}