<?php

declare(strict_types=1);

/*
 * This file is part of the TranslationFormBundle package.
 *
 * (c) David ALLIX <http://a2lix.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Form\EventListener;

use A2lix\AutoFormBundle\Form\Manipulator\FormManipulatorInterface;
use A2lix\AutoFormBundle\Form\Type\AutoFormType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class TranslationsListener implements EventSubscriberInterface
{
    /** @var FormManipulatorInterface */
    private $formManipulator;

    public function __construct(FormManipulatorInterface $formManipulator)
    {
        $this->formManipulator = $formManipulator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::SUBMIT => 'submit',
        ];
    }

    public function preSetData(FormEvent $event): void
    {
        $form = $event->getForm();

        if (null === $formParent = $form->getParent()) {
            throw new \RuntimeException('Parent form missing');
        }

        $formOptions = $form->getConfig()->getOptions();
        $fieldsOptions = $this->getFieldsOptions($form, $formOptions);
        $translationClass = $this->getTranslationClass($formParent);

        foreach ($formOptions['locales'] as $locale) {
            if (!isset($fieldsOptions[$locale])) {
                continue;
            }

            $form->add($locale, AutoFormType::class, [
                'data_class' => $translationClass,
                'required' => in_array($locale, $formOptions['required_locales'], true),
                'block_name' => ('field' === $formOptions['theming_granularity']) ? 'locale' : null,
                'fields' => $fieldsOptions[$locale],
                'excluded_fields' => $formOptions['excluded_fields'],
            ]);
        }
    }

    public function submit(FormEvent $event): void
    {
        $data = $event->getData();

        foreach ($data as $locale => $translation) {
            // Remove useless Translation object
            if (empty($translation)) {
                $data->removeElement($translation);
                continue;
            }

            $translation->setLocale($locale);
        }
    }

    public function getFieldsOptions(FormInterface $form, array $formOptions): array
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

    private function getTranslationClass(FormInterface $form): string
    {
        do {
            $translatableClass = $form->getConfig()->getDataClass();
        } while ((null === $translatableClass) && $form->getConfig()->getInheritData() && (null !== $form = $form->getParent()));

        // Knp
        if (method_exists($translatableClass, 'getTranslationEntityClass')) {
            return $translatableClass::getTranslationEntityClass();
        }

        // Gedmo
        if (method_exists($translatableClass, 'getTranslationClass')) {
            return $translatableClass::getTranslationClass();
        }

        return $translatableClass.'Translation';
    }
}
