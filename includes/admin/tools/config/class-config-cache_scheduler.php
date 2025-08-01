<?php

namespace OES\Admin\Tools;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('Config')) oes_include('admin/tools/config/class-config.php');

if (!class_exists('Cache_Scheduler')) :

    /**
     * Class Cache_Scheduler
     *
     * Implement the config tool for admin configurations.
     */
    class Cache_Scheduler extends Config
    {
        //Overwrite parent
        function information_html(): string
        {
            /* get current scheduled event */
            $updateEvents = [];
            $events = get_option('cron') ?? [];
            foreach($events as $eventTimestamp => $event)
                if(is_array($event))
                foreach($event as $hookKey => $hook)
                    if($hookKey === 'oes_update_archive_cache')
                        foreach($hook as $call)
                            $updateEvents[$eventTimestamp] = $call;

            $recentEvent = '';
            if($updateEvents){
                foreach($updateEvents as $eventTimestamp => $updateEvent)
                    $recentEvent .= '<p class="oes-cache-recent-event" style="color:red">' . sprintf(
                            __('Update event scheduled for %s, repeating %s.'),
                            date('d.m.y h:i', $eventTimestamp),
                            $updateEvent['schedule']
                        ) . '</p>';
                if(empty($recentEvent))
                    $recentEvent = '<p class="oes-cache-no-recent-event">' .
                        __('No update event scheduled.', 'oes') .
                        '</p>';
            }

            return '<div class="oes-tool-information-wrapper-cache-scheduler  oes-tool-information-wrapper">' .
                '<p style="color:red">UNDER CONSTRUCTION</p>' .
                '<p>' .
                __('To update the cache periodically you can schedule an update event. ' .
                    'The scheduled event will be triggered by WordPress at the specified interval. ' .
                    'The action will trigger when someone visits your WordPress site if the scheduled time has passed.'
                    , 'oes') .
                '</p></div>' .
                '<div>' . $recentEvent . '</div>';
        }

        //Overwrite parent
        function set_table_data_for_display()
        {
            $this->add_table_row(
                [
                    'title' => 'Enable Scheduler',
                    'key' => 'oes_cache-enabled',
                    'value' => get_option('oes_cache-enabled'),
                    'type' => 'checkbox'
                ]
            );

            $this->add_table_row(
                [
                    'title' => 'Timestamp',
                    'key' => 'oes_cache-timestamp',
                    'value' => get_option('oes_cache-timestamp')
                ],
                [
                    'subtitle' => __('The timestamp is the first time you want the event to occur. ' .
                        'The time format has to be HHii, e.g. for a cache update at 3am insert 0300. ' .
                        'The default value is 0300.', 'oes')
                ]
            );

            $this->add_table_row(
                [
                    'title' => 'Interval',
                    'key' => 'oes_cache-interval',
                    'value' => get_option('oes_cache-interval'),
                    'args' => [
                        'options' => [
                            'daily' => 'Daily',
                            'hourly' => 'Hourly',
                            'twicedaily' => 'Twice Daily'
                        ]
                    ]
                ],
                [
                    'subtitle' => __('The interval defines the recurrence determining how often and ' .
                        'when you want the event to run.', 'oes')
                ]
            );
        }


        //Implement parent
        function admin_post_tool_action(): void
        {
            /* add or delete option */
            $options = [
                'oes_cache-enabled',
                'oes_cache-timestamp',
                'oes_cache-interval'
            ];
            foreach ($options as $option)
                if (!oes_option_exists($option)) add_option($option,
                    $option === 'oes_cache-enabled' ? isset($_POST[$option]) : $_POST[$option]
                );
                else update_option($option,
                    $option === 'oes_cache-enabled' ? isset($_POST[$option]) : $_POST[$option]);

            if ($_POST['oes_cache-enabled']) oes_schedule_cache_event();
            else oes_schedule_clear_cache_event();
        }
    }

    // initialize
    register_tool('\OES\Admin\Tools\Cache_Scheduler', 'cache-scheduler');

endif;