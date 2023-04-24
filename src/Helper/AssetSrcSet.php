<?php

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHtmlElement;
use Gumlet\ImageResize;

class AssetSrcset extends AssetSrc
{
    protected $config;
    protected $rootPath;

    public function __construct($config)
    {
        $this->config = $config;
        $this->rootPath = realpath('./public/');
    }

    public function __invoke($filepath = null, $options = null)
    {
        if (! file_exists($this->rootPath . $filepath)) {
            return null;
        }

        $srcset = [];
        $sizes = $options['sizes'] ?? [];

        foreach ($this->generateSrc($filepath, $sizes) as $size => $destFile) {
            $srcset[] = sprintf('%s %sw', $this->encodeUrl($destFile), $size);
        }

        return join(',', $srcset);
    }
}
