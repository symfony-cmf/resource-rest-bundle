<?php

namespace Symfony\Cmf\Bundle\ResourceRestBundle\Controller;

class ResourceController
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @param RepositoryInterface
     */
    public function __construct(
        RepositoryInterface $repository
    )
    {
        $this->repository = $repository;
    }

    public function resourceAction(Request $request)
    {
        $path = $request->query->get('path');
        var_dump($path);die();;
    }
}
