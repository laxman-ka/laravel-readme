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

namespace Divity\Readme\Http\Controllers\Postman\Snippet\Targets\Java;

use App\Http\Controllers\Postman\Snippet\Builder;
use App\Http\Controllers\Postman\Snippet\Targets\TargetAbstract;

/**
 * @author sankar <sankar.suda@gmail.com>
 */
class Native extends TargetAbstract
{
    public function generate(array $request, array $options = []): string
    {
        $opts = array_merge([], $options);

        $code   = new Builder();
        $params = $request['params'];

        $methods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD'];

        $methodsWithBody = ['POST', 'PUT', 'DELETE', 'PATCH'];

        $code->push('OkHttpClient client = new OkHttpClient();')
            ->blank();

        if ($params['body']) {
            if ($request['boundary']) {
                $code->push(sprintf('MediaType mediaType = MediaType.parse("%s; boundary=%s");', $request['mimeType'], $request['boundary']));
            } else {
                $code->push(sprintf('MediaType mediaType = MediaType.parse("%s");', $request['mimeType']));
            }
            $code->push(sprintf('RequestBody body = RequestBody.create(mediaType, %s);', $params['body']));
        }

        $method = $request['method'];
        $url    = $request['url'];

        $code->push('Request request = new Request.Builder()');
        $code->push(sprintf('.url("%s")', $url['raw']), 1);

        if (!isset($methods[strtoupper($method)])) {
            if ($params['body']) {
                $code->push(sprintf('.method("%s", body)', strtoupper($method)), 1);
            } else {
                $code->push(sprintf('.method("%s", null)', strtoupper($method)), 1);
            }
        } elseif (isset($methodsWithBody[strtoupper($method)])) {
            if ($params['body']) {
                $code->push(sprintf('.%s(body)', strtolower($method)), 1);
            } else {
                $code->push(sprintf('.%s(null)', strtolower($method)), 1);
            }
        } else {
            $code->push(sprintf('.%s()', strtolower($method)), 1);
        }

        // Add headers, including the cookies
        if (count($request['header'])) {
            foreach ($request['header'] as $header) {
                $code->push(sprintf('.addHeader("%s", "%s")', $header['key'], $header['value']), 1);
            }
        }

        $code->push('.build();', 1)
            ->blank()
            ->push('Response response = client.newCall(request).execute();');

        return $code->join();
    }

    public function info(): array
    {
        return [
            'key'     => 'java',
            'title'   => 'Java',
            'link'    => 'http://square.github.io/okhttp/',
            'details' => 'An HTTP Request Client Library',
        ];
    }
}
