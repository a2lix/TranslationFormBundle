# A2lix Translation Form Bundle

Translate your doctrine objects easily with some helps

[![Latest Stable Version](https://poser.pugx.org/a2lix/translation-form-bundle/v/stable)](https://packagist.org/packages/a2lix/translation-form-bundle)
[![Latest Unstable Version](https://poser.pugx.org/a2lix/translation-form-bundle/v/unstable)](https://packagist.org/packages/a2lix/translation-form-bundle)
[![License](https://poser.pugx.org/a2lix/translation-form-bundle/license)](https://packagist.org/packages/a2lix/translation-form-bundle)

[![Total Downloads](https://poser.pugx.org/a2lix/translation-form-bundle/downloads)](https://packagist.org/packages/a2lix/translation-form-bundle)
[![Monthly Downloads](https://poser.pugx.org/a2lix/translation-form-bundle/d/monthly)](https://packagist.org/packages/a2lix/translation-form-bundle)
[![Daily Downloads](https://poser.pugx.org/a2lix/translation-form-bundle/d/daily)](https://packagist.org/packages/a2lix/translation-form-bundle)

| Branch | Tools |
| --- | --- |
| 2.x | [![Build Status][v2_ci_badge]][v2_ci_link] [![Coverage Status][v2_coverage_badge]][v2_coverage_link] |
| 3.x (master) | [![Build Status][v3_ci_badge]][v3_ci_link] [![Coverage Status][v3_coverage_badge]][v3_coverage_link] [![SensioLabsInsight][v3_sensioinsight_badge]][v3_sensioinsight_link] |

## Documentation

Check out the documentation on the [official website](http://a2lix.fr/bundles/translation-form).

## Support

* `0.x` & `1.x` are old versions not maintained anymore.
* `2.x` is an old version with low requirements (PHP5.4+/7+, Symfony2.3+/3.0+/4.0+). It is compatible with [Gedmo](https://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/translatable.md), [KnpLabs](https://github.com/KnpLabs/DoctrineBehaviors#translatable), [A2lix](https://github.com/a2lix/I18nDoctrineBundle) and [Prezent](https://github.com/Prezent/doctrine-translatable-bundle)
* `3.x` is a rethinked version that depends on [AutoFormBundle](https://github.com/a2lix/AutoFormBundle) and has higher requirements (PHP7.2+, Symfony3.4+/4.3+/5.0+). It is compatible with [KnpLabs](https://github.com/KnpLabs/DoctrineBehaviors#translatable), [A2lix](https://github.com/a2lix/I18nDoctrineBundle) and [Prezent](https://github.com/Prezent/doctrine-translatable-bundle)

## Contribution help

```
docker run --rm --interactive --tty --volume $PWD:/app --user $(id -u):$(id -g) composer install --ignore-platform-reqs
docker run --rm --interactive --tty --volume $PWD:/app --user $(id -u):$(id -g) composer run-script phpunit
docker run --rm --interactive --tty --volume $PWD:/app --user $(id -u):$(id -g) composer run-script cs-fixer
```

## License

This package is available under the [MIT license](LICENSE).

[v2_ci_badge]: https://github.com/a2lix/TranslationFormBundle/workflows/CI/badge.svg?branch=2.x
[v2_ci_link]: https://github.com/a2lix/TranslationFormBundle/actions?query=workflow%3ACI
[v2_coverage_badge]: https://codecov.io/gh/a2lix/TranslationFormBundle/branch/2.x/graph/badge.svg
[v2_coverage_link]: https://codecov.io/gh/a2lix/TranslationFormBundle/branch/2.x

[v3_ci_badge]: https://github.com/a2lix/TranslationFormBundle/workflows/CI/badge.svg
[v3_ci_link]: https://github.com/a2lix/TranslationFormBundle/actions?query=workflow%3ACI
[v3_coverage_badge]: https://codecov.io/gh/a2lix/TranslationFormBundle/branch/master/graph/badge.svg
[v3_coverage_link]: https://codecov.io/gh/a2lix/TranslationFormBundle/branch/master
[v3_sensioinsight_badge]: https://insight.sensiolabs.com/projects/64aee70e-7b00-406f-8648-f7ea66e29f80/mini.png
[v3_sensioinsight_link]: https://insight.sensiolabs.com/projects/64aee70e-7b00-406f-8648-f7ea66e29f80
