<?php
/**
 * Ranyuen web site
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
     */
    protected function render($lang, $templateName, $params = [])
    {
        $params = [];
        $controller = $this->router->getContainer()
            ->newInstance('Ranyuen\Controller\ApiPhotosController');
        $speciesName = $this->router->request->get('species_name');
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
