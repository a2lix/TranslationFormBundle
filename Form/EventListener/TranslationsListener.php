<?php

namespace A2lix\TranslationFormBundle\Form\EventListener;

use A2lix\TranslationFormBundle\TranslationForm\TranslationForm;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * @author David ALLIX
 */
class TranslationsListener implements EventSubscriberInterface
{
    /**
     * @var TranslationForm
     */
    protected $translationForm;

    /**
     * Constructor
     *
     * @param \A2lix\TranslationFormBundle\TranslationForm\TranslationForm $translationForm
     */
    public function __construct(TranslationForm $translationForm)
    {
        $this->translationForm = $translationForm;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
            FormEvents::SUBMIT => 'submit',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();

        $translatableClass = $this->getTranslatableClass($form);
        $translationClass = $this->getTranslationClass($translatableClass);

        $formOptions = $this->getFormOptions($form);
        $fieldsOptions = $this->translationForm->getFieldsOptions($translationClass, $formOptions);

        foreach ($formOptions['locales'] as $locale) {
            if (isset($fieldsOptions[$locale])) {
                $this->addField($form, $locale, $translationClass, $fieldsOptions, $formOptions);
            }
        }
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $viewData = $form->getViewData();

        $formOptions = $this->getFormOptions($form);
        $translatableClass = $this->getTranslatableClass($form);
        $translationClass = $this->getTranslationClass($translatableClass);
        $fieldsOptions = $this->translationForm->getFieldsOptions($translationClass, $formOptions);

        foreach ($viewData as $locale => $translation) {
            if (!$translation && !in_array($locale, $formOptions['required_locales'])) {
                $this->unsetFieldConstraints($locale, $fieldsOptions);
                $this->addField($form, $locale, $translationClass, $fieldsOptions, $formOptions);
            }
        }
    }

    /**
     * @param \Symfony\Component\Form\FormEvent $event
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
     * @return string
     */
    protected function getTranslatableClass(FormInterface $form)
    {
        return $form->getParent()->getConfig()->getDataClass();
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    protected function getFormOptions(FormInterface $form)
    {
        return $form->getConfig()->getOptions();
    }

    /**
     * @param FormInterface $form
     * @param string $locale
     * @param $translationClass
     * @param array $fieldsOptions
     * @param array $formOptions
     */
    protected function addField(FormInterface $form, $locale, $translationClass, array $fieldsOptions, array $formOptions)
    {
        $form
            ->add(
                $locale,
                'a2lix_translationsFields',
                array(
                    'data_class' => $translationClass,
                    'fields' => $fieldsOptions[$locale],
                    'required' => in_array($locale, $formOptions['required_locales'])
                )
            )
        ;
    }

    /**
     * @param string $locale
     * @param array $fieldsOptions
     */
    protected function unsetFieldConstraints($locale, array & $fieldsOptions)
    {
        foreach ($fieldsOptions[$locale] as $option => $subOptions) {
            if (is_array($subOptions) && array_key_exists('constraints', $subOptions)) {
                unset($fieldsOptions[$locale][$option]['constraints']);
            }
        }
    }

    /**
     * @param string $translatableClass
     * @return string
     */
    protected function getTranslationClass($translatableClass)
    {
        // Knp
        if (method_exists($translatableClass, "getTranslationEntityClass")) {
            return $translatableClass::getTranslationEntityClass();

            // Gedmo
        } elseif (method_exists($translatableClass, "getTranslationClass")) {
            return $translatableClass::getTranslationClass();
        }

        return $translatableClass . 'Translation';
    }
}
