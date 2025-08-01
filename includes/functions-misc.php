<?php


/**
 * OES filters the content when calling the Post or Term Object. If the additional text needs to be filtered, the
 * functions must be called without filter.
 *
 * @param string $text The text to be filtered.
 * @param array $args Additional parameters to identify which functions should apply.
 * @return string The filtered text.
 */
function oes_apply_the_filter(string $text, array $args = []): string
{
    if (empty($args) || in_array('blocks', $args)) $text = do_blocks($text);
    if (empty($args) || in_array('texturize', $args)) $text = wptexturize($text);
    if (empty($args) || in_array('smilies', $args)) $text = convert_smilies($text);
    if (empty($args) || in_array('paragraphs', $args)) $text = wpautop($text);
    if (empty($args) || in_array('no_paragraphs_shortcode', $args)) $text = shortcode_unautop($text);
    if (empty($args) || in_array('attachment', $args)) $text = prepend_attachment($text);
    if (empty($args) || in_array('filter_tags', $args)) $text = wp_filter_content_tags($text);
    return wp_replace_insecure_home_url($text);
}


/**
 * Validate file. Return true if file exists and is readable, return error message if not.
 *
 * @param string $file A string containing the file.
 * @return bool|string Returns true or error message.
 */
function oes_validate_file(string $file)
{
    if (!file_exists($file)) return 'File not found ' . $file . '.';
    else if (!is_readable($file)) return 'File not readable ' . $file . '.';
    return true;
}


/**
 * Convert hex color to gray scale.
 *
 * @param string $hex The hex color.
 * @param bool $invert Invert color.
 * @return string Return the gray scale hex color.
 */
function oes_hex_color_to_grayscale(string $hex, bool $invert = false): string {

    /* convert */
    $rgb = oes_hex_to_rgb($hex);

    /* optional invert color */
    if($invert){
        $rgb['r'] = 255 - $rgb['r'];
        $rgb['g'] = 255 - $rgb['g'];
        $rgb['b'] = 255 - $rgb['b'];
    }
    $convert  = dechex((int)(0.299 * $rgb['r']) + (int)(0.587 * $rgb['g']) + (int)(0.114 * $rgb['b']));
    return sprintf("#%02x%02x%02x", $convert, $convert, $convert);
}


/**
 * Convert hex color string to rgb array
 *
 * @param string $hex The hex string.
 * @param bool $alpha The opacity argument.
 * @return array Return an array with rgb values. Return black, if conversion fails.
 */
function oes_hex_to_rgb(string $hex, bool $alpha = false): array
{
    /* clean string */
    $hex = str_replace('#', '', $hex);

    $rgb = ['r' => 0, 'g' => 0, 'b' => 0];
    switch(strlen($hex)){

        case '6' :
            $rgb = [
                'r' => hexdec(substr($hex, 0, 2)),
                'g' => hexdec(substr($hex, 2, 2)),
                'b' => hexdec(substr($hex, 4, 2))
            ];
            break;

        case'3':
            $rgb = [
                'r' => hexdec(str_repeat(substr($hex, 0, 1), 2)),
                'g' => hexdec(str_repeat(substr($hex, 1, 1), 2)),
                'b' => hexdec(str_repeat(substr($hex, 2, 1), 2))
            ];
            break;
    }

    /* add alpha */
    if ($alpha) $rgb['a'] = $alpha;
    return $rgb;
}