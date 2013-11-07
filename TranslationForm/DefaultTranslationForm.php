<?php

namespace A2lix\TranslationFormBundle\TranslationForm;

use Doctrine\Common\Util\ClassUtils;

/**
 * @author David ALLIX
 */
class DefaultTranslationForm extends TranslationForm
{
    /**
     *
     * @param string $translationClass
     * @return array
     */
    protected function getTranslatableFields($translationClass)
    {
        $translationClass = ClassUtils::getRealClass($translationClass);
        $manager = $this->getManagerRegistry()->getManagerForClass($translationClass);
        $metadataClass = $manager->getMetadataFactory()->getMetadataFor($translationClass);

        $fields = array();
        foreach ($metadataClass->fieldMappings as $fieldMapping) {
            if (!in_array($fieldMapping['fieldName'], array('id', 'locale'))) {
                $fields[] = $fieldMapping['fieldName'];
            }
        }

        return $fields;
    }
}