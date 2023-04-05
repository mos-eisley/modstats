<?php

/**
 * Report modstats
 *
 * @package    report
 * @subpackage modstats
**/

require __DIR__ . '/../../config.php';
require 'minidb.php';
require __DIR__ . '/report_modstats_categories_form.php';
require __DIR__ . '/constants.php';


$category = optional_param('category', REPORT_MODSTATS_ALL_CATEGORIES, PARAM_INT);

echo $OUTPUT->header();

$mform = new report_modstats_categories_form();
$mform->display();

$selected_interval = '';

if (isset($_POST['select-menu'])) {
    $selected_interval = $_POST['select-menu'];
}

if ($category == REPORT_MODSTATS_ALL_CATEGORIES) {
    $data = $DB->get_records_sql(
        'SELECT C.fullname AS fullname, C.id AS courseid, M.name, M.id, COUNT(CM.id) AS amount
        FROM {modules} AS M
        JOIN {course_modules} AS CM ON M.id = CM.module
        JOIN {course} AS C ON C.id = CM.course
        WHERE C.visible = 1
        GROUP BY C.fullname'
    );

    $total = $DB->count_records_sql(
        'SELECT COUNT(CM.id) 
        FROM {course} AS C 
        INNER JOIN {course_modules} AS CM ON C.id = CM.course 
        WHERE C.visible = 1'
    );
} else {
    $data = $DB->get_records_sql(
        'SELECT C.fullname AS fullname, C.id AS courseid,
            COUNT(CM.id) AS amount,
            COUNT(CASE WHEN M.name = "quiz" THEN 1 END) AS tests,
            COUNT(CASE WHEN M.name = "resource" THEN 1 END) AS resources
        FROM {modules} AS M
        JOIN {course_modules} AS CM ON M.id = CM.module
        JOIN {course} AS C ON C.id = CM.course
        WHERE C.visible = 1 AND C.category = :cat
        GROUP BY C.fullname',
        array("cat" => $category)
    );

    $total = $DB->count_records_sql(
        'SELECT COUNT(CM.id) 
        FROM {course} AS C 
        INNER JOIN {course_modules} AS CM ON C.id = CM.course 
        WHERE C.visible = 1 AND C.category = :cat',
        array("cat" => $category)
    );
}

$completion_csv_data = array();




    $chart_labels = array();
    $chart_values = array();



    $table = new html_table();
    $table->size = array( '50%', '50%');
    $table->head = array('Létrehozott modulok', 'Kurzus neve');



    foreach ($data as $item) {
        //todo: 100 helyett kreditértékkel számolni
        $max = 100;
        $row = array();
        //darabszám
        //$row[] = $item->amount;
        //százalék
        $percentage = round(($item->amount / $max) * 100, 2);
        $row[] = $percentage;
        $row[] = '<a href="http://localhost/moodle311/course/view.php?id='.$item->courseid.'">'.$item->fullname.'</a>';


        $chart_labels[] = $item->fullname;
        $chart_values[] = $percentage;

        $table->data[] = $row;
        $completion_csv_data[] = $row;
    }

    if (class_exists('core\chart_bar')) {
        $chart = new core\chart_bar();
        $serie = new core\chart_series(
            get_string('lb_chart_serie', 'report_modstats'), $chart_values
        );
        $chart->add_series($serie);
        $chart->set_labels($chart_labels);
        echo $OUTPUT->render_chart($chart, false);
    }

    echo html_writer::table($table);

$head = array('Reviewed', 'Összes tevékenység', 'Kurzus neve', 'Fejlesztő neve', 'Létrehozva', 'Frissítve', 'PDF', 'TXT', 'WORD', 'PPT', 'Videó', 'Tesztek');

echo '<h1> Összes adat </h1>';

$all_table = new html_table();
$all_table->head = $head;

$all_csv_data = array();
$all_data = $DB->get_records_sql(
    'SELECT
    @row_number:=@row_number+1 AS "Sorszám",
    COUNT(CASE WHEN f.filesize != 0 THEN 1 END) AS amount,
    c.id AS courseid,
    c.fullname AS coursename,
    u.username AS username,
    u.firstname,
    u.lastname,
    u.email,
    COUNT(CASE WHEN l.action = "created" AND f.filesize != 0 THEN 1 END) AS "Created",
    COUNT(CASE WHEN l.action = "updated" AND f.filesize != 0 THEN 1 END) AS "Updated",
    COUNT(CASE WHEN mimetype = "application/pdf" AND l.action = "created" THEN 1 END) AS pdf,
    COUNT(CASE WHEN mimetype = "text/plain" AND l.action = "created" THEN 1 END) AS txt,
    COUNT(CASE WHEN mimetype = "application/vnd.openxmlformats-officedocument.wordprocessingml.document" AND l.action = "created" THEN 1 END) AS word,
    COUNT(CASE WHEN mimetype = "application/vnd.openxmlformats-officedocument.presentationml.presentation" AND l.action = "created" THEN 1 END) AS ppt,
    COUNT(CASE WHEN mimetype = "video/mp4" AND l.action = "created" THEN 1 END) AS video,
    FROM_UNIXTIME(l.timecreated) AS "Létrehozás ideje",
    NOW() AS "Lekérdezés ideje"
FROM
    mdl_course c
        JOIN mdl_logstore_standard_log l ON l.courseid = c.id
        JOIN mdl_user u ON u.id = l.userid
        JOIN mdl_course_categories cc ON cc.id = c.category
        JOIN mdl_files f ON f.contextid = l.contextid
        JOIN (SELECT @row_number := 0) r
WHERE
        cc.id = :cat AND l.target = "course_module" AND (l.action = "created" OR l.action = "updated") 
GROUP BY l.userid, l.courseid
ORDER BY `Létrehozás ideje` DESC', array("cat" => $category)
);

foreach ($all_data as $item) {

    $row = array();
    if (getCheck($item->courseid) == "checked")
        $row[] = '<input id='. $item->courseid . ' onclick="handleCheck('. $item->courseid .')" type="checkbox" checked>';
    else
        $row[] = '<input id='. $item->courseid . ' onclick="handleCheck('. $item->courseid .')" type="checkbox">';
    $row[] = $item->amount;
    //todo: make that dynamic
    $row[] = '<a href="http://localhost/moodle311/course/view.php?id='.$item->courseid.'">'.$item->coursename.'</a>';
    $row[] = $item->username;
    $row[] = $item->created;
    $row[] = $item->updated;
    $row[] = $item->pdf;
    $row[] = $item->txt;
    $row[] = $item->word;
    $row[] = $item->ppt;
    $row[] = $item->video;
    $row[] = 7;

    $all_table->data[] = $row;

    $all_csv_data[] = $row;
}

echo html_writer::table($all_table);

echo '<h1> Elmúlt 1 hét </h1>';

$interval_table = new html_table();
$interval_table->head = $head;

$interval_csv_data = array();
$interval_data = $DB->get_records_sql(
    'SELECT
    @row_number:=@row_number+1 AS "Sorszám",
    COUNT(CASE WHEN f.filesize != 0 THEN 1 END) AS amount,
    c.id AS courseid,
    c.fullname AS coursename,
    u.username AS username,
    u.firstname,
    u.lastname,
    u.email,
    COUNT(CASE WHEN l.action = "created" AND f.filesize != 0 THEN 1 END) AS "Created",
    COUNT(CASE WHEN l.action = "updated" AND f.filesize != 0 THEN 1 END) AS "Updated",
    COUNT(CASE WHEN mimetype = "application/pdf" AND l.action = "created" THEN 1 END) AS pdf,
    COUNT(CASE WHEN mimetype = "text/plain" AND l.action = "created" THEN 1 END) AS txt,
    COUNT(CASE WHEN mimetype = "application/vnd.openxmlformats-officedocument.wordprocessingml.document" AND l.action = "created" THEN 1 END) AS word,
    COUNT(CASE WHEN mimetype = "application/vnd.openxmlformats-officedocument.presentationml.presentation" AND l.action = "created" THEN 1 END) AS ppt,
    COUNT(CASE WHEN mimetype = "video/mp4" AND l.action = "created" THEN 1 END) AS video,
    FROM_UNIXTIME(l.timecreated) AS "Létrehozás ideje",
    NOW() AS "Lekérdezés ideje"
FROM
    mdl_course c
        JOIN mdl_logstore_standard_log l ON l.courseid = c.id
        JOIN mdl_user u ON u.id = l.userid
        JOIN mdl_course_categories cc ON cc.id = c.category
        JOIN mdl_files f ON f.contextid = l.contextid
        JOIN (SELECT @row_number := 0) r
WHERE
        cc.id = :cat AND l.target = "course_module" AND (l.action = "created" OR l.action = "updated") AND l.timecreated > UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 WEEK))
GROUP BY l.userid, l.courseid
ORDER BY `Létrehozás ideje` DESC', array("cat" => $category)
);



foreach ($interval_data as $item) {

    $row = array();
    if (getCheck($item->courseid) == "checked")
        $row[] = '<input id='. $item->courseid . ' onclick="handleCheck('. $item->courseid .')" type="checkbox" checked disabled>';
    else
        $row[] = '<input id='. $item->courseid . ' onclick="handleCheck('. $item->courseid .')" type="checkbox" disabled>';
    $row[] = $item->amount;
    $row[] = '<a href="http://localhost/moodle311/course/view.php?id='.$item->courseid.'">'.$item->coursename.'</a>';
    $row[] = $item->username;
    $row[] = $item->created;
    $row[] = $item->updated;
    $row[] = $item->pdf;
    $row[] = $item->txt;
    $row[] = $item->word;
    $row[] = $item->ppt;
    $row[] = $item->video;
    $row[] = 7;

    $interval_table->data[] = $row;

    $interval_csv_data[] = $row;
}


echo html_writer::table($interval_table);

echo '<h1>Exportálás</h1>';


echo '<a id="interval" href="' . write_csv($interval_csv_data, 'interval.csv') . '">Az elmúlt 1 hét adatainak letöltése</a> <br>';
echo '<a id="all" href="' . write_csv($all_csv_data, 'all.csv') . '">Összes adat letöltése</a> <br>';
echo '<a href="' . write_csv($completion_csv_data, 'completion.csv') . '">Kurzus feltöltöttség letöltése</a>';


echo $OUTPUT->footer();


function write_csv($data, $filename) {
    $path = './adatok/';
    $csv = fopen($path . $filename, 'w');
    foreach ($data as $line) {
        fputcsv($csv, $line);
    }
    fclose($csv);

    return $path . $filename;
}

function initTable() {

}

?>

<script>
    function handleCheck(id) {
        let checkbox = document.getElementById(id);
        let isChecked = checkbox.checked ? "checked" : "";
        if (checkbox.checked) {
            fetch(`minidb.php?function=updateCheckboxState&id=${id}&isChecked=${isChecked}`)
                .then(response => response.text())
                .then(data => console.log(data))
            console.log(checkbox.value);
        } else {
            fetch(`minidb.php?function=updateCheckboxState&id=${id}&isChecked=${isChecked}`)
                .then(response => response.text())
                .then(data => console.log(data))
            console.log("unchecked");
        }
    }
</script>

