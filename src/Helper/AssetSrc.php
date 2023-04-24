<?php

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHtmlElement;
use Gumlet\ImageResize;

class AssetSrc extends AbstractHtmlElement
{
    protected $config;
    protected $rootPath;

    public function __construct($config)
    {
        $this->config = $config;
        $this->rootPath = realpath('./public/');
    }

    public function __invoke($filepath = null, $resize = null)
    {
        if (! file_exists($this->rootPath . $filepath)) {
            return null;
        }

        $src = [];

        foreach ($this->generateSrc($filepath, [$resize => $resize]) as $size => $destFile) {
            $src[] = sprintf('%s', $this->getView()->AssetUrl($this->encodeUrl($destFile)));
        }

        return join('', $src);
    }

    protected function generateSrc($filepath, $sizes)
    {
        $parts = @pathinfo($filepath);
        $srcTime = @filemtime($this->rootPath . $filepath);

        if (!$parts['filename']) {
            return false;
        }

        foreach ($sizes as $size => $resize) {
            $dimensions = explode('x', $resize);
            $resizeDimensions = (count($dimensions) > 1) ? $resize : sprintf('%sx', $dimensions[0]);

            $destFile = sprintf(
                "%s/.%s/%s.%s",
                $parts['dirname'],
                $resizeDimensions,
                $parts['filename'],
                $parts['extension']
            );

            $destPath = $this->rootPath . sprintf(
                '%s/.%s',
                $parts['dirname'],
                $resizeDimensions
            );

            yield $size => $destFile;
        }
    }

    protected function encodeUrl($filepath)
    {
        return str_replace(
            [' '],
            ['%20'],
            $filepath
        );
    }
}
