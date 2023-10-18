<?php
/*
 * ibexadesignbundle.
 *
 * @package   ibexadesignbundle
 *
 * @author    florian
 * @copyright 2023-present Florian ALEXANDRE
 * @license   https://github.com/erdnaxelaweb/ibexadesignintegration/blob/main/LICENSE
 */

declare(strict_types=1);

namespace ErdnaxelaWeb\IbexaDesignIntegration\Fake\Generator;

use DOMDocument;
use DOMElement;
use ErdnaxelaWeb\StaticFakeDesign\Fake\FakerGenerator;
use ErdnaxelaWeb\StaticFakeDesign\Fake\Generator\RichTextGenerator as BaseRichTextGenerator;
use Ibexa\Contracts\FieldTypeRichText\RichText\Converter;

class RichTextGenerator extends BaseRichTextGenerator
{
    public const TYPE = 'richtext';

    public const P_TAG = 'para';

    public const A_TAG = 'link';

    public const TABLE_TAG = 'table';

    public const THEAD_TAG = 'thead';

    public const TBODY_TAG = 'tbody';

    public const TR_TAG = 'tr';

    public const TD_TAG = 'td';

    public const TH_TAG = 'th';

    public const UL_TAG = 'itemizedlist';

    public const LI_TAG = 'listitem';

    public const H_TAG = 'title';

    public const B_TAG = 'emphasis';

    public const I_TAG = 'emphasis';

    public function __construct(
        protected Converter $richTextOutputConverter,
        FakerGenerator      $fakerGenerator
    ) {
        parent::__construct($fakerGenerator);
    }

    public function __invoke(int $maxWidth = 10, array $allowedTags = []): string
    {
        $domDocument = new DOMDocument();

        $domDocument->loadXML(
            '<?xml version="1.0" encoding="UTF-8"?>
<section xmlns="http://docbook.org/ns/docbook" 
xmlns:xlink="http://www.w3.org/1999/xlink" 
xmlns:ezxhtml="http://ibexa.co/xmlns/dxp/docbook/xhtml" 
xmlns:ezcustom="http://ibexa.co/xmlns/dxp/docbook/custom" 
version="5.0-variant ezpublish-1.0"></section>'
        );

        /** @var DOMElement $root */
        $body = $domDocument->firstChild;
        $this->addRandomSubTree($body, $maxWidth, $allowedTags);

        $resultDocument = new DOMDocument();
        $resultDocument->loadXml($domDocument->saveXML());

        return $this->richTextOutputConverter->convert($resultDocument)
            ->saveHTML() ?: '';
    }

    protected function addRandomSubTree(DOMElement $root, int $maxWidth = 10, array $allowedTags = []): DOMElement
    {
        $siblings = $this->fakerGenerator->numberBetween(1, $maxWidth);

        for ($i = 0; $i < $siblings; ++$i) {
            $this->addRandomLeaf($root, $allowedTags);
        }

        return $root;
    }

    protected function addRandomLeaf(DOMElement $node, array $allowedTags = [])
    {
        $tag = $this->fakerGenerator->randomElement(! empty($allowedTags) ? $allowedTags : self::ALLOWED_TAGS);

        switch ($tag) {
            case 'a':
                $this->addRandomA($node);
                break;

            case 'ul':
                $this->addRandomUL($node);
                break;

            case 'h':
                $this->addRandomH($node);
                break;

            case 'b':
                $this->addRandomB($node);
                break;

            case 'i':
                $this->addRandomI($node);
                break;

            case 'table':
                $this->addRandomTable($node);
                break;
            case 'p':
            default:
                $this->addRandomP($node);
                break;
        }
    }

    protected function addRandomP(DOMElement $element, $maxLength = 10)
    {
        $node = $element->ownerDocument->createElement(static::P_TAG);
        $node->textContent = $this->fakerGenerator->sentence($this->fakerGenerator->numberBetween(1, $maxLength));
        $element->appendChild($node);
    }

    protected function addRandomA(DOMElement $element, $maxLength = 10)
    {
        $text = $element->ownerDocument->createTextNode(
            $this->fakerGenerator->sentence($this->fakerGenerator->numberBetween(1, $maxLength))
        );
        $linkNode = $element->ownerDocument->createElement(static::A_TAG);
        $linkNode->setAttribute('xlink:href', $this->fakerGenerator->url());
        $linkNode->appendChild($text);

        $node = $element->ownerDocument->createElement(static::P_TAG);
        $node->textContent = $this->fakerGenerator->sentence($this->fakerGenerator->numberBetween(1, $maxLength));
        $node->appendChild($linkNode);
        $element->appendChild($node);
    }

    protected function addRandomH(DOMElement $element, $maxLength = 10)
    {
        $text = $element->ownerDocument->createTextNode(
            $this->fakerGenerator->sentence($this->fakerGenerator->numberBetween(1, $maxLength))
        );
        $node = $element->ownerDocument->createElement(static::H_TAG);
        $node->setAttribute('ezxhtml:level', (string) $this->fakerGenerator->numberBetween(1, 5));
        $node->appendChild($text);
        $element->appendChild($node);
    }

    protected function addRandomB(DOMElement $element, $maxLength = 10)
    {
        $text = $element->ownerDocument->createTextNode(
            $this->fakerGenerator->sentence($this->fakerGenerator->numberBetween(1, $maxLength))
        );
        $node = $element->ownerDocument->createElement(static::B_TAG);
        $node->setAttribute('role', 'strong');
        $node->appendChild($text);
        $element->appendChild($node);
    }

    protected function addRandomI(DOMElement $element, $maxLength = 10)
    {
        $text = $element->ownerDocument->createTextNode(
            $this->fakerGenerator->sentence($this->fakerGenerator->numberBetween(1, $maxLength))
        );
        $node = $element->ownerDocument->createElement(static::I_TAG);
        $node->appendChild($text);
        $element->appendChild($node);
    }

    protected function addRandomTable(DOMElement $element, $maxRows = 10, $maxCols = 6, $maxTitle = 4, $maxLength = 10)
    {
        $rows = $this->fakerGenerator->numberBetween(1, $maxRows);
        $cols = $this->fakerGenerator->numberBetween(1, $maxCols);

        $table = $element->ownerDocument->createElement(static::TABLE_TAG);
        $thead = $element->ownerDocument->createElement(static::THEAD_TAG);
        $tbody = $element->ownerDocument->createElement(static::TBODY_TAG);

        $table->appendChild($thead);
        $table->appendChild($tbody);

        $row = $element->ownerDocument->createElement(static::TR_TAG);
        $thead->appendChild($row);

        for ($i = 0; $i < $cols; ++$i) {
            $cell = $element->ownerDocument->createElement(static::TH_TAG);
            $cell->textContent = $this->fakerGenerator->sentence(
                $this->fakerGenerator->numberBetween(1, $maxTitle)
            );
            $row->appendChild($cell);
        }

        for ($i = 0; $i < $rows; ++$i) {
            $row = $element->ownerDocument->createElement(static::TR_TAG);
            $tbody->appendChild($row);

            for ($j = 0; $j < $cols; ++$j) {
                $cell = $element->ownerDocument->createElement(static::TD_TAG);
                $cell->textContent = $this->fakerGenerator->sentence(
                    $this->fakerGenerator->numberBetween(1, $maxLength)
                );
                $row->appendChild($cell);
            }
        }
        $element->appendChild($table);
    }

    protected function addRandomUL(DOMElement $element, $maxItems = 11, $maxLength = 4)
    {
        $num = $this->fakerGenerator->numberBetween(1, $maxItems);
        $list = $element->ownerDocument->createElement(static::UL_TAG);

        for ($i = 0; $i < $num; ++$i) {
            $listItem = $element->ownerDocument->createElement(static::LI_TAG);

            $para = $element->ownerDocument->createElement(static::P_TAG);
            $para->textContent = $this->fakerGenerator->sentence(
                $this->fakerGenerator->numberBetween(1, $maxLength)
            );
            $listItem->appendChild($para);
            $list->appendChild($listItem);
        }
        $element->appendChild($list);
    }
}
