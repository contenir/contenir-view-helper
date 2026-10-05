# Media helpers

## `image(string $path, array $attributes = [], array $pictureAttributes = []): string`

Renders a lazy-loaded `<picture><img data-lazyload></picture>`.

- **Local paths** are served from the CDN host in `view_cdn.host` (scheme
  `view_cdn.method`, default `https`), or root-relative without a host. The
  `src` is a 20px-wide placeholder (`?width=20&auto_optimize=high`) and
  `data-src` the full image (`?auto_optimize=high`, plus `&width=N` when
  the `resizeWidth` attribute is given; `resizeWidth` is not rendered).
- **Absolute `http(s)` URLs** are used as they are for both.
- `<img>` defaults to `class="img"` and `alt=""`, `<picture>` to
  `class="picture"`; the arrays override them. All attributes are escaped.

## `srcset(?string $filepath = null, array $sizes = []): string`

Builds a `srcset` value for derivatives named
`<dir>/<name>_<width>x[<height>].<ext>`:

```php
<?= $this->srcset('/asset/hero.jpg', [800, '1600x900']) ?>
<!-- /asset/hero_800x.jpg 800w,/asset/hero_1600x900.jpg 1600w -->
```

## `video(string $path, array $options = [], bool $controls = false): string`

- `https://vimeo.com/<id>`: a Vimeo player `<iframe>`, wrapped in a
  `<div class="…">` when `videoWrapperClass` is set.
- `https://www.youtube.com/watch?v=<id>`: a YouTube embed `<iframe>`.
- Anything else, including Vimeo `external` file links: a `<video>`
  element with `playsinline`, and either native controls (`$controls`) or
  `muted autoplay loop`.

| Option | Default | Meaning |
| --- | --- | --- |
| `videoClass` | `video` | Class of the `<iframe>` or `<video>` |
| `videoWrapperClass` | `''` | Wrapper `<div>` class for Vimeo embeds |
| `poster` | `null` | `<video poster>` URL |
| `preloadPoster` | `true` | With a poster, queue `<link rel="preload" as="image" fetchpriority="high">` through `headLink()`, once per URL |
| `preload` | `metadata` | `<video preload>`; `''` omits it |

## `icon(string $iconName, array $options = []): string`

Inlines `<base_path>/<name>.<extension>` (default
`./public/asset/icon/<name>.svg`). With a class, the SVG is wrapped:
`<i class="icon">…</i>`. A missing file renders an empty string.

| Option | Default | Setter |
| --- | --- | --- |
| `tag` | `i` | `setTag()` |
| `class` | `view_helper_config.icon.class`, else none | `setClass()` |
| `base_path` | `./public/asset/icon` | `setBasePath()` |
| `extension` | `svg` | `setExtension()` |

Options apply to that call only; setters change the defaults.
