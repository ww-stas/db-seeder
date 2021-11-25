<?php declare(strict_types=1);

namespace App;

use App\Attributes\Constructor;
use App\Resolver\ArgumentResolver;

class ClassField
{
    private string $name;
    private bool $required;
    private ?string $type = null;
    private bool $isList = false;
    private ?ClassInfo $classInfo = null;
    private string $setter;
    private bool $isPublic = true;
    private string $constructor = Constructor::DEFAULT_EMPTY;

    /**
     * @return string
     */
    public function getConstructor(): string
    {
        return $this->constructor;
    }

    /**
     * @param string $constructor
     *
     * @return ClassField
     */
    public function setConstructor(string $constructor): ClassField
    {
        $this->constructor = $constructor;

        return $this;
    }


    /**
     * @return string
     */
    public function getSetter(): string
    {
        return $this->setter;
    }

    /**
     * @param string $setter
     *
     * @return ClassField
     */
    public function setSetter(string $setter): ClassField
    {
        $this->setter = $setter;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    /**
     * @param bool $isPublic
     *
     * @return ClassField
     */
    public function setIsPublic(bool $isPublic): ClassField
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    /**
     * @return ClassInfo|null
     */
    public function getClassInfo(): ?ClassInfo
    {
        return $this->classInfo;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    public function isPrimitive(): bool
    {
        return null === $this->classInfo;
    }

    public function isArgumentResolver(): bool
    {
        return $this->type === ArgumentResolver::class;
    }

    /**
     * @return bool
     */
    public function isList(): bool
    {
        return $this->isList;
    }

    /**
     * @param bool $isList
     *
     * @return ClassField
     */
    public function setIsList(bool $isList): ClassField
    {
        $this->isList = $isList;

        return $this;
    }

    /**
     * @param ClassInfo|null $classInfo
     *
     * @return ClassField
     */
    public function setClassInfo(?ClassInfo $classInfo): ClassField
    {
        $this->classInfo = $classInfo;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return ClassField
     */
    public function setName(string $name): ClassField
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param bool $required
     *
     * @return ClassField
     */
    public function setRequired(bool $required): ClassField
    {
        $this->required = $required;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return ClassField
     */
    public function setType(string $type): ClassField
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param string $name
     * @param bool   $required
     */
//    public function __construct(string $name, bool $required)
//    {
//        $this->name = $name;
//        $this->required = $required;
//    }


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    public function newInstance($value = null): object
    {
        if ($this->constructor === Constructor::STATIC_MAKE) {
            $callable = [$this->type, 'make'];
            if ($value !== null) {
                return $callable($value);
            }

            return $callable();
        }

        return new $this->type;
    }
}
