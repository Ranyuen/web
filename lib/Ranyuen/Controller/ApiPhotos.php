<?php
namespace Ranyuen\Controller;

use \Ranyuen\Model\Photo;

class ApiPhotos extends ApiController
{
    public function get(array $params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : 100;
        $offset = isset($params['offset']) ? $params['offset'] : 0;
        $species_name = isset($params['species_name']) && $params['species_name'] ?
            $params['species_name'] :
            null;
        $result = [];
        if ($species_name === null) {
            $result = Photo::orderBy('RANDOM()')->take($limit)->get();
        } elseif ($species_name === 'all') {
            $result = Photo::skip($offset)->take($limit)->get();
        } elseif ($species_name === 'others') {
            $result = Photo::whereNull('species_name')->skip($offset)->take($limit)->get();
        } else {
            $result = Photo::whereRaw('LOWER(species_name) LIKE ?', ['%' . strtolower($species_name) . '%'])->skip($offset)->take($limit)->get();
        }
        $photos = [];
        foreach ($result as $photo) {
            $photos[] = $photo->toArray();
        }

        return $photos;
    }
}
