<?php
return array(
    'newrelic' => array(
        /**
         * Define the name of the application as it will be seen in the New Relic UI
         */
        'application_name' => getenv('NEW_RELIC_APP_NAME') ?: 'webpt/emr-delegator',

        /**
         * Define the New Relic license key to use
         */
        // 'license' => '',

        /**
         * Enable auto-insertion of the JavaScript fragments for browser monitoring
         */
        // 'browser_timing_enabled' => false,

        /**
         * Enable auto-insertion of the JavaScript fragments for browser monitoring by NewRelic itself
         */
        // 'browser_timing_auto_instrument' => true,

        /**
         * Enable exceptions logging
         */
        'exceptions_logging_enabled' => true,

        /**
         * Defines transactions that does not generate metrics
         */
        'ignored_transactions' => array(),

        /**
         * Defines background job transactions
         */
        'background_jobs' => array(),

        /**
         * Defines transactions that does not generate apdex metrics
         */
        'ignored_apdex' => array(),
    ),
);