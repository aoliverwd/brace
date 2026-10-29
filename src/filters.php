<?php

declare(strict_types=1);

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
     * @return Parser
     */
    public function registerFilter(string $name, callable $filter): Parser
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
        if (preg_match('/([\w\d\-\_>\?\[\]]+)\|([\w\d\-\_]+)/s', $variable, $matches)) {
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

    /**
     * Register sanitiseation helper filters
     */
    private function registerHelperFilters(): void
    {
        $this->registerFilter('esc_html', fn ($text) => SanatiseHelpers::esc_html($text));
        $this->registerFilter('esc_attr', fn ($text) => SanatiseHelpers::esc_attr($text));
        $this->registerFilter('esc_textarea', fn ($text) => SanatiseHelpers::esc_textarea($text));
        $this->registerFilter('esc_js', fn ($text) => SanatiseHelpers::esc_js($text));
        $this->registerFilter('esc_json', fn ($text) => SanatiseHelpers::esc_json($text));
        $this->registerFilter('esc_url', fn ($text) => SanatiseHelpers::esc_url($text));
        $this->registerFilter('sanitise_text_field', fn ($text) => SanatiseHelpers::sanitise_text_field($text));
        $this->registerFilter('sanitise_textarea_field', fn ($text) => SanatiseHelpers::sanitise_textarea_field($text));
        $this->registerFilter('sanitise_email', fn ($text) => SanatiseHelpers::sanitise_email($text));
    }
}
