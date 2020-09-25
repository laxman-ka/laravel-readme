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

namespace Diviky\Readme\Http\Controllers\Postman\Snippet\Targets\Ruby;

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

        $code->push('require \'uri\'')
            ->push('require \'net/http\'')
            ->blank();

        // To support custom methods we check for the supported methods
        // and if doesn't exist then we build a custom class for it
        $method  = strtoupper($request['method']);
        $methods = ['GET', 'POST', 'HEAD', 'DELETE', 'PATCH', 'PUT', 'OPTIONS', 'COPY', 'LOCK', 'UNLOCK', 'MOVE', 'TRACE'];

        if (isset($methods[$method])) {
            $code->push(sprintf('class Net::HTTP::%s < Net::HTTPRequest', ucfirst(strtolower($method))))
                ->push(sprintf('  METHOD = \'%s\'', $method))
                ->push(sprintf('  REQUEST_HAS_BODY = \'%s\'', $params['body'] ? 'true' : 'false'))
                ->push(sprintf('  RESPONSE_HAS_BODY = true'))
                ->push('end')
                ->blank();
        }

        $url = $request['url'];

        $code->push(sprintf('url = URI("%s")', $url['raw']))
            ->blank()
            ->push('http = Net::HTTP.new(url.host, url.port)');

        if ('https:' === $url['protocol']) {
            $code->push('http.use_ssl = true')
                ->push('http.verify_mode = OpenSSL::SSL::VERIFY_NONE');
        }

        $code->blank()
            ->push('request = Net::HTTP::%s.new(url)', ucfirst(strtolower($method)));

        if (count($request['header'])) {
            foreach ($request['header'] as $header) {
                $code->push(sprintf('request["%s"] = \'%s\'', $header['key'], $header['value']));
            }
        }

        if ($params['body']) {
            $code->push(sprintf('request.body = %s', $params['body']));
        }

        $code->blank()
            ->push('response = http.request(request)')
            ->push('puts response.read_body');

        return $code->join();
    }

    public function info(): array
    {
        return [
            'key'     => 'Ruby',
            'title'   => 'Ruby',
            'link'    => 'http://ruby-doc.org/stdlib-2.2.1/libdoc/net/http/rdoc/Net/HTTP.html',
            'details' => 'Ruby HTTP client',
        ];
    }
}
