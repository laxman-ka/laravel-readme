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

namespace Divity\Readme\Http\Controllers\Postman\Snippet\Targets\Python;

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

        $code = new Builder();

        $params = $request['params'];
        $url    = $request['url'];

        // Start Request
        $code->push('import http.client')
            ->blank();

        // Check which protocol to be used for the client connection
        $host     = implode('.', $url['host']);
        $protocol = $url['protocol'];

        if ('https:' === $protocol) {
            $code->push(sprintf('conn = http.client.HTTPSConnection("%s")', $host))
                ->blank();
        } else {
            $code->push(sprintf('conn = http.client.HTTPConnection("%s")', $host))
                ->blank();
        }

        // Create payload string if it exists
        if ($params['query']) {
            $code->push(sprintf('payload = "%s"', $params['query']))
                ->blank();
        }

        $headerCount = count($request['header']);

        if ($headerCount) {
            $code->push('headers = {');
            $code->push(implode(",\n", $params['header']), 1);
            $code->push('}')->blank();
        }

        // Make Request
        $method = $request['method'];
        $path   = implode('/', $url['path']);
        if ($payload && $headerCount) {
            $code->push(sprintf('conn.request("%s", "%s", payload, headers)', $method, $path));
        } elseif ($payload && !$headerCount) {
            $code->push(sprintf('conn.request("%s", "%s", payload)', $method, $path));
        } elseif (!$payload && $headerCount) {
            $code->push(sprintf('conn.request("%s", "%s", headers=headers)', $method, $path));
        } else {
            $code->push(sprintf('conn.request("%s", "%s")', $method, $path));
        }

        // Get Response
        $code->blank()
            ->push('res = conn.getresponse()')
            ->push('data = res.read()')
            ->blank()
            ->push('print(data.decode("utf-8"))');

        return $code->join();
    }

    public function info(): array
    {
        return [
            'key'     => 'python',
            'title'   => 'Python3',
            'link'    => 'https://docs.python.org/3/library/http.client.html',
            'details' => 'Python3 HTTP Client',
        ];
    }
}
