<?php
/**
 * /api/photos controller
 */
namespace Ranyuen\Controller;

use \Ranyuen\Model\Photo;

/**
 * /api/photos controller
 */
class ApiPhotos
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
        $result = [];
        if (null === $speciesName) {
            $result = Photo::orderBy('RANDOM()')->take($limit)->get();
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
        $photos = [];
        foreach ($result as $photo) {
            $photos[] = $photo->toArray();
        }

        return $photos;
    }
}
