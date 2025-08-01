<?php

/**
 * Prepare data for attachment post.
 *
 * @return void
 */
function oes_prepare_attachment(): void
{
    oes_set_attachment_data();
}

/**
 * Prepare page data for single post.
 * Check if archive is "flat" (redirect to archive), else set single post data.
 *
 * @return void
 */
function oes_prepare_single(): void
{
    global $oes, $post;
    if ($oes->post_types[$post->post_type]['archive_on_single_page'] ?? false)
        oes_redirect(get_post_type_archive_link($post->post_type) . '#' . $post->post_type . '-' . $post->ID);
    else oes_set_post_data();
}

/**
 * Prepare page data for tax (term) page.
 * Check if redirect to archive (use term as filter), else prepare term data.
 *
 * @return void
 */
function oes_prepare_tax(): void
{
    global $taxonomy, $term, $oes;
    if ($taxonomy &&
        isset($oes->taxonomies[$taxonomy]['redirect']) &&
        ($oes->taxonomies[$taxonomy]['redirect'] ?? false) &&
        (($oes->taxonomies[$taxonomy]['redirect'] ?? false) !== 'none') &&
        !isset($_GET['oesf_' . $taxonomy]) &&
        $termObject = get_term_by('slug', $term, $taxonomy)) {
        oes_redirect(get_post_type_archive_link(
                $oes->taxonomies[$taxonomy]['redirect']) . '?oesf_' . $taxonomy . '=' . $termObject->term_id);
    } else oes_set_term_data();
}

/**
 * Prepare page data for search page.
 *
 * @return void
 */
function oes_prepare_search(): void
{

    global $oes_search;
    global $oes_archive_count;


    $args = ['language' => 'all'];
    $class = oes_get_project_class_name('OES_Search');
    $oes_search = new $class($args);
    $oes_archive_count = $oes_search->count;

    global $oes_is_search;
    if (!empty($oes_search->search_term)) {
        global $oes, $oes_archive_data, $oes_archive_count, $oes_filter, $oes_archive;
        if($oes->block_theme) {
            $oes_search->get_results();
            $oes_archive_count = $oes_search->count;
            $oes_filter = $oes_search->filter_array;
            $oes_archive_data = $oes_search->get_data_as_table();
            $oes_archive = (array) $oes_search; //TODO simplify!!
        }
    }
    $oes_is_search = true;
}

/**
 * Prepare page data for index page.
 *
 * @param string $indexPageKey The index page key.
 * @return void
 */
function oes_prepare_index(string $indexPageKey): void
{
    global $oes, $oes_is_index, $oes_is_index_page;
    if ($oes->theme_index_pages[$indexPageKey]['slug'] !== 'hidden') {
        $oes_is_index = $indexPageKey;
        $oes_is_index_page = true;

        $archiveClass = $indexPageKey . '_Index_Archive';
        if (!class_exists($archiveClass)) $archiveClass = 'OES_Index_Archive';
        oes_set_archive_data($archiveClass);

        /* check if additional redirect action */
        do_action('oes/redirect_template', 'prepare_index');
    }
}

/**
 * Prepare page data for taxonomy archive.
 *
 * @return void
 */
function oes_prepare_taxonomies(): void
{
    global $oes, $oes_taxonomy;
    foreach ($oes->taxonomies as $taxonomyKey => $singleTaxonomy) {

        $taxonomyObject = get_taxonomy($taxonomyKey);

        /* Archive pages */
        if (($taxonomyObject->rewrite['slug'] ?? false) &&
            oes_get_current_url(false) ==
            (get_site_url() . '/' . ($taxonomyObject->rewrite['slug'] ?? $taxonomyKey) . '/') &&
            !is_page($taxonomyObject->rewrite['slug'] ?? $taxonomyKey)) {

            if (!empty($oes->theme_index_pages))
                foreach ($oes->theme_index_pages as $indexPageKey => $indexPage)
                    if (in_array($taxonomyKey, $indexPage['objects'] ?? [])) {
                        global $oes_is_index;
                        $oes_is_index = $indexPageKey;
                    }

            $oes_taxonomy = $taxonomyKey;
            $archiveClass = $taxonomyKey . '_Taxonomy_Archive';
            if (!class_exists($archiveClass)) $archiveClass = $taxonomyKey . '_Archive';
            if (!class_exists($archiveClass)) $archiveClass = 'OES_Taxonomy_Archive';
            oes_set_archive_data($archiveClass, ['taxonomy' => $taxonomyKey]);

            /* check if additional redirect action */
            do_action('oes/redirect_template', 'prepare_taxonomies');
        }
    }
}

/**
 * Prepare other page data.
 *
 * @return void
 */
function oes_prepare_data_other(): void
{
    /* check if index page */
    global $oes;
    if (!empty($oes->theme_index_pages))
        foreach ($oes->theme_index_pages as $indexPageKey => $indexPage)
            if (oes_get_current_url(false) === get_site_url() . '/' . ($indexPage['slug'] ?? 'index') . '/')
                oes_prepare_index($indexPageKey);

    /* check if page is taxonomy archive */
    oes_prepare_taxonomies();
}

/**
 * Add body class for created pages (index and taxonomy archives).
 *
 * @param array $classes The body classes.
 * @return array The modified body classes.
 */
function oes_body_class(array $classes): array {

    global $oes_is_index, $oes_taxonomy, $oes_language;
    $removeError = false;
    if(!empty($oes_is_index)) {
        $classes[] = 'oes-index-archive';
        $classes[] = 'oes-index-archive-' . $oes_is_index;
        $removeError = true;
    }

    if(!empty($oes_taxonomy)){
        $classes[] = 'oes-taxonomy-archive';
        $classes[] = 'oes-taxonomy-archive-' . $oes_taxonomy;
        $removeError = true;
    }

    if(!empty($oes_language)) $classes[] = 'oes-body-' . $oes_language;

    /* remove error class */
    if($removeError){
        if (($key = array_search('error404', $classes)) !== false) unset($classes[$key]);
    }

    return $classes;
}