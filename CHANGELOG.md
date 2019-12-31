# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

### Added
- `ValidatorInterface::applyTo()` and `ResultInterface`

### Changed
- Validators are no longer callable
- `Rule` and `ArrayValidator` are now **immutable** and **final**
- Require php 7.4
- `AbstractValidator` rewritten as an extension point for custom validators

## Removed
- `Exception::pushValidatorName()` and `Exception::getSourceValidatorName()`
- `ArrayValidator::addValidator()`

## [2.0.1] - 2017-10-15

### Added
- .gitattributes

## [2.0.0] - 2017-10-15

### Added
- Added ValidatorInterface
- Made validators callable
- Require php 7.1

## [1.0.0] - 2015-12-31
- Initial release
