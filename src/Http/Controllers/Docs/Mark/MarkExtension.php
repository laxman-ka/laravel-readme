<?php

/*
 * This file is part of the Deployment package.
 *
 * (c) Sankar <sankar.suda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Divity\Readme\Http\Controllers\Docs\Mark;

use League\CommonMark\Extension\Extension;

/**
 * @author sankar <sankar.suda@gmail.com>
 */
class MarkExtension extends Extension
{
    private $emojis = [];

    public function getBlockParsers()
    {
        return [];
    }

    public function getInlineParsers()
    {
        return [
            new EmojiParser($this->getEmojis()),
            new FontAwesomeParser($this->getEmojis()),
        ];
    }

    public function getInlineProcessors()
    {
        return [];
    }

    public function getDocumentProcessors()
    {
        return [
            new FencedCodeProcessor(),
        ];
    }

    private function getEmojis(): array
    {
        if (empty($this->emojis)) {
            $this->emojis = json_decode(file_get_contents(__DIR__ . '/emoji.json'), true);
        }

        return $this->emojis;
    }
}
