# Text helpers

## `dateFormat(?string $datetime = null, ?string $format = null): ?string`

Parses `$datetime` with `DateTime` and formats it with a `date()` format,
`d M Y` by default. `null` means now. An unparseable string returns `null`.

```php
<?= $this->dateFormat('2024-03-05') ?>            <!-- 05 Mar 2024 -->
<?= $this->dateFormat($entry->date, 'j F Y') ?>  <!-- 5 March 2024 -->
```

## `truncate($value, int $length = 125, bool $strip = false, string $etc = '...', bool $breakWords = false, bool $middle = false): string`

Shortens text to at most `$length` characters, including `$etc`.

- Headings (`<h2>Title</h2>`) become sentences (`Title. `), and `<br>` and
  line breaks become spaces, before measuring.
- `$strip` removes HTML tags first.
- By default the cut is at the previous word boundary; `$breakWords` cuts
  mid-word.
- `$middle` keeps the start and end and puts `$etc` in the middle.
- A `$length` of `0` returns an empty string.

`$value` may be a string, number, `Stringable` or `null`. The output is not
escaped: escape it for HTML unless the text is trusted markup.

## `fileSize(int|float|string $bytes, int $decimals = 2, string $system = FileSize::SYSTEM_METRIC): string`

| Call | Output |
| --- | --- |
| `fileSize(1500000)` | `1.50MB` |
| `fileSize(1000)` | `1kB` (whole values drop their decimals) |
| `fileSize(1536, 1, FileSize::SYSTEM_BINARY)` | `1.5KiB` |

Metric scales by 1000 (`B`, `kB`, `MB` … `YB`), binary by 1024 (`B`, `KiB`,
`MiB` … `YiB`). Any other `$system` scales by 1000 and omits the unit.

## `fileType(?string $mimeType): string`

`PDF` for `application/pdf`, otherwise `Document`.

## `richContent($content, array $template = []): string`

Lays out rich-text (WYSIWYG) content in grid sections. Editors insert
marker paragraphs:

- an element containing `__SECTION__` (for example `<p>__SECTION__</p>`)
  starts a new section;
- an element containing `__COL__` starts a new column within a section.

A section with one column is output as it is. A section with several is
wrapped:

```html
<section class="grid grid--content">
    <div class="grid__col grid__col--2">…</div>
    <div class="grid__col grid__col--2">…</div>
</section>
```

Override the template per call (or in a subclass, through the protected
`$template` property):

| Key | Default | Meaning |
| --- | --- | --- |
| `outerTag`, `outerClass` | `section`, `grid grid--content` | Section wrapper |
| `innerTag`, `innerClass` | `''`, `''` | Optional inner wrapper; an empty tag omits it |
| `columnTag` | `div` | Column wrapper; empty omits it |
| `columnClass` | `[2 => 'grid__col grid__col--2', 3 => …, 4 => …, 'default' => 'grid__col']` | Column class by column count |

Classes are HTML-escaped; tags and content are not.
