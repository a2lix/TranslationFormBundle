<?php

/*
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace A2lix\TranslationFormBundle\TranslationForm;

/**
 * @author David ALLIX
 */
interface TranslationFormInterface
{
    /**
     *
     */
    public function getFieldsOptions($class, $options);

    /**
     *
     */
    public function guessMissingFieldOptions($guesser, $class, $property, $options);
}
