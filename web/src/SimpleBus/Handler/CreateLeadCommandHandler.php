<?php

namespace App\SimpleBus\Handler;

use App\Entity\Leads;
use App\SimpleBus\CreateLeadCommand;
use DateTime;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\Common\Persistence\ManagerRegistry;

class CreateLeadCommandHandler
{
    protected $validator;
    protected $doctrine;

    /**
     * Dependency Injection constructor.
     *
     * @param ValidatorInterface $validator
     * @param ManagerRegistry  $doctrine
     */
    public function __construct(ValidatorInterface $validator, ManagerRegistry $doctrine)
    {
        $this->validator = $validator;
        $this->doctrine  = $doctrine;
    }

    /**
     * Creates new lead
     *
     * @param  CreateLeadCommand $command
     * @throws BadRequestHttpException
     */
    public function handle(CreateLeadCommand $command)
    {
        $violations = $this->validator->validate($command);

        if (count($violations) != 0) {
            $error = $violations->get(0)->getMessage();
            throw new BadRequestHttpException($error);
        }

        $entity = new Leads();

        $entity
            ->setName($command->name)
            ->setSourceId($command->source_id)
            ->setStatus($command->status)
            ->setCreatedAt(DateTime::createFromFormat("Y-m-d H:i:s", $command->created_at))
            ->setCreatedBy($command->created_by)
        ;

        $this->doctrine->getManager()->persist($entity);
        $this->doctrine->getManager()->flush();
    }
}