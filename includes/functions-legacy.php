<?php

namespace OES\Legacy;


/**
 * Add global nav language parameter.
 *
 * @return void
 */
function add_global_nav_language(): void
{
    global $oes_language, $oes_nav_language;
    $oes_nav_language = $oes_language;
}