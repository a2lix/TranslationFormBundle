<?php

/*
 * This file is part of A2lix projects.
 *
 * (c) David ALLIX
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Form\EventListener;

use A2lix\AutoFormBundle\Form\Manipulator\FormManipulatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class TranslationsListener implements EventSubscriberInterface
{
    /** @var FormManipulatorInterface */
    private $formManipulator;

    /**
     * @param FormManipulatorInterface $formManipulator
     */
    public function __construct(FormManipulatorInterface $formManipulator)
    {
        $this->formManipulator = $formManipulator;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::SUBMIT => 'submit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $formOptions = $form->getConfig()->getOptions();

        $fieldsOptions = $this->getFieldsOptions($form, $formOptions);
        $translationClass = $this->getTranslationClass($form->getParent());

        foreach ($formOptions['locales'] as $locale) {
            if (isset($fieldsOptions[$locale])) {
                $form->add($locale, 'A2lix\AutoFormBundle\Form\Type\AutoFormType', [
                    'data_class' => $translationClass,
                    'required' => in_array($locale, $formOptions['required_locales'], true),
                    'block_name' => ('field' === $formOptions['theming_granularity']) ? 'locale' : null,
                    'fields' => $fieldsOptions[$locale],
                    'excluded_fields' => $formOptions['excluded_fields'],
                ]);
            }
        }
    }

    /**
     * @param FormEvent $event
     */
    public function submit(FormEvent $event)
    {
        $data = $event->getData();

        foreach ($data as $locale => $translation) {
            // Remove useless Translation object
            if (!$translation) {
                $data->removeElement($translation);
            } else {
                $translation->setLocale($locale);
            }
        }
    }

    /**
     * @param FormInterface $form
     *
     * @return string
     */
    private function getTranslationClass(FormInterface $form)
    {
        do {
            $translatableClass = $form->getConfig()->getDataClass();
        } while ((null === $translatableClass) && $form->getConfig()->getVirtual() && ($form = $form->getParent()));

        // Knp
        if (method_exists($translatableClass, 'getTranslationEntityClass')) {
            return $translatableClass::getTranslationEntityClass();
        }

        // Gedmo
        if (method_exists($translatableClass, 'getTranslationClass')) {
            return $translatableClass::getTranslationClass();
        }

        return $translatableClass . 'Translation';
    }

    /**
     * @param FormInterface $form
     * @param array         $formOptions
     *
     * @return array
     */
    public function getFieldsOptions(FormInterface $form, array $formOptions)
    {
        $fieldsOptions = [];

        $fieldsConfig = $this->formManipulator->getFieldsConfig($form);
        foreach ($fieldsConfig as $fieldName => $fieldConfig) {
            // Simplest case: General options for all locales
            if (!isset($fieldConfig['locale_options'])) {
                foreach ($formOptions['locales'] as $locale) {
                    $fieldsOptions[$locale][$fieldName] = $fieldConfig;
                }

                continue;
            }

            // Custom options by locale
            $localesFieldOptions = $fieldConfig['locale_options'];
            unset($fieldConfig['locale_options']);

            foreach ($formOptions['locales'] as $locale) {
                $localeFieldOptions = isset($localesFieldOptions[$locale]) ? $localesFieldOptions[$locale] : [];
                if (!isset($localeFieldOptions['display']) || (true === $localeFieldOptions['display'])) {
                    $fieldsOptions[$locale][$fieldName] = $localeFieldOptions + $fieldConfig;
                }
            }
        }

        return $fieldsOptions;
    }
}
