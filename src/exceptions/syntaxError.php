<?php

declare(strict_types=1);

namespace Brace\Exceptions;

use Exception;

class SyntaxError extends Exception
{
    public function __construct(
        string $message,
        int $code = 0,
        ?int $line = null,
        ?string $file = null,
        ?Exception $previous = null,
    ) {
        if ($line !== null) {
            $this->line = $line;
        }

        if ($file !== null) {
            $this->file = $file;
        }

        parent::__construct($message, $code, $previous);
    }
}
