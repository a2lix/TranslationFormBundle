<?php

namespace A2lix\TranslationFormBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent,
    Symfony\Component\Form\FormEvents,
    Symfony\Component\EventDispatcher\EventSubscriberInterface,
    A2lix\TranslationFormBundle\TranslationForm\TranslationForm;

/**
 * @author David ALLIX
 */
class TranslationsListener implements EventSubscriberInterface
{
    private $translationForm;
    private $unusedLocales = array();

    /**
     *
     * @param \A2lix\TranslationFormBundle\TranslationForm\TranslationForm $translationForm
     */
    public function __construct(TranslationForm $translationForm)
    {
        $this->translationForm = $translationForm;
    }

    /**
     *
     * @param \Symfony\Component\Form\FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();

        $translatableClass = $form->getParent()->getConfig()->getDataClass();
        $translationClass = $translatableClass .'Translation';

        $formOptions = $form->getConfig()->getOptions();
        $fieldsOptions = $this->translationForm->getFieldsOptions($translationClass, $formOptions);

        foreach ($formOptions['locales'] as $locale) {
            if (isset($fieldsOptions[$locale])) {
                $form->add($locale, 'a2lix_translationsFields', array(
                    'data_class' => $translationClass,
                    'fields' => $fieldsOptions[$locale]
                ));
            }
        }
    }
    
    /**
     *
     * @param \Symfony\Component\Form\FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();

        foreach ($data as $locale => $translation) {
            // Detect and trace unused locale
            if (!array_filter($translation)) {
                $this->unusedLocales[$locale] = $locale;
            }
        }
    }
    
    /**
     *
     * @param \Symfony\Component\Form\FormEvent $event
     */
    public function submit(FormEvent $event)
    {
        $data = $event->getData();

        foreach ($data as $locale => $translation) {
            // Remove unused locale if necessary
            if (isset($this->unusedLocales[$locale])) {
                unset($data[$locale]);
                
            } else {
                $translation->setLocale($locale);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
            FormEvents::SUBMIT => 'submit',
        );
    }
}