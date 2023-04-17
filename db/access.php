<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = [
    "local/modstats:access" => [
        "captype" => "write",
        "contextlevel" => CONTEXT_SYSTEM
    ],
    "local/modstats:canmanageothers" => [
        "captype" => "write",
        "contextlevel" => CONTEXT_SYSTEM
    ],
    "local/modstats:deletecourse" => [
        "captype" => "write",
        "contextlevel" => CONTEXT_SYSTEM
    ]
];
