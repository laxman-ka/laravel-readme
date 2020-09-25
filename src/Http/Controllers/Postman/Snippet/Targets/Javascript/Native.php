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
class Native extends TargetAbstract
{
    public function generate(array $request, array $options = []): string
    {
        $opts = array_merge([
            'cors' => true,
        ], $options);

        $code = new Builder();

        $body   = $request['body'];
        $params = $request['params'];

        switch ($body['mode']) {
            case 'formdata':
                $code->push('var data = new FormData();');

                foreach ($body['formdata'] as $param) {
                    $code->push(sprintf('data.append("%s", "%s");', json_encode($param['key']), json_encode($param['value'])));
                }

                $code->blank();
                break;
            default:
                $code->push(sprintf('var data = "%s";', $params['body'] ?: 'false'))
                    ->blank();
        }

        $code->push('var xhr = new XMLHttpRequest();');

        if ($opts['cors']) {
            $code->push('xhr.withCredentials = true;');
        }

        $code->blank()
            ->push('xhr.addEventListener("readystatechange", function () {')
            ->push('if (this.readyState === this.DONE) {', 1)
            ->push('console.log(this.responseText);', 2)
            ->push('}', 1)
            ->push('});')
            ->blank()
            ->push(sprintf('xhr.open("%s", "%s");', $request['method'], $request['url']['raw']));

        if (count($request['header'])) {
            foreach ($request['header'] as $header) {
                $code->push(sprintf('xhr.setRequestHeader(%s, %s);', $header['key'], $header['value']));
            }
        }

        $code->blank()
            ->push('xhr.send(data);');

        return $code->join();
    }

    public function info(): array
    {
        return [
            'key'     => 'javascript',
            'title'   => 'Javascript',
            'link'    => 'https://developer.mozilla.org/en-US/docs/Web/API/XMLHttpRequest',
            'details' => 'W3C Standard API that provides scripted client functionality',
        ];
    }
}
