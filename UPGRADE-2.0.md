# Upgrading from 1.x to 2.0

2.0 registers the same helpers under the same aliases, and view scripts that
call them positionally need no changes. The breaks affect the platform,
named arguments, and code that extends or constructs the classes.

| | 1.x | 2.0 |
| --- | --- | --- |
| PHP | ^8.0 | 8.3, 8.4 or 8.5 |
| laminas-servicemanager | (undeclared) | ^3.22 or ^4.0 |
| laminas-escaper | (undeclared) | ^2.13 |

```bash
composer require contenir/contenir-view-helper:^2.0
```

Projects that must stay on PHP 8.0 to 8.2 can keep using `^1.1`, maintained
on the `1.x` branch.

## Native types

Every public and protected method now declares its types. Calls from view
scripts (which do not declare `strict_types`) keep coercing scalars, but
arguments of the wrong kind now throw a `TypeError` instead of a warning:

| Helper | 2.0 signature |
| --- | --- |
| `Cache` | `__invoke(?string $script = null, int\|string\|null $key = null): self\|string`, `start(int\|string $key): bool` |
| `DateFormat` | `__invoke(?string $datetime = null, ?string $format = null): ?string` |
| `EscapeEmail` | `__invoke(string $email, bool $mailto = false): string` |
| `FileSize` | `__invoke(int\|float\|string $bytes, int $decimals = 2, string $system = self::SYSTEM_METRIC): string` |
| `FileType` | `__invoke(?string $mimeType): string` |
| `Icon` | `__invoke(string $iconName, array $options = []): string`; setters take `string` (`setClass(?string)`) |
| `Image` | `__construct(array $options = [])`, `__invoke(string $path, …): string` |
| `ResourceLink` | `__invoke(string\|array\|null $value = null, ?string $defaultCta = null): array` |
| `RichContent` | `__invoke(string\|Stringable\|null $content, array $template = []): string` |
| `SocialLink` | `__invoke(string $socialId, ?string $link, array $options = []): string` |
| `Srcset` | `__invoke(?string $filepath = null, array $sizes = []): string` |
| `Truncate` | `__invoke(string\|int\|float\|Stringable\|null $value, int $length = 125, bool $strip = false, string $etc = '...', bool $breakWords = false, bool $middle = false): string` |
| `UrlFormat` | `__invoke(?string $url = null, ?string $format = null): string` |
| `Video` | `__invoke(string $path, array $options = [], bool $controls = false): string` |

Return types narrowed: `Icon` (`bool|string` to `string`), `Truncate` and
`UrlFormat` (`array|string|null` to `string`), and
`FormGroup::getNormalisedId()` (`array|string|null` to `string`).

### Subclasses

Subclasses that override a method must match the new signatures. The
protected methods changed as follows:

```php
// 1.x
protected function formatSection($section, $template): string;           // RichContent
protected function formatWrapper($html, $class, $tag): string;           // RichContent
protected function renderVideoElement($view, string $videoClass, string $src, bool $controls, array $options): string;
protected function injectPosterPreload($view, string $poster): void;     // Video
protected function parsePath($path): array;                              // Video
protected function getNormalisedId($id): array|string|null;              // FormGroup

// 2.0
protected function formatSection(string $section, array $template): string;
protected function formatWrapper(string $html, string $class, string $tag): string;
protected function renderVideoElement(PhpRenderer $view, string $videoClass, string $src, bool $controls, array $options): string;
protected function injectPosterPreload(PhpRenderer $view, string $poster): void;
protected function parsePath(string $path): array;                       // always has a 'path' key
protected function getNormalisedId(string $id): string;
```

`UrlFormat`'s protected `$_format` property is renamed `$format`:

```php
// 1.x
class MyUrlFormat extends UrlFormat { protected string $_format = '%host%%path%'; }

// 2.0
class MyUrlFormat extends UrlFormat { protected string $format = '%host%%path%'; }
```

## `truncate()` named argument

```php
// 1.x
$this->truncate($text, 80, break_words: true);

// 2.0
$this->truncate($text, 80, breakWords: true);
```

## `Cache` needs its storage

```php
// 1.x: accepted, then failed inside laminas-cache with a TypeError
new Cache();

// 2.0
new Cache($storage);
```

## Final wiring classes

`Module`, `AclFactory`, `CacheFactory`, `IconFactory`, `ImageFactory` and
`SettingsFactory` are `final`. Decorate or replace a factory in your
`view_helpers` configuration instead of extending it:

```php
// 1.x
class MyImageFactory extends ImageFactory { /* … */ }

// 2.0
final class MyImageFactory
{
    public function __invoke(ContainerInterface $container): Image
    {
        return new MyImage(/* … */);
    }
}
```

`Module::getConfig()` now declares an `array` return type.

## Behaviour changes

These are bug fixes, listed because output changes:

- **`icon()`** honours the `class` option, applies options to that call
  only, and applies `view_helper_config.icon.class` (it is now built by
  `IconFactory`). Sites that relied on an option sticking to later calls
  should call the setters instead.
- **`image()`** emits `?width=20&amp;auto_optimize=high` in `src` (it was
  double-escaped) and `<picture class=…>` with a single space.
- **`formGroup()`** no longer gives multi-checkbox inputs the
  `form__control` class, matching radios.
- **`fileSize()`** in the binary system scales by 1024 (1000 bytes is
  `1000B`, not `0.98KiB`).
- **`urlFormat('')`** and **`urlFormat(null)`** return `''` instead of
  `http://`.
- **`socialLink()`** HTML-escapes the profile URL in `href`, and
  **`video()`** the Vimeo wrapper class.
- **`cache()`** and **`acl()`** throw `ServiceNotCreatedException` when the
  `ViewCache` or `Application\Acl\Acl` service has the wrong type, instead
  of a `TypeError`.
