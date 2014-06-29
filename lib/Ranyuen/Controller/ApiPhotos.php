<?php
namespace Ranyuen\Controller;

use \Ranyuen\Model\Photo;

class ApiPhotos implements ApiController
{
    public function render($method, array $uri_params, array $request_params)
    {
        $limit = isset($request_params['limit']) ? $request_params['limit'] : 100;
        $offset = isset($request_params['offset']) ? $request_params['offset'] : 0;
        $species_name = isset($request_params['species_name']) ?
            $request_params['species_name'] : null;
        $result = [];
        try {
            if ($species_name === null) {
                $result = Photo::rawQuery(
                    'select * from photo order by random() limit :limit offset :offset',
                    ['limit' => $limit, 'offset' => $offset]
                )->findMany();
            } else if ($species_name === 'all') {
                $result = Photo::rawQuery(
                    'select * from photo limit :limit offset :offset',
                    ['limit' => $limit, 'offset' => $offset]
                )->findMany();
            } else {
                $result = Photo::rawQuery(
                    'select * from photo where lower(species_name) like :species_name limit :limit offset :offset',
                    [
                        'species_name' => '%' . strtolower($species_name) . '%',
                            'limit' => $limit,
                            'offset' => $offset
                        ]
                    )->findMany();
            }
        } catch (PDOException $e) {
        }
        return array_map(function ($photo) { return $photo->asArray(); }, $result);
    }
}
