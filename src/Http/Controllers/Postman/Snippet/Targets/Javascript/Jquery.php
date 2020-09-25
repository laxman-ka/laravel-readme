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

namespace Diviky\Readme\Http\Controllers\Postman\Snippet\Targets\Javascript;

use App\Http\Controllers\Postman\Snippet\Builder;
use App\Http\Controllers\Postman\Snippet\Targets\TargetAbstract;

/**
 * @author sankar <sankar.suda@gmail.com>
 */
class Jquery extends TargetAbstract
{
    public function generate(array $request, array $options = []): string
    {
        $opts = array_merge([
            'cors' => true,
        ], $options);

        $code = new Builder();

        $url    = $request['url'];
        $body   = $request['body'];
        $params = $request['params'];

        $settings = [
            'async'       => true,
            'crossDomain' => true,
            'url'         => $url['raw'],
            'method'      => $request['method'],
            'headers'     => $params['header'],
        ];

        switch ($body['mode']) {
            case 'formdata':
                $code->push('var form = new FormData();');

                foreach ($body['formdata'] as $param) {
                    $code->push(sprintf('form.append("%s", "%s");', json_encode($param['key']), json_encode($param['value'])));
                }

                $settings['processData'] = false;
                $settings['contentType'] = false;
                $settings['mimeType']    = 'multipart/form-data';
                $settings['data']        = '[form]';

                $code->blank();
                break;

            default:
                if ($params['body']) {
                    $settings['data'] = $params['body'];
                }
        }

        $code->push('var settings = ' . str_replace('"[form]"', 'form', json_encode($settings, JSON_PRETTY_PRINT)))
            ->blank()
            ->push('$.ajax(settings).done(function (response) {')
            ->push('console.log(response);', 1)
            ->push('});');

        return $code->join();
    }

    public function info(): array
    {
        return [
            'key'     => 'javascript',
            'title'   => 'jQuery',
            'link'    => 'http://api.jquery.com/jquery.ajax/',
            'details' => 'Perform an asynchronous HTTP (Ajax) requests with jQuery',
        ];
    }
}
