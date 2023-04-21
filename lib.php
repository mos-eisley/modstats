<?php
function local_modstats_extend_navigation(global_navigation $navigation): bool
{

    global $PAGE, $USER;

    try {
        $context = context_system::instance();
    } catch (dml_exception $e) {
        $context = null;
    }

    try {
        $isManage = has_capability("local/modstats:access", $context, $userid = $USER->id, $doanything = true);
    } catch (coding_exception $e) {
        $isManage = false;
    }

    if($isManage){
        $masternode = $PAGE->navigation->add(
            "Modstats",
            new moodle_url("/local/modstats/index.php"),
            navigation_node::TYPE_CONTAINER,
            null,
            null,
            new pix_icon('i/stats', '')
        );
        $masternode->title("Modstats");
        $masternode->showinflatnavigation = true;
    }
    return true;
}
