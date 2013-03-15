<?php

namespace A2lix\TranslationFormBundle\Aop;

use CG\Proxy\MethodInvocation,
    CG\Proxy\MethodInterceptorInterface;
use Gedmo\Translatable\TranslatableListener;

/**
 * @author David ALLIX
 */
class TranslationInterceptor implements MethodInterceptorInterface
{
    private $translatableListener;

    /**
     * @param TranslatableListener $translatableListener
     */
    public function __construct(TranslatableListener $translatableListener)
    {
        $this->translatableListener = $translatableListener;
    }

    public function intercept(MethodInvocation $invocation)
    {
        // Force default locale
        $this->translatableListener->setTranslatableLocale($this->translatableListener->getDefaultLocale());

        return $invocation->proceed();
    }
}