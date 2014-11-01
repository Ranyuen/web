<?php
/**
 * Static page.
 */
namespace Ranyuen\Controller;

/**
 * Static page.
 */
class NavPhotosController extends NavController
{
    /**
     * @param string $lang Current lang
     * @param string $path URI path
     *
     * @return void
     */
    public function showFromTemplate($lang, $path = 'photos/index')
    {
        $path = 'photos/index';
        parent::showFromTemplate($lang, $path);
    }

    /**
     * Echo rendered string.
     *
     * @param string $lang         Current lang
     * @param string $templateName Template name
     * @param array  $params       Template params
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function render($lang, $templateName, $params = [])
    {
        $params = [];
        $controller = new \Ranyuen\Controller\ApiPhotos();
        $speciesName = isset($_GET['species_name']) ?
            $_GET['species_name'] :
            null;
        $photos = $controller->get(
            [
                'species_name' => $speciesName,
                'limit'        => 20,
            ]
        );
        $photos = array_map(
            function ($photo) {
                $thumbWidth = 349;
                $thumbHeight = floor($photo['height'] * $thumbWidth / $photo['width']);
                $photo['thumb_width'] = $thumbWidth;
                $photo['thumb_height'] = $thumbHeight;

                return $photo;
            },
            $photos
        );
        parent::render(
            $lang,
            $templateName,
            [
                'species_name' => $speciesName,
                'photos'       => $photos,
            ]
        );
    }
}
