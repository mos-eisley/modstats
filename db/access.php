<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = [
    "report/modstats:access" => [
        "captype" => "write",
        "contextlevel" => CONTEXT_SYSTEM
    ]
];
