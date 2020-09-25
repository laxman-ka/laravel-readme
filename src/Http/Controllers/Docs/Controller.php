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

use App\Http\Controllers\Controller as BaseController;

/**
 * @author sankar <sankar.suda@gmail.com>
 */
class Controller extends BaseController
{
    public function index($version = null, $page = null): array
    {
        $repo = new Repository();

        $versions = $repo->getVersions();

        if ($page == null && $version && !$versions[$version]) {
            $page    = $version;
            $version = null;
        }

        $page    = $page ?: config('readme.docs.landing');
        $version = $version ?: 'master';
        $version = $versions[$version];

        $indexes = $repo->getIndexes($version);
        $content = $repo->getPage($page, $version);

        $sections = [];
        if (is_array($content['sections'])) {
            $sections = $content['sections'];
            $title    = $sections[0]['t'];
            $sections = $repo->formatSections($sections);
        }

        return [
            'content'  => $content['body'],
            'title'    => $title,
            'index'    => $indexes,
            'sections' => $sections,
            'versions' => $versions,
            'version'  => $version,
        ];
    }
}
