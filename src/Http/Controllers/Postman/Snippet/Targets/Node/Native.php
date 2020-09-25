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

namespace Diviky\Readme\Http\Controllers\Postman\Snippet\Targets\Node;

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

        $url    = $request['url'];
        $params = $request['params'];

        $reqOpts = [
            'method'   => $request['method'],
            'hostname' => $url['host'],
            'port'     => $url['port'],
            'path '    => $url['path'],
            'headers'  => $params['header'] ?: [],
        ];

        $code->push('var http = require("%s");', str_replace(':', '', $url['protocol']));

        $code->blank()
            ->push(sprintf('var options = %s;', json_encode($reqOpts, JSON_PRETTY_PRINT)))
            ->blank()
            ->push('var req = http.request(options, function (res) {')
            ->push('var chunks = [];', 1)
            ->blank()
            ->push('res.on("data", function (chunk) {', 1)
            ->push('chunks->push(chunk);', 2)
            ->push('});', 1)
            ->blank()
            ->push('res.on("end", function () {', 1)
            ->push('var body = Buffer.concat(chunks);', 2)
            ->push('console.log(body.toString());', 2)
            ->push('});', 1)
            ->push('});')
            ->blank();

        $code->push(sprintf('req.write(%s);', $params['body']));

        $code->push('req.end();');

        return $code->join();
    }

    public function info(): array
    {
        return [
            'key'     => 'node',
            'title'   => 'Node',
            'link'    => 'http://nodejs.org/api/http.html#http_http_request_options_callback',
            'details' => 'Node.js native HTTP interface',
        ];
    }
}
