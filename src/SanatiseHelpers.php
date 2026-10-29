<?php

declare(strict_types=1);

namespace Brace;

class SanatiseHelpers
{
    /**
     * Escape for HTML body text: <p><?= esc_html($x) ?></p>
     *
     * @param string $text
     * @return string
     */
    public static function esc_html(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Escape for HTML attribute values: <input value="<?= esc_attr($x) ?>">
     *
     * @param string $text
     * @return string
     */
    public static function esc_attr(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Escape for <textarea> content
     *
     * @param string $text
     * @return string
     */
    public static function esc_textarea(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Escape a string for use inside an inline JS string in an HTML attribute,
     * e.g. onclick="alert('<?= esc_js($x) ?>')". (Same approach as WordPress.)
     * Prefer esc_json()/data-attributes over inline handlers where possible.
     *
     * @param string $text
     * @return string
     */
    public static function esc_js(string $text): string
    {
        $text = htmlspecialchars($text, ENT_COMPAT | ENT_SUBSTITUTE, 'UTF-8');
        $text = str_replace(['\\', "'", "\r", "\n"], ['\\\\', "\\'", '', '\\n'], $text);
        return $text;
    }

    /**
     * Safest way to pass data into a <script> block. Outputs a valid JS literal
     * (string, number, array, object), so do NOT wrap it in quotes:
     * <script>const name = <?= esc_json($name) ?>;</script>
     *
     * @param string $value
     * @return string
     */
    public static function esc_json(string $value): string
    {
        return json_encode(
            $value,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
        );
    }

    /**
     * Validate and escape a URL for href/src. Returns '' for disallowed schemes
     * (e.g. javascript:, data:). Relative URLs are allowed.
     *
     * @param string $url
     * @param array<string> $protocols
     * @return string
     */
    public static function esc_url(string $url, array $protocols = ['http', 'https', 'mailto', 'tel']): string
    {
        $url = trim($url);
        // Strip control characters and whitespace tricks (e.g. "java\tscript:")
        $url = preg_replace('/[\x00-\x20\x7F]+/u', '', $url) ?? '';
        if ($url === '') {
            return '';
        }

        // Has a scheme? Check against the allow-list.
        if (preg_match('/^([a-z][a-z0-9+.\-]*):/i', $url, $m)) {
            if (!in_array(strtolower($m[1]), $protocols, true)) {
                return '';
            }
        } elseif (str_starts_with($url, '//')) {
            // Protocol-relative: fine, inherits page scheme.
        }

        return htmlspecialchars($url, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * sanitise a single-line text input: strips tags, control chars, extra whitespace
     *
     * @param string $text
     * @return string
     */
    public static function sanitise_text_field(string $text): string
    {
        $text = strip_tags($text);
        $text = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $text) ?? '';
        $text = preg_replace('/\s+/u', ' ', $text) ?? '';
        return trim($text);
    }

    /**
     * Like sanitise_text_field but keeps line breaks
     *
     * @param string $text
     * @return string
     */
    public static function sanitise_textarea_field(string $text): string
    {
        $text = strip_tags($text);
        $text = str_replace("\r\n", "\n", $text);
        $text = preg_replace('/[^\P{C}\n]+/u', '', $text) ?? '';
        return trim($text);
    }

    /**
     * Returns a valid email or ''
     *
     * @param string $email
     * @return string
     */
    public static function sanitise_email(string $email): string
    {
        $email = trim($email);
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false ? $email : '';
    }
}
