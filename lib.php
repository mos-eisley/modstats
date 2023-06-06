<?php
function local_modstats_extend_navigation(global_navigation $navigation){

    global $PAGE, $USER;

    $context = get_system_context();
    $isManage = has_capability("local/modstats:access", $context, $userid = $USER->id, $doanything = true);

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
