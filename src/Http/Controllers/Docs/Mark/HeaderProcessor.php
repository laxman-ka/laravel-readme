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

use Illuminate\Support\Str;
use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\Heading;
use League\CommonMark\DocumentProcessorInterface;
use League\CommonMark\Inline\Element\Link;

/**
 * @author sankar <sankar.suda@gmail.com>
 */
class HeaderProcessor implements DocumentProcessorInterface
{
    private $sections = [];

    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @param Document $document
     */
    public function processDocument(Document $document)
    {
        $walker = $document->walker();
        while ($event = $walker->next()) {
            $node = $event->getNode();

            // Only stop at Link nodes when we first encounter them
            if (!($node instanceof Heading) || !$event->isEntering()) {
                continue;
            }

            if ($node->getLevel() > 3) {
                continue;
            }

            $title = implode("\n", $node->getStrings());
            $slug  = Str::slug($title);

            $this->sections[] = [
                'l' => $node->getLevel(),
                't' => $title,
                's' => $slug,
            ];

            $node->data['attributes']['class'] = 'header-scroll header-scroll-' . $node->getLevel();

            $inline                     = new Link('#' . $slug);
            $inline->data['attributes'] = ['class' => 'fa fa-anchor hub-anchor'];
            $node->appendChild($inline);

            $inline                     = new Link('#' . $slug);
            $inline->data['attributes'] = ['id' => $slug, 'class' => 'section-anchor'];
            $node->prependChild($inline);
        }
    }
}
