<?php

namespace Faucon\Bundle\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FauconUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
