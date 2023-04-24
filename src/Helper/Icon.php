<?php

namespace Contenir\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Traversable;

class Icon extends AbstractHelper
{
    protected $options = [
        'class' => 'icon',
        'base_path' => './public/asset/icon',
        'extension' => 'svg'
    ];

    public function __invoke($iconName, Array $options = [])
    {
        $options = array_merge($this->options, $options);

        $iconPath = sprintf(
            '%s/%s.%s',
            $options['base_path'],
            $iconName,
            $options['extension']
        );

        $iconData = file_get_contents($iconPath);

        return sprintf(
            '<i class="%s">%s</i>',
            $options['class'],
            $iconData
        );
    }
}
