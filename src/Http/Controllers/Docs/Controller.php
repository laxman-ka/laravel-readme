<?php

declare(strict_types=1);

/*
 * This file is part of the Deployment package.
 *
 * (c) Sankar <sankar.suda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Diviky\Readme\Http\Controllers\Docs;

use App\Http\Controllers\Controller as BaseController;

/**
 * @author sankar <sankar.suda@gmail.com>
 */
class Controller extends BaseController
{
    /**
     * The documentation repository.
     *
     * @var \App\Documentation
     */
    protected $docs;

    /**
     * Create a new controller instance.
     *
     * @param \App\Documentation $docs
     */
    public function __construct(Repository $docs)
    {
        $this->docs = $docs;
    }

    public function loadViewsFrom(): string
    {
        return __DIR__;
    }

    public function index($version = null, $page = null): array
    {
        $versions = $this->docs->getVersions();

        $page    = $page ?: config('readme.docs.landing');
        $version = $version ?: config('readme.versions.default', 'master');

        $version = isset($versions[$version]) ? $versions[$version] : $version;

        $indexes = $this->docs->getIndexes($version);
        $content = $this->docs->getPage($page, $version);

        $sections = [];
        if (isset($content['sections']) && \is_array($content['sections'])) {
            $sections = $content['sections'] ?? null;
            $title    = $sections[0]['t'] ?? null;
            $sections = $this->docs->formatSections($sections);
        }

        return [
            'content'  => $content['body'] ?? null,
            'title'    => $title ?? null,
            'index'    => $indexes,
            'sections' => $sections,
            'versions' => $versions,
            'version'  => $version,
        ];
    }
}
