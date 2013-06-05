<?php

namespace A2lix\TranslationFormBundle\Tools;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Translatable trait.
 *
 * Should be used inside entity, that needs to be translated.
 */
trait KnpTranslatable
{
    /**
     * @Assert\Valid(deep=true)
     */
    private $translations;
    private $newTranslations;
    private $currentLocale;

    use \Knp\DoctrineBehaviors\Model\Translatable\TranslatableMethods;

    /**
     *
     * @param type $method
     * @param type $arguments
     * @return type
     */
    public function __call($methodOrProperty, $arguments)
    {
        if (!method_exists(self::getTranslationEntityClass(), $methodOrProperty)) {
            $methodOrProperty = 'get'. ucfirst($methodOrProperty);
        }

        return $this->proxyCurrentLocaleTranslation($methodOrProperty, $arguments);
    }
}
