<?php


namespace App\Controller;

use Doctrine\Common\Inflector\Inflector;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("")
 */
class DefaultController extends AbstractController
{
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        Inflector $inflector
    )
    {
        parent::__construct($serializer, $validator, $inflector);
    }
}
