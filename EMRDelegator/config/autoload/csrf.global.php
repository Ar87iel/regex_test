<?php
/**
 * Configuration file where the first value will enable or disable CSRF security feature.
 *
 * To add an element into blacklist, take the fourth last field from log info, see the next example:
 *
 * 2015-11-05 21:19:38.439 T563bc7e82cb2a9.51951172 P mvc.csrf ERROR A CSRF token violation has occurred. | 198.62.255.59 | MRamirez | 36 | Thursday, November 5, 2015 at 9:19:36 PM Etc/UTC | POST | /scheduler/index/data/T/e | HTTP/1.1 | 302 | Error MVC: scheduler, index, data
 *
 * 'whiteList' array will store the Touch Points (TPs) which will considered to be checked for CSRF violation.
 * To add an element, take the action URL from your form,
 * or the URL of your AJAX request (POST) without the domain nor their parameters. See examples below.
 *
 * Example - Whitelist TP:
 * URL: https://auth.webpt.vagrant/service/authenticate.json
 * White List TP: service/authenticate.json
 */
return array(
    'csrf' => array(
        'enabled' => false,
        'black_list' => array( //array will store the TPs which will not be considered to check CSRF violation.
        ),
        'handleCsrfViolationsEnabled' => false,
        'whiteList' => array(
            'agreement/agreed',
        ),
    ),
);
