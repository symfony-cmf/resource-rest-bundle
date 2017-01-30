<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Controller;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Cmf\Bundle\ResourceRestBundle\Serializer\Jms\Handler\ResourceHandler;
use Symfony\Cmf\Component\Resource\Puli\Api\ResourceNotFoundException;
use Symfony\Cmf\Component\Resource\RepositoryRegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ResourceController
{
    const ROLE_RESOURCE_READ = 'CMF_RESOURCE_READ';
    const ROLE_RESOURCE_WRITE = 'CMF_RESOURCE_WRITE';

    /**
     * @var RepositoryRegistryInterface
     */
    private $registry;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ResourceHandler
     */
    private $resourceHandler;

    /**
     * @var AuthorizationCheckerInterface|null
     */
    private $authorizationChecker;

    /**
     * @param SerializerInterface         $serializer
     * @param RepositoryRegistryInterface $registry
     */
    public function __construct(SerializerInterface $serializer, RepositoryRegistryInterface $registry, ResourceHandler $resourceHandler, AuthorizationCheckerInterface $authorizationChecker = null)
    {
        $this->serializer = $serializer;
        $this->registry = $registry;
        $this->authorizationChecker = $authorizationChecker;
        $this->resourceHandler = $resourceHandler;
    }

    /**
     * Provides resource information.
     *
     * @param string $repositoryName
     * @param string $path
     */
    public function getResourceAction(Request $request, $repositoryName, $path)
    {
        if ($request->query->has('depth')) {
            $this->resourceHandler->setMaxDepth($request->query->getInt('depth'));
        }

        $path = '/'.ltrim($path, '/');

        try {
            $repository = $this->registry->get($repositoryName);

            $fullPath = method_exists($repository, 'resolvePath') ? $repository->resolvePath($path) : $path;
            $this->guardAccess('read', $repositoryName, $fullPath);

            $resource = $repository->get($path);

            return $this->createResponse($resource);
        } catch (ResourceNotFoundException $e) {
            throw new NotFoundHttpException(
                sprintf('No resource found at path "%s" for repository "%s"', $path, $repositoryName),
                $e
            );
        }
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
        $path = '/'.ltrim($path, '/');
        $repository = $this->registry->get($repositoryName);

        $fullPath = method_exists($repository, 'resolvePath') ? $repository->resolvePath($path) : $path;
        $this->guardAccess('write', $repositoryName, $fullPath);

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

                    $resource = $repository->get($targetPath);

                    break;
                default:
                    return $this->badRequestResponse(sprintf('Operation "%s" is not supported, supported operations: move.', $action['operation']));
            }
        }

        $this->resourceHandler->setMaxDepth(0);

        return $this->createResponse($resource, Response::HTTP_OK);
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
        $path = '/'.ltrim($path, '/');
        $repository = $this->registry->get($repositoryName);

        $fullPath = method_exists($repository, 'resolvePath') ? $repository->resolvePath($path) : $path;
        $this->guardAccess('write', $repositoryName, $fullPath);

        $repository->remove($path);

        return $this->createResponse('', Response::HTTP_NO_CONTENT);
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

    private function guardAccess($attribute, $repository, $path)
    {
        if (null !== $this->authorizationChecker
            && !$this->authorizationChecker->isGranted(
                'CMF_RESOURCE_'.strtoupper($attribute),
                ['repository_name' => $repository, 'path' => $path]
            )
        ) {
            throw new AccessDeniedException(sprintf('%s access denied for "%s".', ucfirst($attribute), $path));
        }
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
