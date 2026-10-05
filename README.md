# contenir/contenir-view-helper

[![Continuous Integration](https://github.com/contenir/contenir-view-helper/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/contenir/contenir-view-helper/actions/workflows/continuous-integration.yml)
[![codecov](https://codecov.io/gh/contenir/contenir-view-helper/graph/badge.svg)](https://codecov.io/gh/contenir/contenir-view-helper)

laminas-view helpers shared by [Contenir CMS](https://contenir.com.au) sites:
text formatting, links, media embeds, BEM form groups, and access to the
application's ACL, settings and view cache from view scripts.

## Requirements

- PHP 8.3, 8.4 or 8.5
- laminas-view 2.35+, laminas-form 3.21+, laminas-cache 3.12+
- `laminas/laminas-i18n` when you use `formGroup()`, as the laminas-form view
  helpers it renders through need it

The 1.x releases, which support PHP 8.0 to 8.2, remain available from the
`1.x` branch and `v1.*` tags; see [UPGRADE-2.0.md](UPGRADE-2.0.md).

## Installation

```bash
composer require contenir/contenir-view-helper
```

With the Laminas component installer, the `Contenir\View` module (laminas-mvc)
or `Contenir\View\ConfigProvider` (Mezzio) registers itself. Otherwise add
`Contenir\View` to `config/modules.config.php`, or the `ConfigProvider` to
your config aggregator.

## Usage

Every helper is registered under its class name and a camelCase and
PascalCase alias, so view scripts call `$this->truncate(...)` or
`$this->Truncate(...)`.

| Helper | Purpose | Docs |
| --- | --- | --- |
| `dateFormat($date = null, $format = null)` | Format a date string (`d M Y` by default); `null` when unparseable | [Text](docs/text.md) |
| `truncate($text, $length = 125, $strip = false, $etc = '...', $breakWords = false, $middle = false)` | Shorten text at a word boundary | [Text](docs/text.md) |
| `fileSize($bytes, $decimals = 2, $system = FileSize::SYSTEM_METRIC)` | `1.5MB`, or `1.5MiB` in the binary system | [Text](docs/text.md) |
| `fileType($mimeType)` | `PDF` or `Document` | [Text](docs/text.md) |
| `richContent($html, $template = [])` | Lay out `__SECTION__` / `__COL__` markers as grid sections and columns | [Text](docs/text.md) |
| `urlFormat($url = null, $format = null)` | Normalise a URL and render chosen parts | [Links](docs/links.md) |
| `resourceLink($value = null, $defaultCta = null)` | Normalise a CMS link field into link objects | [Links](docs/links.md) |
| `socialLink($network, $handleOrUrl, $options = [])` | Link to a social network profile | [Links](docs/links.md) |
| `escapeEmail($email, $mailto = false)` | Obfuscate an email address against harvesters | [Links](docs/links.md) |
| `image($path, $attributes = [], $pictureAttributes = [])` | Lazy-loaded `<picture>` served from a CDN | [Media](docs/media.md) |
| `srcset($path, $sizes = [])` | `srcset` value for pre-resized derivatives | [Media](docs/media.md) |
| `video($path, $options = [], $controls = false)` | Vimeo/YouTube embed or `<video>` element | [Media](docs/media.md) |
| `icon($name, $options = [])` | Inline an SVG icon | [Media](docs/media.md) |
| `formGroup($element, $displayAttributes = [], $groupAttributes = [...])` | Render a laminas-form element as a BEM form group | [Forms](docs/forms.md) |
| `acl()`, `settings()`, `cache()` | The application's ACL, settings and view cache | [Application services](docs/application-services.md) |

```php
<h2><?= $this->escapeHtml($this->truncate($entry->title, 60)) ?></h2>
<time><?= $this->dateFormat($entry->published) ?></time>
<?= $this->image($entry->image, ['alt' => $entry->title, 'resizeWidth' => 800]) ?>
<?php foreach ($this->resourceLink($entry->links) as $link): ?>
    <a href="<?= $this->escapeHtmlAttr($link->url) ?>"><?= $this->escapeHtml($link->cta) ?></a>
<?php endforeach ?>
```

## Configuration

| Key | Used by | Default |
| --- | --- | --- |
| `view_cdn.host`, `view_cdn.method` | `image()` | no host; `https` |
| `view_helper_config.icon.class` | `icon()` default wrapper class | none (icon output bare) |
| `settings` | `settings()` | `[]` |
| service `ViewCache` (a laminas-cache `StorageInterface`) | `cache()` | required to use `cache()` |
| service `Application\Acl\Acl` (an `AclInterface`) | `acl()` | required to use `acl()` |

The laminas-mvc `Module` also sets shared `view_manager` defaults: an HTML5
doctype, `error/404`, `error/403` and `error/index` templates, and the JSON
view strategy. See [Configuration](docs/configuration.md).

## Development

The QA toolchain is [php-db/phpdb-qa-tools](https://github.com/php-db/phpdb-qa-tools).
[Mago](https://mago.carthage.software/) is a standalone binary, installed
separately (`brew install mago`).

```bash
composer check             # everything below
composer cs-check          # mago format --check && mago lint
composer static-analysis   # mago analyze
composer test              # unit suite: helpers and factories with doubled collaborators, no I/O
composer test-integration  # integration suite: a real PhpRenderer and plugin manager, temp-directory files
composer test-coverage     # both suites, clover.xml for Codecov
```

## License

BSD-3-Clause. See [LICENSE.md](LICENSE.md).
