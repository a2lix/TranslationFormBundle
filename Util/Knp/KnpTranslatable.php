<?php

/*
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\Util\Knp;

/**
 * Translatable trait.
 *
 * Should be used inside entity, that needs to be translated.
 */
trait KnpTranslatable
{
    /**
     * @Symfony\Component\Validator\Constraints\Valid
     */
    protected $translations;
    private $newTranslations;
    private $currentLocale;

    use \Knp\DoctrineBehaviors\Model\Translatable\TranslatableMethods;

    /**
     * @param type $method
     * @param type $arguments
     *
     * @return type
     */
    public function __call($method, $args)
    {
        if (!method_exists(self::getTranslationEntityClass(), $method)) {
            $method = 'get'.ucfirst($method);
        }

        return $this->proxyCurrentLocaleTranslation($method, $args);
    }
}
