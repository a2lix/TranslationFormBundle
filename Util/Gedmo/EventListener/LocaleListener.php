<?php

namespace A2lix\TranslationFormBundle\Util\Gedmo\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface,
    Symfony\Component\HttpKernel\Event\GetResponseEvent,
    Symfony\Component\HttpKernel\KernelEvents,
    Gedmo\Translatable\TranslatableListener;

/**
 * @author David ALLIX
 */
class LocaleListener implements EventSubscriberInterface
{
    private $translatableListener;

    /**
     * @param \Gedmo\Translatable\TranslatableListener $translatableListener
     */
    public function __construct(TranslatableListener $translatableListener)
    {
        $this->translatableListener = $translatableListener;
    }

    /**
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->translatableListener->setTranslatableLocale($event->getRequest()->getLocale());
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', -10),
        );
    }
}
