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

use A2lix\AutoFormBundle\Form\Type\AutoType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class TranslationsListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            // FormEvents::SUBMIT => 'submit',
        ];
    }

    public function preSetData(FormEvent $event): void
    {
        $form = $event->getForm();
        $formOptions = $form->getConfig()->getOptions();
        $translationClass = $this->getTranslationClass($form->getParent());

        foreach ($formOptions['locales'] as $locale) {
            $form->add($locale, AutoType::class, [
                'data_class' => $translationClass,
                'label' => $formOptions['locale_labels'][$locale] ?? null,
                'required' => \in_array($locale, $formOptions['required_locales'], true),
                'block_name' => ('field' === $formOptions['theming_granularity']) ? 'locale' : null,
                'children_excluded' => ['id', 'translatable', 'locale'],
            ]);
        }
    }

    // public function submit(FormEvent $event): void
    // {
    //     $form = $event->getForm();
    //     $formOptions = $form->getConfig()->getOptions();

    //     $data = $event->getData();

    //     foreach ($data as $locale => $translation) {
    //         // Remove useless Translation object
    //         if ((method_exists($translation, 'isEmpty') && $translation->isEmpty() && !\in_array($locale, $formOptions['required_locales'], true)) // Knp
    //             || empty($translation) // Default
    //         ) {
    //             $data->removeElement($translation);

    //             continue;
    //         }

    //         $translation->setLocale($locale);
    //     }
    // }

    private function getTranslationClass(FormInterface $form): string
    {
        do {
            $translatableClass = $form->getConfig()->getDataClass();
        } while ((null === $translatableClass) && $form->getConfig()->getInheritData() && (null !== $form = $form->getParent()));

        // Knp
        if (method_exists($translatableClass, 'getTranslationEntityClass')) {
            return $translatableClass::getTranslationEntityClass();
        }

        return $translatableClass.'Translation';
    }
}
