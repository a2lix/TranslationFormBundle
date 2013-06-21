A2lixTranslationFormBundle

* If you use symfony 2.2 or less, configure your composer.json with:
"a2lix/translation-form-bundle": "0.*@dev"

--> Documentation: https://github.com/a2lix/TranslationFormBundle/blob/0.x/README.md

* If you use symfony 2.3, configure your composer.json with :
"a2lix/translation-form-bundle": "1.*@dev"

--> New Documentation: http://a2lix.fr/bundles/translation-form/


The version 1.x of this bundle doesn't have anymore the Gedmo translation strategy as default.
The KnpLabs or other custom IndexBy similar strategies are better.

So, if you want continue to use the Gedmo strategy, you have to use "a2lix_translations_gedmo" instead of "a2lix_translations" in your forms
