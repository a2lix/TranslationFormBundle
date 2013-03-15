<?php

namespace A2lix\TranslationFormBundle\Form\DataMapper;

use Symfony\Component\Form\DataMapperInterface,
    Symfony\Component\Form\Util\VirtualFormAwareIterator,
    Symfony\Component\Form\Exception\UnexpectedTypeException,
    Doctrine\Common\Collections\ArrayCollection;

/**
 * @author David ALLIX
 */
class TranslationMapper implements DataMapperInterface
{
    private $translationClass;

    public function __construct($translationClass)
    {
        $this->translationClass = $translationClass;
    }

    /**
     * {@inheritdoc}
     */
    public function mapDataToForms($data, array $forms)
    {
        if (null === $data || array() === $data) {
            return;
        }

        if (!is_array($data) && !is_object($data)) {
            throw new UnexpectedTypeException($data, 'object, array or empty');
        }

        $iterator = new VirtualFormAwareIterator($forms);
        $iterator = new \RecursiveIteratorIterator($iterator);

        foreach ($iterator as $form) {
            $locale = $form->getConfig()->getName();

            $trans = array();
            foreach ($data as $d) {
                if ($locale === $d->getLocale()) {
                    $trans[$d->getField()] = $d->getContent();
                }
            }
            $form->setData($trans);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function mapFormsToData(array $forms, &$data)
    {
        if (null === $data) {
            return;
        }

        if (!is_array($data) && !is_object($data)) {
            throw new UnexpectedTypeException($data, 'object, array or empty');
        }

        $iterator = new VirtualFormAwareIterator($forms);
        $iterator = new \RecursiveIteratorIterator($iterator);

        $newData = new ArrayCollection();
        foreach ($iterator as $form) {
            $locale = $form->getConfig()->getName();

            foreach ($form->getData() as $field => $content) {
                $existingTranslationEntity = $data ? $data->filter(function($entity) use ($locale, $field) {
                    return ($entity && ($entity->getLocale() === $locale) && ($entity->getField() === $field));
                })->first() : null;

                if ($existingTranslationEntity) {
                    $existingTranslationEntity->setContent($content);
                    $newData->add($existingTranslationEntity);

                } else {
                    $translationEntity = new $this->translationClass();
                    $translationEntity->setLocale($locale);
                    $translationEntity->setField($field);
                    $translationEntity->setContent($content);
                    $newData->add($translationEntity);
                }
            }
        }

        $data = $newData;
    }
}
