<?php

require __DIR__ . '/../../config.php';
require_once $CFG->libdir . '/adminlib.php';
require_once __DIR__ . '/constants.php';

admin_externalpage_setup('reportmodstats', '', null, '', array('pagelayout' => 'report'));

$category = required_param('category', PARAM_INT);
$module = required_param('module', PARAM_INT);

$result = $DB->get_record('modules', array('id' => $module));

echo $OUTPUT->header();
echo $OUTPUT->heading(
  get_string('lb_course', 'report_modstats') . get_string('pluginname', 'mod_' . $result->name)
);

if ($category == REPORT_MODSTATS_ALL_CATEGORIES) {
  $courses = $DB->get_records_sql(
    'SELECT C.id, C.fullname FROM {course} AS C JOIN {course_modules} CM ON C.id = CM.course WHERE C.visible=1 AND CM.module = :mod',
    array('mod' => $module)
  );
} else {  
  $courses = $DB->get_records_sql(
    'SELECT C.id, C.fullname FROM {course} AS C JOIN {course_modules} CM ON C.id = CM.course WHERE C.visible=1 AND C.category = :cat AND CM.module = :mod',
    array('cat' => $category, 'mod' => $module)
  );
}

$table = new html_table();
$table->head = array(
  get_string('lb_course', 'report_modstats')
);

foreach ($courses as $course) {
  $table->data[] = array(
    html_writer::link(
      $CFG->wwwroot . '/course/view.php?id=' . $course->id, 
      $course->fullname
    )        
  );
}

echo html_writer::table($table);
echo $OUTPUT->footer();
