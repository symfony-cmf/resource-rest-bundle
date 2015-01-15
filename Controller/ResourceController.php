<?php

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Controller;

use Symfony\Cmf\Component\Resource\RepositoryRegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Hateoas\HateoasBuilder;
use Symfony\Component\HttpFoundation\Response;
use Hateoas\UrlGenerator\UrlGeneratorInterface;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;

class ResourceController
{
    /**
     * @var RepositoryRegistryInterface
     */
    private $registry;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    private $serializer;

    /**
     * @param RepositoryInterface
     */
    public function __construct(
        SerializerInterface $serializer,
        RepositoryRegistryInterface $registry,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $this->serializer = $serializer;
        $this->registry = $registry;
        $this->urlGenerator = $urlGenerator;
    }

    public function resourceAction($repositoryName, $path)
    {
        $repository = $this->registry->get($repositoryName);
        $resource = $repository->get('/' . $path);

        $json = $this->serializer->serialize($resource, 'json');

        return new Response($json);
    }
}
