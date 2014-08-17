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
                $result = Photo::order_by_expr('RANDOM()')
                    ->limit($limit)
                    ->findMany();
            } elseif ($species_name === 'all') {
                $result = Photo::offset($offset)->limit($limit)->findMany();
            } else if ($species_name === 'others') {
                $result = Photo::where_raw('species_name is null')
                    ->offset($offset)
                    ->limit($limit)
                    ->findMany();
            } else {
                $result = Photo::where_raw('LOWER(species_name) LIKE ?', '%' . strtolower($species_name) . '%')
                    ->offset($offset)
                    ->limit($limit)
                    ->findMany();
            }
        } catch (PDOException $e) {
        }

        return array_map(function ($photo) { return $photo->asArray(); }, $result);
    }
}
