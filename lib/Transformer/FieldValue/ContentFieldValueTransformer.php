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

namespace ErdnaxelaWeb\IbexaDesignIntegration\Transformer\FieldValue;

use ErdnaxelaWeb\IbexaDesignIntegration\Transformer\ContentTransformer;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\Relation\Value as RelationValue;
use Ibexa\Core\FieldType\RelationList\Value as RelationListValue;

class ContentFieldValueTransformer implements FieldValueTransformerInterface
{
    public function __construct(
        protected ContentTransformer $contentTransformer
    ) {
    }

    public function transformFieldValue(
        Content         $content,
        string          $fieldIdentifier,
        FieldDefinition $fieldDefinition,
        array $fieldConfiguration
    ) {
        $max = $fieldConfiguration['options']['max'];
        /** @var \Ibexa\Core\FieldType\RelationList\Value $fieldValue */
        $fieldValue = $content->getFieldValue($fieldIdentifier);
        $destinationContentIds = [];

        if ($fieldValue instanceof RelationValue) {
            $destinationContentIds = [$fieldValue->destinationContentId];
        }
        if ($fieldValue instanceof RelationListValue) {
            $destinationContentIds = $fieldValue->destinationContentIds;
        }

        if ($max === 1) {
            if (! empty($destinationContentIds)) {
                return $this->contentTransformer->lazyTransformContentFromContentId(reset($destinationContentIds));
            }
            return null;
        }
        return array_map(function (int $destinationContentId) {
            return $this->contentTransformer->lazyTransformContentFromContentId($destinationContentId);
        }, $destinationContentIds);
    }
}
