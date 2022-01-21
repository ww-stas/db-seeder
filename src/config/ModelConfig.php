<?php declare(strict_types=1);

namespace App\Config;

use App\Attributes\Collection;
use App\Attributes\Constructor;
use App\Attributes\Required;
use App\Context\Dependent;
use App\DependencyCollection;
use App\Resolver\ArgumentResolver;
use App\YamlConfigurable;

class ModelConfig implements YamlConfigurable
{
    #[Required]
    public string $table;
    /**
     * @var ArgumentResolver[]
     */
    #[Required]
    #[Collection(class: ArgumentResolver::class)]
    #[Constructor(value: Constructor::STATIC_MAKE)]
    public array $columns;

    /**
     * Returns the list of table names from which this model depends on.
     *
     * @return DependencyCollection
     */
    public function getDepsOn(): DependencyCollection
    {
        $dependencyCollection = new DependencyCollection();
        foreach ($this->columns as $fieldName => $resolver) {
            if (!$resolver instanceof Dependent) {
                continue;
            }

            foreach ($resolver->getTables() as $table) {
                $dependencyCollection->createAndAdd($fieldName, $table, $resolver->getFieldName());
            }
        }

        return $dependencyCollection;
    }
}
