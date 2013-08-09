<?php

namespace A2lix\TranslationFormBundle\Form\DataMapper;

use Symfony\Component\Form\DataMapperInterface,
    Symfony\Component\Form\Exception\UnexpectedTypeException,
    Doctrine\Common\Collections\ArrayCollection;

/**
 * @author David ALLIX
 */
class GedmoTranslationMapper implements DataMapperInterface
{
    /**
     * {@inheritdoc}
     */
    public function mapDataToForms($data, $forms)
    {
        if (null === $data || array() === $data) {
            return;
        }

        if (!is_array($data) && !is_object($data)) {
            throw new UnexpectedTypeException($data, 'object, array or empty');
        }

        foreach ($forms as $translationsFieldsForm) {
            $locale = $translationsFieldsForm->getConfig()->getName();

            $tmpFormData = array();
            foreach ($data as $translation) {
                if ($locale === $translation->getLocale()) {
                    $tmpFormData[$translation->getField()] = $translation->getContent();
                }
            }
            $translationsFieldsForm->setData($tmpFormData);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function mapFormsToData($forms, &$data)
    {
        if (null === $data) {
            return;
        }

        if (!is_array($data) && !is_object($data)) {
            throw new UnexpectedTypeException($data, 'object, array or empty');
        }

        $newData = new ArrayCollection();

        foreach ($forms as $translationsFieldsForm) {
            $translationsFieldsConfig = $translationsFieldsForm->getConfig();
            $locale = $translationsFieldsConfig->getName();
            $translationClass = $translationsFieldsConfig->getOption('translation_class');

            foreach ($translationsFieldsForm->getData() as $field => $content) {
                $existingTranslation = $data ? $data->filter(function($object) use ($locale, $field) {
                    return ($object && ($object->getLocale() === $locale) && ($object->getField() === $field));
                })->first() : null;

                if ($existingTranslation) {
                    $existingTranslation->setContent($content);
                    $newData->add($existingTranslation);

                } else {
                    $translation = new $translationClass();
                    $translation->setLocale($locale);
                    $translation->setField($field);
                    $translation->setContent($content);
                    $newData->add($translation);
                }
            }
        }

        $data = $newData;
    }
}
