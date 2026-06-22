<?php

declare(strict_types=1);

namespace Brace;

trait Callables
{
    /**
     * callable_methods
     * @var array<string, callable>
     */
    private array $callable_methods = [];

    /**
     * The pattern used to match callable methods in the content
     */
    private string $callable_pattern = '/([a-zA-Z0-9_-]+)\((.*?)\)/';

    /**
     * Register a callable method
     *
     * @param string $name
     * @param callable $method
     * @return Parser
     */
    public function registerCallable(string $name, callable $method): Parser
    {
        if (!isset($this->callable_methods[$name])) {
            $this->callable_methods[$name] = $method;
        }

        return $this;
    }

    /**
     * Call a callable method
     *
     * @param string $method
     * @param string $content
     * @return string
     */
    private function callables(string $method, string $content): string
    {
        if (isset($this->callable_methods[$method])) {
            return $this->callable_methods[$method](preg_replace(['/^"(.*?)"$/', "/^'(.*?)'$/"], '$1', $content));
        }

        return '';
    }

    /**
     * Match a callable method in the line
     *
     * @param string $line
     * @return bool
     */
    private function matchCallable(string $line): bool
    {
        return (bool) preg_match($this->callable_pattern, $line);
    }

    /**
     * Check if the line contains callable methods
     *
     * @param string $line
     * @return array<int, array<int, string>>|false
     */
    private function hasCallables(string $line): array|false
    {
        if (preg_match_all($this->callable_pattern, $line, $matches, PREG_SET_ORDER)) {
            return $matches;
        }

        return false;
    }

    /**
     * Process callable methods in the content
     *
     * @param string $content
     * @return string
     */
    private function processCallables(string $content): string
    {
        $callables = $this->hasCallables($content);

        if (!$callables) {
            return $content;
        }

        foreach ($callables as $callableMethod) {
            if (isset($this->callable_methods[$callableMethod[1]])) {
                $content = str_replace(
                    $callableMethod[0],
                    $this->callables(method: $callableMethod[1], content: $callableMethod[2]),
                    $content,
                );
            }
        }

        return $content;
    }
}
