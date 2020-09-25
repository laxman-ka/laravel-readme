<?php

declare (strict_types = 1);

/*
 * This file is part of the Speedwork package.
 *
 * (c) Sankar <sankar.suda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Diviky\Readme\Http\Controllers\Postman\Snippet\Targets\Python;

use App\Http\Controllers\Postman\Snippet\Builder;
use App\Http\Controllers\Postman\Snippet\Targets\TargetAbstract;

/**
 * @author sankar <sankar.suda@gmail.com>
 */
class Requests extends TargetAbstract
{
    public function generate(array $request, array $options = []): string
    {
        $opts = array_merge([], $options);

        $code = new Builder();

        $url    = $request['url'];
        $params = $request['params'];

        $uri = $url['protocol'] . '://' . implode('.', $url['host']) . '/' . implode('/', $url['path']);

        // Import requests
        $code->push('import requests')
            ->blank();

        // Set URL
        $code->push('url = "%s"', $uri)
            ->blank();

        // Construct query string
        if ($params['query']) {
            $qs = 'querystring = ' . $params['query'];

            $code->push($qs)
                ->blank();
        }

        // Construct payload
        $payload = $params['body'];

        if ($payload) {
            $code->push('payload = "%s"', $payload);
        }

        $headerCount = count($request['header']);
        // Construct headers
        $code->push('headers = {')
            ->push(implode(',\n' . $opts['indent'] . $opts['indent'], $params['header']), 1)
            ->push('),')
            ->blank();

        // Construct request
        $req = sprintf('response = requests.request("%s", url', $request['method']);

        if ($payload) {
            $req .= ', data=payload';
        }

        if ($headerCount > 0) {
            $req .= ', headers=headers';
        }

        if ($qs) {
            $req .= ', params=querystring';
        }

        $req .= ')';

        $code->push($req)
            ->blank();

        // Print response
        $code->push('print(response.text)');

        return $code->join();
    }

    public function info(): array
    {
        return [
            'key'     => 'python',
            'title'   => 'Python Requests',
            'link'    => 'http://docs.python-requests.org/en/latest/api/#requests.request',
            'details' => 'Requests HTTP library',
        ];
    }
}
