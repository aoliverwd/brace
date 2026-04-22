<?php

namespace Brace;

trait Filters
{
    /**
     * Filters
     * @var array<mixed>
     */
    private array $filters = [];

    /**
     * Register a filter
     *
     * @param string $name
     * @param callable $filter
     * @return object
     */
    public function registerFilter(string $name, callable $filter): object
    {
        if (!isset($this->filters[$name])) {
            $this->filters[$name] = $filter;
        }

        return $this;
    }

    /**
     * Get variable and filter from variable string
     *
     * @param string $variable
     * @return array<int, string|false>
     */
    private function variableFilter(string $variable): array
    {
        // Match variable and filter {{ variable|filter }}
        if (preg_match('/([\w\d\-\_>]+)\|([\w\d\-\_]+)/s', $variable, $matches)) {
            $is_filter = isset($this->filters[$matches[2]]) && is_callable($this->filters[$matches[2]]);
            return [$matches[1], $is_filter ? $matches[2] : false];
        }

        return [$variable, false];
    }

    /**
     * Process a filter on a value
     *
     * @param string $filter
     * @param mixed $value
     * @return mixed
     */
    private function processFilter(string $filter, mixed $value): mixed
    {
        if (!isset($this->filters[$filter]) || !is_callable($this->filters[$filter])) {
            return $value;
        }

        return call_user_func($this->filters[$filter], $value);
    }
}
