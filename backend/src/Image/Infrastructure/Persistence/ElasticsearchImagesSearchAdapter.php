<?php

namespace App\Image\Infrastructure\Persistence;

use App\Image\Application\Port\ImageSearchPort;
use App\Image\Application\SearchImages\SearchImagesCriteria;
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

        if ($searchCriteria->category) {
            $boolQuery->addMust(
                new Term(['category' => $searchCriteria->category->value])
            );
        }

        if ($searchCriteria->showOnHomepage) {
            $boolQuery->addFilter(
                new Term(['showOnHomepage' => true])
            );
        }

        if ($searchCriteria->searchTerm) {
            $matchQuery = new MultiMatch();
            $matchQuery->setFields(['description', 'tags']);
            $matchQuery->setQuery($searchCriteria->searchTerm);
            $matchQuery->setFuzziness('AUTO');
            $boolQuery->addMust($matchQuery);
        }

        $query = new Query($boolQuery);

        if ($searchCriteria->sortBy) {

            switch ($searchCriteria->sortBy) {
                case 'likeCount':
                    $query->setSort(['likeCount' => ['order' => 'desc']]);
                    break;
                case 'createdAt':
                    $query->setSort(['createdAt' => ['order' => 'desc']]);
                    break;
            }
        }

        $query->setFrom(($searchCriteria->pageNumber - 1) * $searchCriteria->pageSize);
        $query->setSize($searchCriteria->pageSize);

        return $this->finder->find($query);
    }
}
