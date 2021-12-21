<?php declare(strict_types=1);

namespace App\Resolver;

use App\Attributes\Component;
use App\ConnectionDriver;
use App\HasConnection;
use Doctrine\DBAL\Connection;

/**
 * Resolver for the polymorphic relations where is needed
 * to select random model

 */
#[Component]
class OneOfArgumentResolver extends ArgumentResolver
{
    use HasConnection;

    protected function doResolve($context = null)
    {
        
    }
}
