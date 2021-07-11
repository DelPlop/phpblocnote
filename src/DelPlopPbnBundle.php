<?php

namespace DelPlop\PbnBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class DelPlopPbnBundle extends Bundle
{
    public function getParent(): string
    {
        return 'DelPlopUserBundle';
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
