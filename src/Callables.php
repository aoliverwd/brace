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
     * @param array<mixed> $data
     * @return string
     */
    private function callables(string $method, string $content, array $data = []): string
    {
        // Check if the method exists
        if (isset($this->callable_methods[$method])) {
            // Replace quotes around the content and call the method
            $method_arg = preg_replace(['/^"(.*?)"$/', "/^'(.*?)'$/"], '$1', $content);

            // Process the data chain and call the method
            if (is_string($method_arg) && !empty($data)) {
                $method_arg = $this->processDataChain(trim($method_arg), $data);
            }

            // Return the result of the callable method
            return (string) $this->callable_methods[$method]($method_arg);
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
     * @param array<mixed> $data
     * @return string
     */
    private function processCallables(string $content, array $data = []): string
    {
        $callables = $this->hasCallables($content);

        if (!$callables) {
            return $content;
        }

        foreach ($callables as $callableMethod) {
            if (isset($this->callable_methods[$callableMethod[1]])) {
                $content = str_replace(
                    $callableMethod[0],
                    $this->callables(method: $callableMethod[1], content: $callableMethod[2], data: $data),
                    $content,
                );
            }
        }

        return $content;
    }
}
