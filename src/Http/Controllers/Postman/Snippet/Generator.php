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

namespace Diviky\Readme\Http\Controllers\Postman\Snippet;

/**
 * @author sankar <sankar.suda@gmail.com>
 */
class Generator
{
    public static function prepare(array $request = [], $language = null): array
    {
        $options = [
            'indent' => '  ',
        ];

        $request['params'] = self::generateParams($request);

        list($language, $library) = explode('.', $language);
        $library                  = $library ?: 'native';

        $class = 'App\\Component\\Postman\\Snippet\\Targets\\' . ucfirst($language) . '\\' . ucfirst($library);

        $target = new $class();
        $code   = $target->generate($request, $options);

        return [
            'code' => htmlspecialchars($code),
            'info' => $target->info(),
            'id'   => uniqid(),
        ];
    }

    public static function generateParams(array $request): array
    {
        $params  = [];
        $payload = [];

        if (is_array($request['url']['query'])) {
            foreach ($request['url']['query'] as $param) {
                $payload[$param['key']] = $param['value'];
            }
        }

        $params['query'] = http_build_query($payload);

        if (is_array($request['header'])) {
            $params['header'] = array_map(function ($header) {
                return sprintf('\'%s\': "%s"', $header['key'], $header['value']);
            }, $request['header']);
        }

        if (is_array($request['cookies'])) {
            $params['cookies'] = array_map(function ($cookie) {
                return urlencoded($cookie['name']) . '=' . urlencoded($cookie['value']);
            }, $request['cookies']);
        }

        $params['body'] = self::postFields($request['body']);

        return $params;
    }

    public static function postFields($body = []): string
    {
        $mode = $body['mode'];

        if ('raw' == $mode) {
            return $body[$mode];
        }

        if ('urlencoded' == $mode || 'formdata' == $mode) {
            $params = [];
            foreach ($body[$mode] as $data) {
                $params[$data['key']] = $data['value'];
            }

            return http_build_query($params);
        }
    }
}
