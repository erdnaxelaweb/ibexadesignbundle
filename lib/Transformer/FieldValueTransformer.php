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

namespace ErdnaxelaWeb\IbexaDesignIntegration\Transformer;

use Ibexa\Contracts\Core\Repository\Values\Content\Content as IbexaContent;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;

class FieldValueTransformer
{
    /**
     * @var \ErdnaxelaWeb\IbexaDesignIntegration\Transformer\FieldValue\FieldValueTransformerInterface[]
     */
    protected array $fieldValueTransformers = [];

    public function __construct(
        iterable $transformers
    ) {
        foreach ($transformers as $type => $fieldValueTransformer) {
            $this->fieldValueTransformers[$type] = $fieldValueTransformer;
        }
    }

    public function transform(
        IbexaContent $ibexaContent,
        ContentType $contentType,
        string $fieldIdentifier,
        array $fieldConfiguration
    ) {
        $fieldDefinition = $contentType->getFieldDefinition($fieldIdentifier);
        if ($fieldDefinition) {
            $fieldValueTransformer = $this->fieldValueTransformers[$fieldDefinition->fieldTypeIdentifier];
            return $fieldValueTransformer->transformFieldValue(
                $ibexaContent,
                $fieldIdentifier,
                $fieldDefinition,
                $fieldConfiguration
            );
        }
        return null;
    }
}
