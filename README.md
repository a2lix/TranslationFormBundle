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
| 3.x (master) | [![Build Status][v3_ci_badge]][v3_ci_link] [![Coverage Status][v3_coverage_badge]][v3_coverage_link] |

## Documentation

Check out the documentation on the [official website](http://a2lix.fr/bundles/translation-form).

## Support

* `3.x` depends on [AutoFormBundle](https://github.com/a2lix/AutoFormBundle) and has higher requirements (PHP8.1+, Symfony5.4+/6.3+/7.0+). It is compatible with [KnpLabs](https://github.com/KnpLabs/DoctrineBehaviors#translatable), [A2lix](https://github.com/a2lix/I18nDoctrineBundle) and [Prezent](https://github.com/Prezent/doctrine-translatable-bundle)

## Contribution help

```
docker run --rm --interactive --tty --volume $PWD:/app --user $(id -u):$(id -g) composer install --ignore-platform-reqs
docker run --rm --interactive --tty --volume $PWD:/app --user $(id -u):$(id -g) composer run-script phpunit
docker run --rm --interactive --tty --volume $PWD:/app --user $(id -u):$(id -g) composer run-script cs-fixer
```

## License

This package is available under the [MIT license](LICENSE).

[v3_ci_badge]: https://github.com/a2lix/TranslationFormBundle/actions/workflows/ci.yml/badge.svg
[v3_ci_link]: https://github.com/a2lix/TranslationFormBundle/actions/workflows/ci.yml
[v3_coverage_badge]: https://codecov.io/gh/a2lix/TranslationFormBundle/branch/master/graph/badge.svg
[v3_coverage_link]: https://codecov.io/gh/a2lix/TranslationFormBundle/branch/master
