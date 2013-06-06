<?php

namespace A2lix\TranslationFormBundle\TranslationForm;

/**
 * @author David ALLIX
 */
class DefaultTranslationForm extends TranslationForm
{
    /**
     * {@inheritdoc}
     */
    public function init($translatableClass)
    {
        $translationClass = $translatableClass::getTranslationEntityClass();

        $this->setTranslatableClass($translatableClass);
        $this->setTranslationClass($translationClass);
        $this->setTranslatableFields($this->getTranslatableFieldsFromMetadata($translationClass));
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentLocale()
    {
        return $this->getTranslatableClass()->getCurrentLocale();
    }

    /**
     *
     * @param type $translationClass
     * @return type
     */
    private function getTranslatableFieldsFromMetadata($translationClass)
    {
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