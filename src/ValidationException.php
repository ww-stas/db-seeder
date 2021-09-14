<?php declare(strict_types=1);

namespace App;

class ValidationException extends \Exception
{
    private ConfigValidationResult $result;

    public function __construct(ConfigValidationResult $result)
    {
        $this->result = $result;
        parent::__construct('Config validation failure');
    }

    /**
     * @return ConfigValidationResult
     */
    public function getResult(): ConfigValidationResult
    {
        return $this->result;
    }

    public function __toString()
    {
        $output = "\nThere are validation errors: \n";
        foreach ($this->result->getErrors() as $error) {
            $output .= " - $error\n";
        }

        return $output;
    }


}
