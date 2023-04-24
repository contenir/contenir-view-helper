<?php

namespace Contenir\View\Helper;

use Application\Repository\ResourceCollectionRepository;
use Laminas\View\Helper\AbstractHelper;

class Collection extends AbstractHelper
{
    protected $resourceCollectionRepository;

    public function __construct(ResourceCollectionRepository $resourceCollectionRepository = null)
    {
        $this->resourceCollectionRepository = $resourceCollectionRepository;
    }

    public function __invoke($resourceCollectionId)
    {
        return $this->resourceCollectionRepository->findOne([
            'resource_collection_id' => $resourceCollectionId
        ]);
    }
}
