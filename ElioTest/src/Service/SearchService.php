<?php declare(strict_types=1);

namespace ElioTest\Service;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use \Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;

class SearchService
{
    private EntityRepository $productRepository;

    public function __construct(EntityRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function searchAvailableProducts(
        $context,
        string $id = ''
    ): EntitySearchResult {
        $criteria = new Criteria();

        $criteria->addFilter(new RangeFilter('availableStock', [RangeFilter::GT => 0]));

        if ($id) {
            $criteria->addFilter(new EqualsFilter('id', $id));
        }

        return $this->productRepository->search($criteria, $context);
    }
}
