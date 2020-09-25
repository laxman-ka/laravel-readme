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

namespace Divity\Readme\Http\Controllers\Postman\Snippet\Targets\Php;

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
            'closingTag'   => false,
            'indent'       => '  ',
            'maxRedirects' => 10,
            'namedErrors'  => false,
            'noTags'       => false,
            'shortTags'    => false,
            'timeout'      => 30,
        ], $options);

        $code = new Builder($opts['indent']);

        $params = $request['params'];

        if (!$opts['noTags']) {
            $code->push($opts['shortTags'] ? '<?' : '<?php')->blank();
        }

        $code->push('$curl = curl_init();')->blank();

        $curlOptions = [[
            'escape' => true,
            'name'   => 'CURLOPT_PORT',
            'value'  => $request['url']['port'],
        ], [
            'escape' => true,
            'name'   => 'CURLOPT_URL',
            'value'  => $request['url']['raw'],
        ], [
            'escape' => false,
            'name'   => 'CURLOPT_RETURNTRANSFER',
            'value'  => 'true',
        ], [
            'escape' => true,
            'name'   => 'CURLOPT_ENCODING',
            'value'  => '',
        ], [
            'escape' => false,
            'name'   => 'CURLOPT_MAXREDIRS',
            'value'  => $opts['maxRedirects'],
        ], [
            'escape' => false,
            'name'   => 'CURLOPT_TIMEOUT',
            'value'  => $opts['timeout'],
        ], [
            'escape' => false,
            'name'   => 'CURLOPT_HTTP_VERSION',
            'value'  => 'HTTP/1.0' === $request['httpVersion'] ? 'CURL_HTTP_VERSION_1_0' : 'CURL_HTTP_VERSION_1_1',
        ], [
            'escape' => true,
            'name'   => 'CURLOPT_CUSTOMREQUEST',
            'value'  => $request['method'],
        ], [
            'escape' => true,
            'name'   => 'CURLOPT_POSTFIELDS',
            'value'  => $params['body'] ?: null,
        ]];

        $code->push('curl_setopt_array($curl, array(');

        $curlopts = new Builder($opts['indent'], "\n" . $opts['indent']);

        foreach ($curlOptions as $option) {
            if (!is_null($option['value']) && 'undefined' != $option['value']) {
                $curlopts->push(sprintf('%s => %s,', $option['name'], $option['escape'] ? '"' . $option['value'] . '"' : $option['value']));
            }
        }

        // construct cookies
        if ($params['cookies']) {
            $curlopts->push(sprintf('CURLOPT_COOKIE => "%s",', implode('; ', $params['cookies'])));
        }

        if ($params['header']) {
            $curlopts->push('CURLOPT_HTTPHEADER => array(')
                ->push(implode(',\n' . $opts['indent'] . $opts['indent'], $params['header']), 1)
                ->push('),');
        }

        $code->push($curlopts->join(), 1)
            ->push('));')
            ->blank()
            ->push('$response = curl_exec($curl);')
            ->push('$err = curl_error($curl);')
            ->blank()
            ->push('curl_close($curl);')
            ->blank()
            ->push('if ($err) {');

        if ($opts['namedErrors']) {
            $code->push('echo array_flip(get_defined_constants(true)["curl"])[$err];', 1);
        } else {
            $code->push('echo "cURL Error #:" . $err;', 1);
        }

        $code->push('} else {')
            ->push('echo $response;', 1)
            ->push('}');

        if (!$opts['noTags'] && $opts['closingTag']) {
            $code->blank()->push('?>');
        }

        return $code->join();
    }

    public function info(): array
    {
        return [
            'key'     => 'php',
            'title'   => 'PHP',
            'link'    => 'http://php.net/manual/en/book.curl.php',
            'details' => 'PHP with ext-curl',
        ];
    }
}
