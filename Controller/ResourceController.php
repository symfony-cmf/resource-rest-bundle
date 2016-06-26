<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Controller;

use Puli\Repository\Api\EditableRepository;
use Puli\Repository\Api\ResourceRepository;
use Symfony\Cmf\Component\Resource\RepositoryRegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class ResourceController
{
    /**
     * @var RepositoryRegistryInterface
     */
    private $registry;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param SerializerInterface         $serializer
     * @param RepositoryRegistryInterface $registry
     */
    public function __construct(SerializerInterface $serializer, RepositoryRegistryInterface $registry)
    {
        $this->serializer = $serializer;
        $this->registry = $registry;
    }

    /**
     * Provides resource information.
     *
     * @param string $repositoryName
     * @param string $path
     */
    public function getResourceAction($repositoryName, $path)
    {
        $repository = $this->registry->get($repositoryName);
        $resource = $repository->get('/'.$path);

        return $this->createResponse($resource);
    }

    /**
     * Changes the current resource.
     *
     * The request body should contain a JSON list of operations
     * like:
     *
     *     [{"operation": "move", "target": "/cms/new/id"}]
     *
     * Currently supported operations:
     *
     *  - move (options: target)
     *
     * changing payload properties isn't supported yet.
     *
     * @param string  $repositoryName
     * @param string  $path
     * @param Request $request
     *
     * @return Response
     */
    public function patchResourceAction($repositoryName, $path, Request $request)
    {
        $repository = $this->registry->get($repositoryName);
        $this->failOnNotEditable($repository, $repositoryName);

        $path = '/'.ltrim($path, '/');

        $requestContent = json_decode($request->getContent(), true);
        if (!$requestContent) {
            return $this->badRequestResponse('Only JSON request bodies are supported.');
        }

        foreach ($requestContent as $action) {
            if (!isset($action['operation'])) {
                return $this->badRequestResponse('Malformed request body. It should contain a list of operations.');
            }

            switch ($action['operation']) {
                case 'move':
                    $targetPath = $action['target'];
                    $repository->move($path, $targetPath);

                    break;
                default:
                    return $this->badRequestResponse(sprintf('Only operation "%s" is not supported, supported operations: move.', $action['operation']));
            }
        }

        return $this->createResponse('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Deletes the resource.
     *
     * @param string $repositoryName
     * @param string $path
     *
     * @return Response
     */
    public function deleteResourceAction($repositoryName, $path)
    {
        $repository = $this->registry->get($repositoryName);
        $this->failOnNotEditable($repository, $repositoryName);

        $path = '/'.ltrim($path, '/');

        $repository->remove($path);

        return $this->createResponse('', Response::HTTP_NO_CONTENT);
    }

    private function failOnNotEditable(ResourceRepository $repository, $repositoryName)
    {
        if (!$repository instanceof EditableRepository) {
            throw new RouteNotFoundException(sprintf('Repository "%s" is not editable.', $repositoryName));
        }
    }

    /**
     * @param string $message
     *
     * @return Response
     */
    private function badRequestResponse($message)
    {
        return $this->createResponse(['message' => $message], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param mixed $resource
     * @param int   $httpStatusCode
     *
     * @return Response
     */
    private function createResponse($resource, $httpStatusCode = Response::HTTP_OK)
    {
        $context = SerializationContext::create();
        $context->enableMaxDepthChecks();
        $context->setSerializeNull(true);
        $json = $this->serializer->serialize(
            $resource,
            'json',
            $context
        );

        $response = new Response($json, $httpStatusCode);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
