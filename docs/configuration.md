# Configuration

## Registration

| Framework | Entry point | Provides |
| --- | --- | --- |
| laminas-mvc | `Contenir\View\Module` | `view_helpers` and `view_manager` defaults |
| Mezzio / config aggregators | `Contenir\View\ConfigProvider` | `view_helpers` |

The Laminas component installer adds the right one automatically.

## Keys

```php
return [
    // image(): the CDN serving local images, and its scheme.
    'view_cdn' => [
        'host'   => 'cdn.example.com',
        'method' => 'https',
    ],
    // icon(): a default wrapper class for inlined icons.
    'view_helper_config' => [
        'icon' => ['class' => 'icon'],
    ],
    // settings(): anything the views need.
    'settings' => [
        'site_name' => 'Example',
    ],
];
```

## Services

| Service | Type | Used by |
| --- | --- | --- |
| `ViewCache` | `Laminas\Cache\Storage\StorageInterface` | `cache()` |
| `Application\Acl\Acl` | `Laminas\Permissions\Acl\AclInterface` | `acl()` |

Requesting `cache()` or `acl()` when the service has the wrong type throws
`Laminas\ServiceManager\Exception\ServiceNotCreatedException`.

## `view_manager` defaults (laminas-mvc)

```php
'view_manager' => [
    'display_not_found_reason' => true,
    'display_exceptions'       => true,
    'doctype'                  => 'HTML5',
    'not_found_template'       => 'error/404',
    'forbidden_template'       => 'error/403',
    'exception_template'       => 'error/index',
    'strategies'               => ['ViewJsonStrategy'],
],
```

Override any of them in the application's own configuration.
