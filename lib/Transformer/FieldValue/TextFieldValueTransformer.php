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

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;

class TextFieldValueTransformer
{
    public function transformFieldValue(
        Content         $content,
        string          $fieldIdentifier,
        FieldDefinition $fieldDefinition,
        array $fieldConfiguration
    ) {
        /** @var \Ibexa\Core\FieldType\TextBlock\Value $fieldValue */
        $fieldValue = $content->getFieldValue($fieldIdentifier);
        if ($fieldValue != "") {
            return sprintf('<p>%s</p>', nl2br($fieldValue->text));
        }

        return null;
    }
}
