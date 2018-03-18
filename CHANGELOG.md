# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [2.4.1](https://github.com/a2lix/TranslationFormBundle/compare/2.4.0...2.4.1) - 2018-03-18
### Fixed
- Remove call to `setRequest` on the xml configuration fixes scope widening

## [2.4.0](https://github.com/a2lix/TranslationFormBundle/compare/2.3.0...2.4.0) - 2018-03-17
### Fixed
- Scope widening and missing request on certain Symfony versions

### Deprecated
- `TranslatedEntityType::setRequest` is deprecated, consider using `setRequestStack` instead

## [2.3.0](https://github.com/a2lix/TranslationFormBundle/compare/2.2.0...2.3.0) - 2018-03-12
### Added
- Added Symfony 4 support

### Fixed
- Usage of FQCNs for form types to improve SF3 compatibility
- Fix deprecated strict attribute on services configuration
- `TranslatedEntityType` is now compatible with `choice_label` for Symfony >= 2.7
