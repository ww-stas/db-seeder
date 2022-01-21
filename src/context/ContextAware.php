<?php declare(strict_types=1);

namespace App\Context;

trait ContextAware
{
    private Context $appContext;

    public function setAppContext(Context $appContext): void
    {
        $this->appContext = $appContext;
    }
}
