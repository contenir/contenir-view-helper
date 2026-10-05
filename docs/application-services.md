# Application services

## `acl(): AclInterface`

Returns the application's ACL, from the `Application\Acl\Acl` service
(`AclFactory::SERVICE`), which must implement
`Laminas\Permissions\Acl\AclInterface`.

```php
<?php if ($this->acl()->isAllowed($role, 'admin')): ?>…<?php endif ?>
```

## `settings(): Laminas\Config\Config`

Returns the `settings` configuration key as a read-only `Config`:

```php
<?= $this->escapeHtml($this->settings()->site_name) ?>
```

## `cache()`

Caches rendered output in the `ViewCache` service, a laminas-cache
`StorageInterface`. Keys are lower-cased, with runs of other characters
collapsed to `_`.

```php
<?= $this->cache('partial/navigation', 'main-nav') ?>  <!-- a whole partial -->

<?php if (! $this->cache()->start('footer')): ?>      <!-- an inline block -->
    …expensive markup…
<?php $this->cache()->end(); endif ?>
```

- `cache($script, $key)` returns the cached output of `$script`, rendering
  and storing it on a miss.
- `start($key)` echoes a cached block and returns `true`, or starts
  capturing and returns `false`; `end()` stores and echoes the captured
  block.
- `cache()` without both arguments returns the helper.
