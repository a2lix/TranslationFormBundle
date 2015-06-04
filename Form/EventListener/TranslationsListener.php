<?php

namespace A2lix\TranslationFormBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent,
    Symfony\Component\Form\FormEvents,
    Symfony\Component\EventDispatcher\EventSubscriberInterface,
    A2lix\TranslationFormBundle\TranslationForm\TranslationForm,
    Doctrine\Common\Annotations\AnnotationReader,
    ReflectionClass;

/**
 * @author David ALLIX
 */
class TranslationsListener implements EventSubscriberInterface
{
    private $translationForm;

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
        $translationClass = $this->getTranslationClass($translatableClass);

        $formOptions = $form->getConfig()->getOptions();
        $fieldsOptions = $this->translationForm->getFieldsOptions($translationClass, $formOptions);

        foreach ($formOptions['locales'] as $locale) {            
            if (isset($fieldsOptions[$locale])) {
                $form->add($locale, 'a2lix_translationsFields', array(
                    'data_class' => $translationClass,
                    'fields' => $fieldsOptions[$locale],
                    'required' => in_array($locale, $formOptions['required_locales'])
                ));
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
            // Remove useless Translation object
            if (!$translation) {
                $data->removeElement($translation);
                
            } else {
                $translation->setLocale($locale);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::SUBMIT => 'submit',
        );
    }
    
    /**
     *
     * @param string $translatableClass
     *
     * @return string
     */
    private function getTranslationClass($translatableClass)
    {
        $reader = new AnnotationReader();
        $reflectionClass = new ReflectionClass($translatableClass);

        // Knp
        if (method_exists($translatableClass, "getTranslationEntityClass")) {
            return $translatableClass::getTranslationEntityClass();
        
        // Gedmo    
        } elseif (method_exists($translatableClass, "getTranslationClass")) {
            return $translatableClass::getTranslationClass();

        // Gedmo Annotations
        } elseif (!is_null($reader->getClassAnnotation($reflectionClass, 'Gedmo\Mapping\Annotation\TranslationEntity'))){
            return $reader->getClassAnnotation($reflectionClass, 'Gedmo\Mapping\Annotation\TranslationEntity')->class;
        }

        return $translatableClass .'Translation';
    }
}
