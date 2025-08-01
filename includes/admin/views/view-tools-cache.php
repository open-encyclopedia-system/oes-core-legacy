<?php

/* prepare tabs */
$tabs = [
    'cache-status' => __('Status', 'oes'),
    'cache-update' => __('Update', 'oes'),
    'cache-empty' => __('Empty Cache', 'oes'),
    //@oesDevelopment 'cache-scheduler' => 'Scheduler',
    'cache-info' => __('Info', 'oes')
];

?>
<div class="oes-page-header-wrapper">
    <div class="oes-page-header">
        <h1><?php _e('OES Cache Settings', 'oes'); ?></h1>
    </div>
    <nav class="oes-tabs-wrapper hide-if-no-js tab-count-<?php echo sizeof($tabs); ?>" aria-label="Secondary menu"><?php

        foreach ($tabs as $tab => $label) printf('<a href="%s" class="oes-tab %s">%s</a>',
            admin_url('admin.php?page=oes_tools_cache&tab=' . $tab),
            ((($_GET['tab'] ?? 'cache-status') == $tab) ? 'active' : ''),
            $label
        );
        ?>
    </nav>
</div>
<div class="oes-page-body"><?php

    if (isset($_GET['tab']) && $_GET['tab'] == 'cache-info'):?>
        <p><?php _e('The OES feature <b>Cache</b> allows you to store data so that future request on ' .
                'archive pages (pages that list all posts of a specific post type or taxonomy) or the index page ' .
                'can ve served faster. ' .
                'The table below shows the timestamp of the current cache entries. You can see the most ' .
                'recent object of the post type or taxonomy and the corresponding timestamp. ' .
                'You can update the cache manually or by setting up a scheduler.', 'oes'); ?></p>
    <?php
    elseif (!isset($_GET['tab']) || $_GET['tab'] == 'cache-status'):
        include(__DIR__ . '/view-tools-cache-status.php');
    else: \OES\Admin\Tools\display($_GET['tab'] ?? 'cache-status');
    endif;
    ?>
</div>