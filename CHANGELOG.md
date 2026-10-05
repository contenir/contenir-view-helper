# Changelog

All notable changes to this project are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and the project
adheres to [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Added

- Infection mutation testing in CI, MSI 100%.

## [2.0.0] - Unreleased

The helpers, their aliases and their view-script calls are unchanged. The
major version marks the move to PHP 8.3+, native types throughout and the
php-db QA toolchain shared by all Contenir 2.x packages. See
[UPGRADE-2.0.md](UPGRADE-2.0.md).

### Changed

- Requires PHP 8.3, 8.4 or 8.5. PHP 8.0 to 8.2 are no longer supported.
- Every helper and factory method has native parameter and return types.
  `truncate()`'s `$break_words` parameter is now `$breakWords`.
- Every class is `final`: `Module`, the factories and all 17 helpers. The
  helpers' protected properties and methods are now private. Customise a
  helper by registering your own under the same alias.
- `cache()` requires its storage; it could never be built without one.
- Escaping inside the helpers uses laminas-escaper directly, with the same
  output.
- `laminas/laminas-escaper`, `laminas/laminas-servicemanager` and
  `psr/container`, used directly, are now required.

### Added

- `LICENSE.md` with the BSD-3-Clause text `composer.json` already declared.

- `Contenir\View\ConfigProvider`. composer.json already pointed the Laminas
  component installer at it, but the class did not exist.
- `AclFactory::SERVICE` and `CacheFactory::SERVICE` constants. Both
  factories throw `ServiceNotCreatedException` when the service has the
  wrong type.
- Documentation for every helper in `docs/`.
- Continuous integration on PHP 8.3, 8.4 and 8.5 against lowest, locked and
  latest dependencies, with coverage reported to Codecov.
- Unit and integration test suites, with 100% line and branch coverage.

### Fixed

- `icon()`: the `class` option was ignored (the `tag` option was used as the
  class), and options passed to one call stuck to every later call.
- `icon()` is now built by `IconFactory`, so `view_helper_config.icon.class`
  takes effect. It was registered as an invokable, leaving the factory
  unused.
- `image()`: the placeholder `src` was escaped twice (`&amp;amp;`), so CDNs
  received a parameter named `amp;auto_optimize`. `<picture>` no longer
  carries a double space.
- `socialLink()`: the profile URL is now escaped in `href`.
- `video()`: the Vimeo wrapper class is now escaped.
- `formGroup()`: multi-checkboxes now get no control class, like radios;
  the `multi_checkbox` type check never matched. A group attribute list
  without `class`, or with a list of classes, no longer raises warnings.
- `fileSize()`: binary sizes scaled by decimal digit count, so 1000 to 1023
  bytes showed as `0.98KiB`.
- `truncate()`: `$middle` with an odd length no longer raises a float-to-int
  deprecation.
- `urlFormat()`: an empty URL returns an empty string instead of raising an
  "uninitialized string offset" warning and returning `http://`.
- `srcset()`: a file without an extension no longer raises a warning.
- `dateFormat()`, `escapeEmail()` and `srcset()` no longer pass `null` to
  built-in functions, which PHP 8.1+ deprecates.

### Removed

- `laminas/laminas-coding-standard` and `phpcs.xml`, replaced by Mago via
  `php-db/phpdb-qa-tools`.

## [1.1.2] - 2026-05-12

- `video()` preloads `metadata` by default so autoplay still fires.

## [1.1.1] - 2026-05-12

- `video()` poster and preload options, with a `headLink()` poster preload.

## [1.1.0] - 2026-03-18

- `resourceLink()` helper, accepting decoded arrays as well as JSON.

## [1.0.x] - 2023-04-24 to 2024-10-23

- Initial helpers, then `video()`, `icon()` configuration, PHP 8.1
  compatibility and `formGroup()` fixes.
