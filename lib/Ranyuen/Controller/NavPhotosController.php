<?php
namespace Ranyuen\Controller;

class NavPhotosController extends NavController
{
    public function showFromTemplate($lang, $path = 'photos/index')
    {
        parent::showFromTemplate($lang, $path);
    }

    protected function render($lang, $template_name, $params = [])
    {
        $controller = new \Ranyuen\Controller\ApiPhotos();
        $species_name = isset($_GET['species_name']) ?
            $_GET['species_name'] :
            null;
        $photos = $controller->get([
          'species_name' => $species_name,
          'limit'        => 20,
        ]);
        $photos = array_map(function ($photo) {
          $thumb_width = 349;
          $thumb_height =
              floor($photo['height'] * $thumb_width / $photo['width']);
          $photo['thumb_width'] = $thumb_width;
          $photo['thumb_height'] = $thumb_height;

          return $photo;
        }, $photos);
        parent::render($lang, $template_name, [
            'species_name' => $species_name,
            'photos'       => $photos,
        ]);
    }
}
