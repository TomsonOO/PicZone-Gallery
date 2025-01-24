<?php

declare(strict_types=1);

namespace App\Image\Infrastructure\Persistence;

use App\Image\Application\Port\ImageSearchPort;
use App\Image\Application\SearchImages\CategoryEnum;
use App\Image\Application\SearchImages\SearchImagesCriteria;
use App\Image\Application\SearchImages\SortByEnum;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\MultiMatch;
use Elastica\Query\Term;
use FOS\ElasticaBundle\Finder\TransformedFinder;

class ElasticsearchImagesSearchAdapter implements ImageSearchPort
{
    private TransformedFinder $finder;

    public function __construct(TransformedFinder $finder)
    {
        $this->finder = $finder;
    }

    public function searchImages(SearchImagesCriteria $searchCriteria): array
    {
        $boolQuery = new BoolQuery();

        if ($searchCriteria->searchTerm) {
            $matchQuery = new MultiMatch();
            $matchQuery->setFields(['description', 'tags']);
            $matchQuery->setQuery($searchCriteria->searchTerm);
            $matchQuery->setFuzziness('AUTO');
            $boolQuery->addMust($matchQuery);
        }

        if ($searchCriteria->showOnHomepage) {
            $boolQuery->addFilter(
                new Term(['showOnHomepage' => true])
            );
        }

        $query = new Query($boolQuery);

        if ($searchCriteria->category) {
            switch ($searchCriteria->category) {
                case CategoryEnum::MOST_LIKED:
                    $query->setSort(['likeCount' => ['order' => 'desc']]);
                    break;
                case CategoryEnum::NEWEST:
                    $query->setSort(['createdAt' => ['order' => 'desc']]);
                    break;
            }
        } elseif ($searchCriteria->sortBy) {
            switch ($searchCriteria->sortBy) {
                case SortByEnum::LIKE_COUNT:
                    $query->setSort(['likeCount' => ['order' => 'desc']]);
                    break;
                case SortByEnum::CREATED_AT:
                    $query->setSort(['createdAt' => ['order' => 'desc']]);
                    break;
            }
        }

        $query->setFrom(($searchCriteria->pageNumber - 1) * $searchCriteria->pageSize);
        $query->setSize($searchCriteria->pageSize);

        return $this->finder->find($query);
    }
}
