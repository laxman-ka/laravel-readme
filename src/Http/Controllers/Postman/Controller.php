<?php

/*
 * This file is part of the Speedwork package.
 *
 * (c) Sankar <sankar.suda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Diviky\Readme\Http\Controllers\Postman;

use App\Http\Controllers\Controller as BaseController;

/**
 * @author sankar <sankar.suda@gmail.com>
 */
class Controller extends BaseController
{
    public function index()
    {
        $service = new Service();

        $path       = resource_path('docs') . '/collection.json';
        $collection = $this->get('files')->get($path);

        $collection = json_decode($collection, true);

        $indexes    = $service->generateIndexes($collection);
        $collection = $service->modifyCollection($collection);

        $collection['info']['description'] = $service->parse($collection['info']['description']);

        return [
            'indexes'    => $indexes,
            'collection' => $collection,
        ];
    }
}
