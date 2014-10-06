<?php
namespace Ranyuen\Controller;

use \Ranyuen\Model\Photo;

class ApiPhotos implements ApiController
{
    public function render($method, array $uri_params, array $request_params)
    {
        $limit = isset($request_params['limit']) ? $request_params['limit'] : 100;
        $offset = isset($request_params['offset']) ? $request_params['offset'] : 0;
        $species_name = isset($request_params['species_name']) && $request_params['species_name'] ?
            $request_params['species_name'] : null;
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
