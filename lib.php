<?php
function report_modstats_extend_navigation(global_navigation $navigation) {
    global $PAGE, $USER;

    $context = get_system_context();
    $isManage = has_capability("report/modstats:access", $context, $userid = $USER->id, $doanything = true);

    if (!$isManage) {
        $masternode = $PAGE->navigation->add(
            "report_modstats",
            new moodle_url("/local/neptun_course_manager/list.php"),
            navigation_node::TYPE_CONTAINER,
            null,
            null,
            new pix_icon('i/settings', '')
        );
        $masternode->showinflatnavigation = true;
    }
    return true;
}
