<?php

namespace isoft\fmtsf4\Helpers;

use Symfony\Component\HttpFoundation\Request;

trait SortableTrait
{
    private $regexSorting = '/(.+)\((.+)\)/';
    private $availableDirections = ['asc', 'desc'];

    /**
     * Grab query builder and request and transforms the query string into a query->orderBy
     *
     * @param string $class
     * @param string $alias
     * @param [type] $queryBuilder
     * @param Request $request
     */
    public function sort($class, $alias, $queryBuilder, Request $request)
    {
        //create a new instance to access the available fields
        $entity = new $class();

        $sorting = $request->query->get('sorting');
        $sortingConfigs = explode(',', $sorting);

        foreach ($sortingConfigs as $i => $sortingConfig) {
            $matches = [];

            //separate sorting parts with a regex
            $validFormat = preg_match($this->regexSorting, $sortingConfig, $matches);

            //not a valid sorting format
            if ($validFormat !== 1) {
                continue;
            }

            list(, $direction, $field) = $matches;

            //not a valid direction
            if (!in_array(strtolower($direction), $this->availableDirections)) {
                continue;
            }

            if (!property_exists($class, $field)) {
                continue;
            }

            $queryBuilder->addOrderBy($alias . '.' . $field, $direction);
        }
    }
}
