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
        try {
            if ($species_name === null) {
                $result = Photo::rawQuery(
                    'SELECT * FROM photo ORDER BY RANDOM() LIMIT :limit OFFSET :offset',
                    ['limit' => $limit, 'offset' => $offset]
                )->findMany();
            } else if ($species_name === 'all') {
                // FIXME Don't use rawQuery at ORM.
                $result = Photo::rawQuery(
                    'SELECT * FROM photo LIMIT :limit OFFSET :offset',
                    ['limit' => $limit, 'offset' => $offset]
                )->findMany();
            } else {
                // FIXME Don't use rawQuery at ORM.
                $result = Photo::rawQuery(
                    'SELECT * FROM photo WHERE LOWER(species_name) LIKE :species_name LIMIT :limit OFFSET :offset',
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
