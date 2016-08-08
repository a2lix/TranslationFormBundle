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
use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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

        $translationClass = $this->getTranslationClass($form->getParent()->getConfig()->getDataClass());
        $fieldsOptions = $this->getFieldsOptions($form->getConfig()->getOptions(), $translationClass);

        foreach ($formOptions['locales'] as $locale) {
            if (isset($fieldsOptions[$locale])) {
                $form->add($locale, 'A2lix\AutoFormBundle\Form\Type\AutoFormType', [
                    'data_class' => $translationClass,
                    'required' => in_array($locale, $formOptions['required_locales'], true),
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
     * @param string $translatableClass
     *
     * @return string
     */
    private function getTranslationClass($translatableClass)
    {
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
     * @param array  $options
     * @param string $class
     *
     * @return array
     */
    public function getFieldsOptions($options, $class)
    {
        $fieldsOptions = [];

        $fieldsConfig = $this->formManipulator->getFieldsConfig(ClassUtils::getRealClass($class), $options);
        foreach ($fieldsConfig as $fieldName => $fieldConfig) {
            // Simplest case: General options for all locales
            if (!isset($fieldConfig['locale_options'])) {
                foreach ($options['locales'] as $locale) {
                    $fieldsOptions[$locale][$fieldName] = $fieldConfig;
                }

                continue;
            }

            // Custom options by locale
            $localesFieldOptions = $fieldConfig['locale_options'];
            unset($fieldConfig['locale_options']);

            foreach ($options['locales'] as $locale) {
                $localeFieldOptions = isset($localesFieldOptions[$locale]) ? $localesFieldOptions[$locale] : [];
                if (!isset($localeFieldOptions['display']) || (true === $localeFieldOptions['display'])) {
                    $fieldsOptions[$locale][$fieldName] = $localeFieldOptions + $fieldConfig;
                }
            }
        }

        return $fieldsOptions;
    }
}
