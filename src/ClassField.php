<?php declare(strict_types=1);

namespace App;

class ClassField
{
    private string $name;
    private bool $required;
    private ?string $type = null;
    private ?ClassInfo $classInfo = null;

    /**
     * @return ClassInfo|null
     */
    public function getClassInfo(): ?ClassInfo
    {
        return $this->classInfo;
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
}
