<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

require_once $CFG->libdir . '/formslib.php';
require_once __DIR__ . '/constants.php';

class local_modstats_categories_form extends moodleform {
    public function definition() {
        global $DB;

        $mform = $this->_form; // Don't forget the underscore! 

        $categories = $DB->get_records('course_categories', null, 'name');
        $cat_names = array();

        $cat_names[REPORT_MODSTATS_ALL_CATEGORIES] = get_string('lb_all_categories', 'local_modstats');

        foreach ($categories as $item) {
            $catfullname = $item->name;
            // check if category has parent
            if ($item->depth == 2) {
                $parents = explode("/", $item->path);
                $id = $parents[count($parents) - 2]; 
                $temp = $DB->get_record('course_categories', ['id' => $id]);
                $catfullname = $temp->name . ' -> ' . $catfullname;
            }
            $cat_names[$item->id] = $catfullname;
        }

        $mform->addElement('select', 'category', get_string('lb_choose_category', 'local_modstats'), $cat_names);
        $this->add_action_buttons(false, get_string('btn_refresh', 'local_modstats'));
    }
}
