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

namespace Diviky\Readme\Http\Controllers\Postman\Snippet\Targets\Shell;

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
            'indent' => '  ',
            'short'  => true,
            'binary' => false,
        ], $options);

        $code = new Builder($opts['indent'], false !== $opts['indent'] ? " \\\n" . $opts['indent'] : ' ');

        $params = $request['params'];
        $url    = $request['url'];

        $code->push(sprintf('curl %s %s', $opts['short'] ? '-X' : '--request', $request['method']))
            ->push(sprintf('%s%s', $opts['short'] ? '' : '--url ', $this->quote($url['raw'])));

        if ('HTTP/1.0' === $request['httpVersion']) {
            $code->push($opts['short'] ? '-0' : '--http1.0');
        }

        // construct headers
        if (count($headers)) {
            $headers = array_map(function ($header) use ($code) {
                $string = sprintf('"%s: %s"', $header['key'], $header['value']);
                $code->push(sprintf('%s %s', $opts['short'] ? '-H' : '--header', $this->quote($string)));
            }, $request['header']);
        }

        if (count($request['cookies'])) {
            $cookies = array_map(function ($cookie) {
                return urlencoded($cookie['key']) . '=' . urlencoded($cookie['value']);
            }, $request['cookies']);

            $code->push(sprintf('%s %s', $opts['short'] ? '-b' : '--cookie', $this->quote(implode('; ', $cookies))));
        }

        // construct post params
        switch ($request['body']['mode']) {
            case 'formdata':
                foreach ($request['body']['formdata'] as $param) {
                    $post = sprintf('%s=%s', $param['key'], $param['value']);

                    if ('file' == $param['type']) {
                        $post = sprintf('%s=@%s', $param['key'], $param['value']);
                    }

                    $code->push(sprintf('%s %s', $opts['short'] ? '-F' : '--form', $this->quote($post)));
                }
                break;

            case 'urlencoded':
                $code->push(
                    sprintf(
                        '%s %s',
                        $opts['binary'] ? '--data-binary' : ($opts['short'] ? '-d' : '--data'),
                        $this->escape($this->quote($params['body']))
                    )
                );
                break;
            default:
                // raw request body
                if ($params['body']) {
                    $code->push(
                        sprintf(
                            '%s %s',
                            $opts['binary'] ? '--data-binary' : ($opts['short'] ? '-d' : '--data'),
                            $this->escape($this->quote($params['body']))
                        )
                    );
                }
        }

        return $code->join();
    }

    public function info(): array
    {
        return [
            'key'     => 'bash',
            'title'   => 'Shell',
            'link'    => 'http://curl.haxx.se/',
            'details' => 'cURL is a command line tool and library for transferring data with URL syntax',
        ];
    }

    /**
     * Use 'strong quoting' using single quotes so that we only need
     * to deal with nested single quote characters.
     * http://wiki.bash-hackers.org/syntax/quoting#strong_quoting.
     */
    protected function quote($value)
    {
        $safe = '/^[a-z0-9-_\/.@%^=:]+$/';
        // Unless `value` is a simple shell-safe string, quote it.
        if (!preg_match($safe, $value)) {
            return sprintf('\'%s\'', str_replace("'", "\'\\'\'", $value));
        }

        return $value;
    }

    protected function escape($value)
    {
        $value = str_replace("\r", '\\r', $value);

        return str_replace("\n", '\\n', $value);
    }
}
