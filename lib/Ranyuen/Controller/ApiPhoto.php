<?php
namespace Ranyuen\Controller;

class ApiPhoto 
{
	public function render($method, array $uri_params, array $request_params)
    {
    	if (!isset($request_params['id'])) {
    		return [ 'error' => 'id is required.', 'status' => 404 ];
    	}
		$filename = 'Calanthe/gallery/' . $request_params['id'] . '.jpg';
		list($width, $height) = getimagesize($filename);
		$new_width = 349;
		$new_height = $height * ($new_width / $width);
		$image_p = imagecreatetruecolor($new_width, $new_height);
		$image = imagecreatefromjpeg($filename);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		header('Content-Type: image/jpeg');

		return imagejpeg($image_p, null, 100);
	}
}