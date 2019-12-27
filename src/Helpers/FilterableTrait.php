<?php

namespace isoft\fmtsf4\Helpers;

use Symfony\Component\HttpFoundation\Request;

trait FilterableTrait
{
    private $regexFiltering = '/^(.+?):(.+)?/';
    private $availableOperators = [
        'eq', //equal
        'pt', //partial %LIKE%
        'pts', //partial start LIKE%
        'pte', //partial ending %LIKE
        'gte', //greater & equal than
        'lte', //less & equal than
        'gt', //greater than
        'lt', //less than
        'btw', //between
        'nul', //is null
        'nnl' //is not null
    ];

    /**
     * Grab query builder and request and transforms the query string into a query->where
     *
     * @param string $class
     * @param string $alias
     * @param [type] $queryBuilder
     * @param Request $request
     * @param array $filterConfig
     * @return void
     */
    public function filter($class, $alias, $queryBuilder, Request $request, array $filterConfig)
    {
        //create a new instance to access the available fields
        $entity = new $class();

        $filters = [];
        $availableFields = array_keys($filterConfig);

        foreach ($filterConfig as $field => $operators) {
            $queryString = $request->query->get($field);
            $splittedConfig = $this->splitConfig($queryString, $operators);
            $validConfig = $splittedConfig !== false;

            //if no query string present
            if (!$queryString) {
                continue;
            }

            if (!$validConfig) {
                continue;
            }

            //if field config not found
            if (!in_array($field, $availableFields)) {
                continue;
            }

            $filters[$field] = $splittedConfig;
        }

        $paramIndex = 1;

        foreach ($filters as $field => $filter) {
            $this->applyFilter($queryBuilder, $alias, $field, $filter, $paramIndex);
            $paramIndex++;
        }
    }

    /**
     * Check if config is valid and splits the config from eq:query into an array with the operator and value
     *
     * @param string $config
     * @param string $validOperators
     * @return void
     */
    private function splitConfig($config, $validOperators)
    {
        $matches = [];
        $validConfig = preg_match($this->regexFiltering, $config, $matches);

        //if config format does not match
        if ($validConfig !== 1) {
            return false;
        }

        $operator = $matches[1] ?? null;
        $value = $matches[2] ?? null;
        $operator = strtolower($operator);

        //special case for between operator
        if ($operator === 'btw') {
            $values = explode(':and:', $value);

            if (count($values) !== 2) {
                return false;
            }

            $value = $values;
        }

        //not a valid operator, neither global operator or field operator
        if (!in_array($operator, $this->availableOperators) or !in_array($operator, $validOperators)) {
            return false;
        }

        return compact('operator', 'value');
    }

    /**
     * Apply where condition
     *
     * @param [type] $queryBuilder
     * @param string $alias
     * @param string $field
     * @param array $filter
     * @param int $paramIndex
     */
    private function applyFilter(&$queryBuilder, $alias, $field, $filter, &$paramIndex)
    {
        $value = $filter['value'];
        $operator = $filter['operator'];
        $expression;
        $alias .= '.';
        $parameters = [$paramIndex => $value];
        $assignParam = true;

        switch ($operator) {
            case 'eq':
            case 'lt':
            case 'gt':
            case 'gte':
            case 'lte':
                $expression = $queryBuilder->expr()->{$operator}($alias . $field, '?' . $paramIndex);
                break;
            case 'pt':
                $expression = $queryBuilder->expr()->like($alias . $field, '?' . $paramIndex);
                $value = '%' . $value . '%';
                $parameters = [$paramIndex => $value];
                break;
            case 'pts':
                $expression = $queryBuilder->expr()->like($alias . $field, '?' . $paramIndex);
                $value = $value . '%';
                $parameters = [$paramIndex => $value];
                break;
            case 'pte':
                $expression = $queryBuilder->expr()->like($alias . $field, '?' . $paramIndex);
                $value = '%' . $value;
                $parameters = [$paramIndex => $value];
                break;
            case 'btw':
                $expression = $queryBuilder
                    ->expr()
                    ->between($alias . $field, '?' . $paramIndex, '?' . ($paramIndex + 1));
                $parameters = [
                    $paramIndex => $value[0],
                    $paramIndex + 1 => $value[1]
                ];

                $paramIndex++;
                break;
            case 'nul':
                $expression = $queryBuilder->expr()->isNull($alias . $field);
                $assignParam = false;
                break;
            case 'nnl':
                $expression = $queryBuilder->expr()->isNotNull($alias . $field);
                $assignParam = false;
                break;
        }

        $queryBuilder->andWhere($expression);

        if ($assignParam) {
            foreach ($parameters as $i => $value) {
                $queryBuilder->setParameter($i, $value);
            }
        }
    }
}
