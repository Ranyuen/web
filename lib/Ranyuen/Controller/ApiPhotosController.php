<?php
/**
 * Ranyuen web site
 */
namespace Ranyuen\Controller;

use \Ranyuen\Model\Photo;

/**
 * /api/photos controller
 */
class ApiPhotosController extends ApiController
{
    /**
     * @param array $params Request params
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function get(array $params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : 100;
        $offset = isset($params['offset']) ? $params['offset'] : 0;
        $speciesName = isset($params['species_name']) && $params['species_name'] ?
            $params['species_name'] :
            null;

        return $this->getPhotos($limit, $offset, $speciesName)->toArray();
    }

    private function getPhotos($limit, $offset, $speciesName)
    {
        $result = [];
        if (null === $speciesName) {
            $result = $this->getRandomPhotos($limit, $offset);
        } elseif ('all' === $speciesName) {
            $result = Photo::skip($offset)->take($limit)->get();
        } elseif ('others' === $speciesName) {
            $result = Photo::whereNull('species_name')
                ->skip($offset)
                ->take($limit)
                ->get();
        } else {
            $result = Photo::whereRaw('LOWER(species_name) LIKE ?', ['%'.strtolower($speciesName).'%'])
                ->skip($offset)
                ->take($limit)
                ->get();
        }

        return $result;
    }

    private function getRandomPhotos($limit, $offset)
    {
        switch ($this->config['mode']) {
            case 'development':
                return Photo::orderByRaw('RANDOM()')->take($limit)->get();
            case 'production':
                return Photo::orderByRaw('RAND()')->take($limit)->get();
            default:
                return Photo::skip($offset)->take($limit)->get();
        }
    }
}
