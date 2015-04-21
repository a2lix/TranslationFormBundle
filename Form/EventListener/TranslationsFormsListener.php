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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TranslationsFormsListener implements EventSubscriberInterface
{
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

        $formsOptions = $this->getFormsOptions($form->getConfig()->getOptions());

        foreach ($formsOptions['locales'] as $locale) {
            if (isset($formsOptions[$locale])) {
                $form->add($locale, $formsOptions['form_type'],
                    $formsOptions[$locale] + ['required' => in_array($locale, $formsOptions['required_locales'], true)]
                );
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
     * {@inheritdoc}
     */
    public function getFormsOptions($options)
    {
        $formsOptions = [];

        // Current options
        $formOptions = $options['form_options'];

        // Simplest case: General options for all locales
        if (!isset($formOptions['locale_options'])) {
            foreach ($options['locales'] as $locale) {
                $formsOptions[$locale] = $formOptions;
            }

            return $formsOptions;
        }

        // Custom options by locale
        $localesFormOptions = $formOptions['locale_options'];
        unset($formOptions['locale_options']);

        foreach ($options['locales'] as $locale) {
            $localeFormOptions = isset($localesFormOptions[$locale]) ? $localesFormOptions[$locale] : [];
            if (!isset($localeFormOptions['display']) || $localeFormOptions['display']) {
                $formsOptions[$locale] = $localeFormOptions + $formOptions;
            }
        }

        return $formsOptions;
    }
}
