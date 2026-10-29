<?php

declare(strict_types=1);

namespace Brace;

trait Shortcodes
{
    /**
     * shortcode_methods
     * @var array<string, string|callable>
     */
    private array $shortcode_methods = [];

    /**
     * The pattern used to match shortcode methods in the content
     */
    private string $shortcode_pattern = '/^\[(.*?)\s+(.*?)\s*\]$|^\[(.*?)\]$/';

    /**
     * Register shortcode
     * @param  string $name
     * @param  string|callable $theMethod
     * @return Parser
     */
    public function regShortcode(string $name, string|callable $theMethod): Parser
    {
        if (!isset($this->shortcode_methods[$name])) {
            $this->shortcode_methods[$name] = $theMethod;
        }

        return $this;
    }

    /**
     * Call shortcode
     *
     * @param string $shortcodeSyntax
     * @param array<mixed> $dataset
     * @return string
     */
    private function callShortcode(string $shortcodeSyntax, array $dataset): string
    {
        preg_match($this->shortcode_pattern, $shortcodeSyntax, $args);
        $args = array_values(array_filter($args));

        /** Check args are valid */
        if (!$args || count($args) < 2) {
            return $shortcodeSyntax;
        }

        /** Get method name from args */
        $methodName = $this->shortcode_methods[$args[1]] ?? false;

        // Get attributes from args
        $args[2] ??= '';
        preg_match_all('/(.*?)="(.*?)"/', $args[2], $attributes, PREG_SET_ORDER);

        /** Check for registered functions */
        if ($methodName) {
            /** Check is a global function */
            $is_global = is_callable($methodName) ? false : is_string($methodName) && isset($GLOBALS[$methodName]);

            /** Check if method is callable */
            $method = $is_global ? $GLOBALS[$methodName] : $methodName;

            if (!is_callable($method)) {
                return (string) $method . ' not found';
            }

            /** Check if attributes are valid */
            if ($attributes) {
                foreach ($attributes as $key => $attribute) {
                    $attributes[trim($attribute[1])] = trim($attribute[2]);
                    unset($attributes[$key]);
                }
            }

            /** Merge arguments with global dataset */
            $send_data = [array_merge($attributes ?: [], ['GLOBAL' => $dataset])];

            /** Execute function and return result */
            return call_user_func_array($method, $send_data);
        }

        /** Return shortcode syntax if method is not callable */
        return $shortcodeSyntax;
    }
}
