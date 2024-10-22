<?php

namespace Brace;

final class DataProcessing
{
    /**
     * Process data chain
     *
     * @param string $string
     * @param array<mixed> $dataset
     * @return mixed
     */
    public static function processDataChain(
        string $string,
        array $dataset
    ): mixed {
        // Check for search by array value
        $array_value_seratch = explode("->?", $string);

        if (count($array_value_seratch) > 1) {
            foreach ($array_value_seratch as $thisVar) {
                if (
                    preg_match('/^(.*?)\[(.*?)\](.*)$/', $thisVar, $matches) &&
                    is_array($dataset)
                ) {
                    foreach ($dataset as $row) {
                        if (
                            isset($row[$matches[1]]) &&
                            $row[$matches[1]] === $matches[2]
                        ) {
                            return self::processChain(
                                (string) preg_replace("/^->/", "", $matches[3]),
                                $row
                            );
                        }
                    }
                }

                $dataset = !empty(trim($thisVar))
                    ? self::processChain(
                        $thisVar,
                        is_array($dataset) ? $dataset : []
                    )
                    : $dataset;
            }
        }

        return self::processChain($string, is_array($dataset) ? $dataset : []);
    }

    /**
     * Process chain
     * @param  string $input
     * @param  array<mixed> $dataset
     * @return mixed
     */
    private static function processChain(string $input, array $dataset): mixed
    {
        $return = [];

        $is_count = self::checkForCount($input);
        $is_a_callable = self::checkForCallable($input);

        if (!empty($is_a_callable) && is_callable($is_a_callable)) {
            return boolval($is_a_callable());
        }

        $input = !empty($is_count) ? $is_count : $input;

        foreach (explode("->", $input) as $thisVar) {
            if (is_array($dataset) && isset($dataset[$thisVar])) {
                $dataset = $dataset[$thisVar];
                $return = !empty($is_count) ? count($dataset) : $dataset;
            } else {
                return "";
            }
        }

        return $return;
    }

    /**
     * Check for count
     * @param  string $input
     * @return string
     */
    private static function checkForCount(string $input): string
    {
        if (preg_match("/^COUNT\((.*?)\)/", $input, $match)) {
            return $match[1];
        }

        return "";
    }

    /**
     * Check for callable
     * @param  string $input
     * @return string
     */
    private static function checkForCallable(string $input): string
    {
        if (preg_match("/^([\\\A-Za-z_]+::[A-Za-z_]+)$/", $input, $match)) {
            return $match[0];
        }

        return "";
    }
}
