<?php declare(strict_types=1);

namespace App\Context;

interface ContextAwareInterface
{
    public function setAppContext(Context $context);
}
