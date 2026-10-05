# Link helpers

## `urlFormat(?string $url = null, ?string $format = null): string`

Normalises a URL and renders it through a format of `%part%` placeholders.
The default format is `%scheme%%host%%path%`, which drops the port, query
and fragment.

| Placeholder | Renders |
| --- | --- |
| `%scheme%` | `https://` (the scheme and `://`) |
| `%userinfo%` | `user:pass` |
| `%host%` | `www.example.com` |
| `%port%` | `8080`, including a scheme's default port |
| `%path%` | `/about` |
| `%query%` | `?q=1` |
| `%fragment%` | `#top` |

Unknown placeholders render empty.

- A root-relative URL (`/about`) is made absolute with the `serverUrl()`
  helper.
- A URL without a scheme (`example.org/page`) is assumed to be `http://`.
- An empty or `null` URL, or one laminas-uri cannot handle (for example an
  unsupported scheme or an invalid host), returns an empty string.

## `resourceLink(string|array|null $value = null, ?string $defaultCta = null): array`

Normalises a CMS link field into a list of link objects with `url`, `cta`
and `target` properties.

- A plain string is a single URL.
- A JSON list, or the already-decoded list, holds items whose `fields`
  object carries `url`, `cta` and `target`. Items without a URL are skipped.
- URLs go through `urlFormat()`. An empty `cta` falls back to the default
  call to action (`Find out more`); an empty `target` becomes `null`.
- `$defaultCta` replaces the helper's default for this and later calls.

```php
<?php foreach ($this->resourceLink($block->links, 'Read more') as $link): ?>
    <a href="<?= $this->escapeHtmlAttr($link->url) ?>"
       <?= $link->target ? 'target="' . $this->escapeHtmlAttr($link->target) . '"' : '' ?>>
        <?= $this->escapeHtml($link->cta) ?>
    </a>
<?php endforeach ?>
```

## `socialLink(string $socialId, ?string $link, array $options = []): string`

Renders a profile link for `instagram`, `behance`, `linkedin`, `facebook`,
`issuu`, `medium`, `flickr`, `vimeo` or `youtube`. A `social_` prefix (as in
settings keys like `social_instagram`) is ignored, and an unknown network
renders an empty string. `$link` may be a handle (`contenir`) or a full
profile URL; the network's URL prefix is stripped and re-applied.

| Option | Default | Meaning |
| --- | --- | --- |
| `link_class` | `navbar__link` | The link's class |
| `icon` | `false` | Inline `./public/asset/icon/icon-<network>.svg` instead of the network's name |

The networks live in the protected `$socialList` property, which a subclass
can extend.

## `escapeEmail(string $email, bool $mailto = false): string`

Obfuscates an address against simple harvesters. For display, each
character becomes a hex entity separated by HTML comments; with `$mailto`,
it is percent-encoded behind an entity-encoded `mailto:`.

```php
<a href="<?= $this->escapeEmail($email, true) ?>"><?= $this->escapeEmail($email) ?></a>
```
