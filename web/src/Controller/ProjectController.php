<?php

namespace App\Controller;

use App\Repository\LeadsRepository;
use App\SimpleBus\CreateLeadCommand;
use Doctrine\Common\Inflector\Inflector;
use Exception;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use SimpleBus\SymfonyBridge\Bus\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api")
 */
class ProjectController extends AbstractController
{
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        Inflector $inflector
    )
    {
        parent::__construct($serializer, $validator, $inflector);
    }

    /**
     * @Route("/get", name="get_leads", methods={"GET"})
     * @Security("is_granted('ROLE_ALL')")
     * @param Request $request
     * @param LeadsRepository $repo
     * @return JsonResponse
     */
    public function getLeads(Request $request, LeadsRepository $repo): JsonResponse
    {
        try {
            $leads = $repo->getLeadsByFilter($request->get('page'),
                $request->get('created_by'), $request->get('status'));

            $serializer = $this->get('serializer');

            return new JsonResponse($serializer->normalize($leads));
        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/add", name="add_lead", methods={"POST"})
     * @Security("is_granted('ROLE_ALL')")
     * @param Request $request
     * @param CommandBus $commandBus
     * @return JsonResponse
     */
    public function addLead(Request $request, CommandBus $commandBus): JsonResponse
    {
        if (0 !== strpos($request->headers->get('Content-Type'), 'application/json')) {
            return new JsonResponse('Error: not json object', Response::HTTP_BAD_REQUEST);
        }

        try {
            $data = json_decode($request->getContent());

            $command = new CreateLeadCommand();
            $command->name = $data->name ?? null;
            $command->source_id = $data->source_id ?? null;
            $command->status = $data->status ?? null;
            $command->created_at = $data->created_at ?? null;
            $command->created_by = $data->created_by ?? null;

            $commandBus->handle($command);

            return new JsonResponse(["status" => "ok"], Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
