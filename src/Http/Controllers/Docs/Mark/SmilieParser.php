<?php

/*
 * This file is part of the Deployment package.
 *
 * (c) Sankar <sankar.suda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Diviky\Readme\Http\Controllers\Docs\Mark;

use League\CommonMark\InlineParserContext;
use League\CommonMark\Inline\Parser\AbstractInlineParser;

/**
 * @author sankar <sankar.suda@gmail.com>
 */
class SmilieParser extends AbstractInlineParser
{
    public function getCharacters()
    {
        return [':'];
    }

    public function parse(InlineParserContext $inlineContext)
    {
        $cursor = $inlineContext->getCursor();

        // The next character must be a paren; if not, then bail
        // We use peek() to quickly check without affecting the cursor
        $nextChar = $cursor->peek();
        if ('(' !== $nextChar && ')' !== $nextChar) {
            return false;
        }

        // Advance the cursor past the 2 matched chars since we're able to parse them successfully
        $cursor->advanceBy(2);

        // Add the corresponding image
        if (')' === $nextChar) {
            $inlineContext->getContainer()->appendChild(new Image('/img/happy.png'));
        } elseif ('(' === $nextChar) {
            $inlineContext->getContainer()->appendChild(new Image('/img/sad.png'));
        }

        return true;
    }
}
