<?php
function local_modstats_extend_navigation(global_navigation $navigation){
    global $PAGE, $USER;




        $masternode = $PAGE->navigation->add(
            "Modstats",
            new moodle_url("/local/modstats/index.php"),
            navigation_node::TYPE_CONTAINER,
            null,
            null,
            new pix_icon('i/settings', '')
        );
        $masternode->title("Modstats");
        $masternode->showinflatnavigation = true;
    return true;
}
