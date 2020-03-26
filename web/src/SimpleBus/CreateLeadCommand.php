<?php

namespace App\SimpleBus;

use Symfony\Component\Validator\Constraints;

class CreateLeadCommand
{
    /**
     * @Constraints\NotBlank()
     * @Constraints\Length(max = "10")
     */
    public $name;

    /**
     * @Constraints\NotBlank()
     * @Constraints\Type("Integer", message="The value {{ value }} is not a valid {{ type }}.")
     */
    public $source_id;

    /**
     * @Constraints\NotBlank()
     * @Constraints\Length(max = "255")
     */
    public $status;

    /**
     * @Constraints\NotBlank()
     * @Constraints\DateTime()
     */
    public $created_at;

    /**
     * @Constraints\NotBlank()
     * @Constraints\Type("Integer", message="The value {{ value }} is not a valid {{ type }}.")
     */
    public $created_by;
}
