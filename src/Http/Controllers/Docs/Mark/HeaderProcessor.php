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
use League\CommonMark\Block\Element\Heading;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Node\Node;
use League\CommonMark\Normalizer\SlugNormalizer;
use League\CommonMark\Util\ConfigurationAwareInterface;
use League\CommonMark\Util\ConfigurationInterface;

/**
 * @author sankar <sankar.suda@gmail.com>
 */
class HeaderProcessor implements ConfigurationAwareInterface
{
    private $sections = [];

    /** @var ConfigurationInterface */
    private $config;

/** @var TextNormalizerInterface */
    private $slugNormalizer;

    public function __construct()
    {
        $this->slugNormalizer = new SlugNormalizer();
    }

    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->config = $configuration;
    }

    public function getSections()
    {
        return $this->sections;
    }

    public function __invoke(DocumentParsedEvent $e): void
    {
        $walker = $e->getDocument()->walker();

        while ($event = $walker->next()) {
            $node = $event->getNode();
            // Only stop at Link nodes when we first encounter them
            if (!($node instanceof Heading) || !$event->isEntering()) {
                continue;
            }

            if ($node->getLevel() > 3) {
                continue;
            }

            if ($node instanceof Heading && $event->isEntering()) {
                $this->addHeadingLink($node, $e->getDocument());
            }
        }
    }

    private function addHeadingLink(Heading $node, Document $document): void
    {
        $text = $node->getStringContent();
        $slug = $this->slugNormalizer->normalize($text);

        $this->sections[] = [
            'l' => $node->getLevel(),
            't' => $text,
            's' => $slug,
        ];

        $node->data['attributes']['class'] = 'header-scroll header-scroll-' . $node->getLevel();

        $inline = new Link('#' . $slug);

        $inline->data['attributes'] = ['class' => 'fa fa-anchor hub-anchor'];

        $node->appendChild($inline);

        $inline = new Link('#' . $slug);

        $inline->data['attributes'] = ['id' => $slug, 'class' => 'section-anchor'];

        $node->prependChild($inline);
    }

    /**
     * @deprecated Not needed in 2.0
     */
    private function getChildText(Node $node): string
    {
        $text = '';

        $walker = $node->walker();
        while ($event = $walker->next()) {
            if ($event->isEntering() && (($child = $event->getNode()) instanceof Text || $child instanceof Code)) {
                $text .= $child->getContent();
            }
        }

        return $text;
    }

}
