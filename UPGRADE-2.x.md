UPGRADE 2.x
===========

UPGRADE FROM 2.3 to 2.4
=======================

## Deprecated `TranslatedEntityType::setRequest`

If you are extending `TranslatedEntityType`, you should know that
`setRequest` is deprecated and will be removed on next major. Instead
you might use `setRequestStack` unless you are using SymfonyF 2.3
(In this case we recommend updating to atleast Symfony 2.8)
