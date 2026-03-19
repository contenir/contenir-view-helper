<?php

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class ResourceLink extends AbstractHelper
{
    use PHPViewTrait;

    protected string $defaultCta = 'Find out more';

    public function __invoke(null|string|array $value = null, string $defaultCta = null): array
    {
        if ($defaultCta !== null) {
            $this->defaultCta = $defaultCta;
        }

        if (empty($value)) {
            return [];
        }

        if (is_array($value)) {
            return $this->parseJson($value);
        }

        $decoded = json_decode($value, false);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $this->parseJson($decoded);
        }

        return [$this->createLink($value)];
    }

    protected function parseJson(array $items): array
    {
        $links = [];

        foreach ($items as $item) {
            $fields = $item->fields ?? null;

            if ($fields === null || empty($fields->url ?? null)) {
                continue;
            }

            $links[] = $this->createLink(
                $fields->url,
                $fields->cta ?? null,
                $fields->target ?? null
            );
        }

        return $links;
    }

    protected function createLink(string $url, ?string $cta = null, ?string $target = null): object
    {
        $view = $this->getPHPView();

        return (object) [
            'url'    => $view->UrlFormat($url),
            'cta'    => $cta ?: $this->defaultCta,
            'target' => $target ?: null,
        ];
    }
}