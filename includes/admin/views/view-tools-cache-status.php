<?php

$table = sprintf('<thead><tr class="oes-config-table-separator"><th>%s</th><th>%s</th><th>%s</th><th>%s</th></tr></thead>',
    __('Type', 'oes'),
    __('Timestamp', 'oes'),
    __('Last updated', 'oes'),
    __('Last updated object of this type', 'oes')
);
$table .= '<tbody>';


global $oes;
foreach ($oes->post_types as $singlePostTypeKey => $singlePostType) {

    /* get current cache */
    $cache = oes_get_cache($singlePostTypeKey);

    /* timestamp */
    $timestamp = ($cache && !empty($cache->cache_value_raw)) ?
        ($cache->cache_date . ' GMT') :
        ('<span class="oes-remark">' . __('No cache', 'oes') . '</span>');

    /* date and name of most recent post */
    $modifiedDate = '';
    $recentPost = '';
    if ($posts = get_posts([
        'post_type' => $singlePostTypeKey,
        'number_of_posts' => 11,
        'post_status' => ['publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'],
        'orderby' => 'post_modified'
    ]))
        if (isset($posts[0])) {
            $modifiedDate = $posts[0]->post_modified;

            /* check if after cache timestamp */
            if (strtotime($timestamp) < strtotime($modifiedDate))
                $modifiedDate = '<span style="color:red">' . $modifiedDate . '</span>';

            $recentPost = '<a href="' . get_edit_post_link($posts[0]->ID) . '">' . $posts[0]->post_title . '</a>';
        }


    $table .= sprintf('<tr><td><strong>%s</strong><code class="oes-object-identifier">%s</code></td><td>%s</td><td>%s</td><td>%s</td></tr>',
        $singlePostType['label'],
        $singlePostTypeKey,
        $timestamp,
        $modifiedDate,
        $recentPost
    );

}

foreach ($oes->taxonomies as $singleTaxonomyKey => $singleTaxonomy) {

    /* get current cache */
    $cache = oes_get_cache($singleTaxonomyKey);

    /* timestamp */
    $timestamp = '<span class="oes-remark">' . __('No cache', 'oes') . '</span>';
    if ($cache && !empty($cache->cache_value) && $cacheData = unserialize($cache->cache_value))
        $timestamp = $cacheData['timestamp'] ?
            date('Y-m-d H:i:s', $cacheData['timestamp']) :
            '';

    /* get most current term */
    $recentTerm = '';
    $terms = get_terms([
        'taxonomy' => $singleTaxonomyKey,
        'number' => 1,
        'orderby' => 'term_id',
        'order' => 'DESC'
    ]);

    if (isset($terms[0]))
        $recentTerm = '<a href="' . get_edit_term_link($terms[0]->ID) . '">' . $terms[0]->name . '</a>';


    $table .= sprintf('<tr><td><strong>%s</strong><code class="oes-object-identifier">%s</code></td><td>%s</td><td>%s</td><td>%s</td></tr>',
        $singleTaxonomy['label'],
        $singleTaxonomyKey,
        $timestamp,
        '-*',
        $recentTerm
    );
}

/* get current cache */
$cache = get_option('oes_cache_index');

/* timestamp */
$timestamp = '<span class="oes-remark">' . __('No cache', 'oes') . '</span>';
if ($cache && $cacheData = unserialize($cache))
    $timestamp = $cacheData['timestamp'] ?
        date('Y-m-d H:i:s', $cacheData['timestamp']) :
        '';

$table .= sprintf('<tr><td><strong>%s</strong></td><td>%s</td><td>%s</td><td>%s</td></tr>',
    $oes->theme_index['label'] ?? 'Index',
    $timestamp,
    '-**',
    '-**'
);

$table .= '</tbody>'; ?>
<div>
<table class="oes-config-table oes-replace-select2-inside striped wp-list-table widefat fixed table-view-list">
    <?php echo $table; ?>
</table>
<div class="oes-settings-annotations">
    <p>* Terms have no stored creation date.</p>
    <p>** Not applicable.</p>
</div>
</div>