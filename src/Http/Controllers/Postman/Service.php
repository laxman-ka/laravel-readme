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

namespace Diviky\Readme\Http\Controllers\Postman;

use App\Http\Controllers\Docs\Mark\MarkExtension;
use App\Http\Controllers\Postman\Snippet\Generator;
use Karla\Routing\Capsule;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extras\SmartPunct\SmartPunctExtension;
use League\CommonMark\Extras\TwitterHandleAutolink\TwitterHandleAutolinkExtension;
use Speedwork\Util\Str;
use Webuni\CommonMark\TableExtension\TableExtension;

/**
 * @author sankar <sankar.suda@gmail.com>
 */
class Service extends Capsule
{
    private $markdown;

    public function generateIndexes(array $collection): array
    {
        return $this->generateIndex($collection['item']);
    }

    public function generateIndex(array $collection): array
    {
        $indexes = [];
        foreach ($collection as $value) {
            $method = is_array($value['request']) ? $value['request']['method'] : '';

            $index = [
                'name'        => $value['name'],
                'link'        => '#' . Str::slug($value['name']),
                'method'      => $method,
                'method_slug' => $method ? Str::slug($method) : '',
            ];

            if (is_array($value['item'])) {
                $index['childs'] = $this->generateIndex($value['item']);
            }

            $indexes[] = $index;
        }

        return $indexes;
    }

    public function parse(string $content): string
    {
        if (is_null($this->markdown)) {
            $environment = Environment::createCommonMarkEnvironment();

            $environment->addExtension(new MarkExtension());
            $environment->addExtension(new TableExtension());
            $environment->addExtension(new TwitterHandleAutolinkExtension());
            $environment->addExtension(new SmartPunctExtension());

            $this->markdown = new CommonMarkConverter([], $environment);
        }

        return $this->markdown->convertToHtml($content);
    }

    public function modifyCollection(array &$collection): array
    {
        foreach ($collection as $key => &$value) {
            if (is_array($value)) {
                if (isset($value['name'])) {
                    $value['slug'] = Str::slug($value['name']);
                }

                if (isset($value['request'])) {
                    foreach (['shell', 'php', 'python', 'ruby', 'java', 'node', 'javascript', 'javascript.jquery'] as $language) {
                        $value['request']['code'][] = Generator::prepare($value['request'], $language);
                    }
                }

                $this->modifyCollection($value);
            } elseif ('description' == $key) {
                $value = $this->parse($value);
            }
        }

        return $collection;
    }
}
