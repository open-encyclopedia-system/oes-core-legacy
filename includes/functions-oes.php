<?php

namespace OES;


/**
 * @oesLegacy Create new string from array parameters.
 *
 * @param array $parts The parts containing field key and additional information.
 * @param int $postID The post ID.
 * @param string $separator The separator between parts.
 * @return string Return the parts as string.
 *
 * @oesDevelopment More options, add parent fields, make link optional, return $url instead of anchor...
 */
function get_post_dtm_parts_from_array(array $parts, int $postID, string $separator = '', bool $sort = false): string
{
    return \OES\Formula\calculate_value($parts, $postID, $separator, $sort);
}