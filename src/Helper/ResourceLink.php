<?php

declare(strict_types=1);

namespace Contenir\View\Helper;

use Laminas\View\Exception\RuntimeException;
use Laminas\View\Helper\AbstractHelper;

use function array_filter;
use function array_map;
use function array_values;
use function in_array;
use function is_array;
use function is_object;
use function is_string;
use function json_decode;

/**
 * Normalises a CMS link field into a list of link objects. The value is
 * either a plain URL, a JSON list of items, or that list already decoded;
 * each item carries its link under "fields" (url, cta, target).
 *
 * @api
 */
class ResourceLink extends AbstractHelper
{
    use PHPViewTrait;

    protected string $defaultCta = 'Find out more';

    private static function optionalString(mixed $value): ?string
    {
        return is_string($value) ? $value : null;
    }

    /**
     * @return object{url: string, cta: string, target: string|null}
     *
     * @throws RuntimeException when the helper is not attached to a PhpRenderer.
     */
    protected function createLink(string $url, ?string $cta = null, ?string $target = null): object
    {
        return (object) [
            'url'    => $this->getPHPView()->plugin(UrlFormat::class)($url),
            'cta'    => in_array($cta, [null, ''], strict: true) ? $this->defaultCta : $cta,
            'target' => in_array($target, [null, ''], strict: true) ? null : $target,
        ];
    }

    /**
     * @param array<array-key, mixed> $items
     *
     * @return list<object{url: string, cta: string, target: string|null}>
     *
     * @throws RuntimeException when the helper is not attached to a PhpRenderer.
     */
    protected function parseJson(array $items): array
    {
        return array_values(array_filter(array_map(
            /** @throws RuntimeException */
            fn(mixed $item): ?object => is_object($item) ? $this->fromFields($item->fields ?? null) : null,
            $items,
        )));
    }

    /**
     * @return object{url: string, cta: string, target: string|null}|null
     *
     * @throws RuntimeException when the helper is not attached to a PhpRenderer.
     */
    private function fromFields(mixed $fields): ?object
    {
        if (! is_object($fields) || ! is_string($fields->url ?? null) || '' === $fields->url) {
            return null;
        }

        return $this->createLink(
            $fields->url,
            self::optionalString($fields->cta ?? null),
            self::optionalString(
                $fields->target ?? null,
            ),
        );
    }

    /**
     * @return list<object{url: string, cta: string, target: string|null}>
     *
     * @throws RuntimeException when the helper is not attached to a PhpRenderer.
     */
    private function fromString(string $value, mixed $decoded): array
    {
        return is_array($decoded) ? $this->parseJson($decoded) : [$this->createLink($value)];
    }

    /**
     * @param string|array<array-key, mixed>|null $value
     * @param string|null                         $defaultCta Replaces the helper's default call to action,
     *                                                        for this and later calls.
     *
     * @return list<object{url: string, cta: string, target: string|null}>
     *
     * @throws RuntimeException when the helper is not attached to a PhpRenderer.
     */
    public function __invoke(string|array|null $value = null, ?string $defaultCta = null): array
    {
        $this->defaultCta = $defaultCta ?? $this->defaultCta;

        if (in_array($value, [null, '', []], strict: true)) {
            return [];
        }

        if (is_string($value)) {
            return $this->fromString($value, json_decode($value, associative: false));
        }

        return $this->parseJson($value);
    }
}
