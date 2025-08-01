<div class="oes-page-header-wrapper">
    <div class="oes-page-header">
        <h1><?php _e('OES Legacy', 'oes'); ?></h1>
    </div>
</div>
<div class="oes-page-body">
    <p><?php
        printf(__('Enable legacy features and save options.', 'oes')); ?>
    </p><?php
    \OES\Admin\Tools\display('legacy-features');
    ?>
</div>