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

use PHPCR\Util\PathHelper;
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
    public function __construct(
        SerializerInterface $serializer,
        RepositoryRegistryInterface $registry
    ) {
        $this->serializer = $serializer;
        $this->registry = $registry;
    }

    public function getResourceAction($repositoryName, $path)
    {
        $repository = $this->registry->get($repositoryName);
        $resource = $repository->get('/'.$path);

        $context = SerializationContext::create();
        $context->enableMaxDepthChecks();
        $context->setSerializeNull(true);
        $json = $this->serializer->serialize(
            $resource,
            'json',
            $context
        );

        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * There are two different changes that can currently be done on a cmf resource:
     *
     * - move
     * - rename
     *
     * changing payload properties isn't supported yet.
     *
     * @param string  $repositoryName
     * @param string  $path
     * @param Request $request
     */
    public function patchResourceAction($repositoryName, $path, Request $request)
    {
        $repository = $this->registry->get($repositoryName);
        $this->failOnNotEditable($repository, $repositoryName);

        $resourcePath = $request->get('path');
        $resourceName = $request->get('name');
        if ($path !== $resourcePath) {
            $repository->move($path, $resourcePath);
        } elseif ($resourceName !== PathHelper::getNodeName($path)) {
            $targetPath = PathHelper::getParentPath($path).'/'.$resourceName;
            $repository->move($path, $targetPath);
        }
    }

    /**
     * Delete a resource of a repository.
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

        $deleted = $repository->remove($path);
        if (0 === $deleted) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    private function failOnNotEditable(ResourceRepository $repository, $repositoryName)
    {
        if (!$repository instanceof EditableRepository) {
            throw new RouteNotFoundException(sprintf('Repository %s is not editable.', $repositoryName));
        }
    }
}
