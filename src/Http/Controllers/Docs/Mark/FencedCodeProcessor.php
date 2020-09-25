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

use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\DocumentProcessorInterface;

/**
 * @author sankar <sankar.suda@gmail.com>
 */
class FencedCodeProcessor implements DocumentProcessorInterface
{
    /**
     * @param Document $document
     */
    public function processDocument(Document $document)
    {
        $walker = $document->walker();
        while ($event = $walker->next()) {
            $node = $event->getNode();

            if (!$event->isEntering() || !($node instanceof FencedCode)) {
                continue;
            }

            $node->data['attributes']['class'] = 'language';
        }
    }
}
