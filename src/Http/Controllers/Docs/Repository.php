<?php

declare (strict_types = 1);

/*
 * This file is part of the Deployment package.
 *
 * (c) Sankar <sankar.suda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Diviky\Readme\Http\Controllers\Docs;

use Diviky\Readme\Http\Controllers\Docs\Mark\HeaderProcessor;
use Diviky\Readme\Http\Controllers\Docs\Mark\MarkExtension;
use Illuminate\Support\Facades\Cache;
use Karla\Routing\Capsule;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\Mention\MentionExtension;
use League\CommonMark\Extension\SmartPunct\SmartPunctExtension;

/**
 * @author sankar <sankar.suda@gmail.com>
 */
class Repository extends Capsule
{
    /**
     * Get the given documentation page.
     *
     * @param string $page
     * @param string $version
     *
     * @return string
     */
    public function getPage(string $page, string $version = '1.0'): array
    {
        $time = config('readme.cache_time') ?: 20;

        return Cache::store('file')->remember('docs.' . $version . $page, $time, function () use ($version, $page) {
            $path = resource_path('docs') . '/' . $version . '/' . $page;

            if ($this->get('files')->isDirectory($path)) {
                $path .= '/' . config('readme.docs.landing');
            }

            $path .= '.md';

            if ($this->get('files')->exists($path)) {
                $content = $this->replaceLinks($this->get('files')->get($path), $version);

                return $this->parse($content);
            }

            return [];
        });
    }

    public function getSimplePage(string $page, string $version = '1.0'): ?string
    {
        $time = config('readme.cache_time') ?: 20;

        return Cache::store('file')->remember('docs.' . $version . $page, $time, function () use ($version, $page) {
            $path = resource_path('docs') . '/' . $version . '/' . $page;

            if ($this->get('files')->isDirectory($path)) {
                $path .= '/' . config('readme.docs.landing');
            }

            $path .= '.md';

            if ($this->get('files')->exists($path)) {
                $content = $this->replaceLinks($this->get('files')->get($path), $version);

                return $this->parseSimple($content);
            }

            return null;
        });
    }

    public function parse(string $content): array
    {
        $environment = Environment::createCommonMarkEnvironment();

        $headerProcessor = new HeaderProcessor();
        $environment->addEventListener(DocumentParsedEvent::class, $headerProcessor, -100);

        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new AttributesExtension());
        $environment->addExtension(new FootnoteExtension());
        $environment->addExtension(new MentionExtension());
        $environment->addExtension(new SmartPunctExtension());
        $environment->addExtension(new MarkExtension());

        $extensions = config('readme.extensions');

        if (is_array($extensions)) {
            foreach ($extensions as $extension) {
                $environment->addExtension($extension);
            }
        }

        $config = config('readme.markdown');

        $converter = new CommonMarkConverter($config, $environment);
        $content   = $converter->convertToHtml($content);

        $sections = $headerProcessor->getSections();

        return [
            'body'     => $content,
            'sections' => $sections,
        ];
    }

    public function parseSimple(string $content): string
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new MarkExtension());

        $converter = new CommonMarkConverter([], $environment);

        return $converter->convertToHtml($content);
    }

    /**
     * Replace the version place-holder in links.
     *
     * @param string $version
     * @param string $content
     *
     * @return string
     */
    public function replaceLinks(string $content, string $version): ?string
    {
        $replace = config('readme.variables');
        if (!is_array($replace)) {
            $replace = [];
        }

        $replace['version'] = $version;
        $replace['domain']  = request()->getSchemeAndHttpHost();

        foreach ($replace as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
            $content = str_replace('{{ ' . $key . ' }}', $value, $content);
            $content = str_replace('{' . $key . '}', $value, $content);
        }

        return $content;
    }

    public function getIndexes($version): ?string
    {
        $documentation = config('readme.docs.menu');

        return $this->getSimplePage($documentation, $version);
    }

    public function formatSections(array $sections): array
    {
        $formated    = [];
        $firstLoop   = 0;
        $secondLoop  = 0;
        $thirdLoop   = 0;
        $secondLevel = null;
        $firstLevel  = null;
        $thirdLevel  = null;

        foreach ($sections as $section) {
            $level = $section['l'];

            $format = [
                'name' => $section['t'],
                'url'  => '#' . $section['s'],
            ];

            if (is_null($firstLevel) || $firstLevel == $level || $firstLevel > $level) {
                ++$firstLoop;
                $formated[$firstLoop] = $format;
                $firstLevel           = $level;
            } elseif ($firstLevel < $level) {
                if (is_null($secondLevel) || $secondLevel == $level || $secondLevel > $level) {
                    ++$secondLoop;
                    $formated[$firstLoop]['childs'][$secondLoop] = $format;
                    $secondLevel                                 = $level;
                } elseif ($secondLevel < $level) {
                    //Thid loop
                    if (is_null($thirdLevel) || $thirdLevel == $level || $thirdLevel > $level) {
                        ++$thirdLoop;
                        $formated[$firstLoop]['childs'][$secondLoop]['childs'][$thirdLoop] = $format;
                        $thirdLevel                                                        = $level;
                    } else {
                        $formated[$firstLoop]['childs'][$secondLoop]['childs'][$thirdLoop]['childs'][] = $format;
                    }
                } else {
                    $formated[$firstLoop]['childs'][$secondLoop]['childs'][] = $format;
                }
            } else {
                $formated[$firstLoop]['childs'][] = $format;
            }
        }

        return $formated;
    }

    public function getTitle($content): ?string
    {
        $pattern = '/<h[1-2]>([^<]*)<\/h[1-2]/i';

        if (preg_match($pattern, $content, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function getVersions(): array
    {
        $versions = config('readme.versions');

        if (!is_array($versions)) {
            return [
                'master' => 'master',
            ];
        }

        $first = array_values($versions)[0];

        $versions['master'] = $first;

        return $versions;
    }
}
