<?php

namespace App\Controller;

use Doctrine\Common\Inflector\Inflector;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    private $validContentTypes = ['json' => 'application/json'];

    protected $serializer;
    protected $validator;
    protected $inflector;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        Inflector $inflector
    )
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->inflector = $inflector;
    }

    /**
     * @param string $contentType
     *
     * @return string|Response
     */
    protected function validateContentType($contentType)
    {
        if (!in_array($contentType, $this->validContentTypes)) {
            return $this->createFailureResponse(
                ['content_type' => sprintf('Invalid content type [%s].', $contentType)],
                'json'
            );
        }

        return array_search($contentType, $this->validContentTypes);
    }

    /**
     * @param string $payload
     * @param string $model
     * @param string $format
     *
     * @return object|Response
     */
    protected function validatePayload($payload, $model, $format)
    {
        $payload = $this->serializer->deserialize($payload, $model, $format);

        $errors = $this->validator->validate($payload);
        if (count($errors)) {
            return $this->createFailureResponse($errors, $format);
        }

        return $payload;
    }

    /**
     * @param array|object $content
     * @param string $format
     *
     * @return Response
     */
    protected function createSuccessResponse($content, $format = 'json')
    {
        return $this->getResponse($content, $format, Response::HTTP_OK);
    }

    /**
     * @param array|ConstraintViolationListInterface $content
     * @param string $format
     *
     * @return Response
     */
    protected function createFailureResponse($content, $format = 'json')
    {
        $errorList = null;

        if ($content instanceof ConstraintViolationList) {
            foreach ($content as $error) {
                $error = $this->getErrorFromValidation($error);
                $errorList[$error['key']] = $error['value'];
            }
        } else {
            $errorList = $content;
        }

        return $this->getResponse(['errors' => $errorList], $format, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param array|object $content
     * @param string $format
     * @param int $status
     *
     * @return Response
     */
    private function getResponse($content, $format, $status)
    {
        $context = new SerializationContext();
        $context->setSerializeNull(false);

        $response = $this->serializer->serialize($content, $format, $context);

        return new Response($response, $status, ['Content-Type' => $this->validContentTypes[$format]]);
    }

    /**
     * @param ConstraintViolationInterface $error
     *
     * @return array
     */
    private function getErrorFromValidation($error)
    {
        $properties = $this->inflector->tableize($error->getPropertyPath());

        return ['key' => $properties, 'value' => $error->getMessage()];
    }
}
