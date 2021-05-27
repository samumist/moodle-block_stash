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

/**
 * Swap requests page.
 *
 * @package    block_stash
 * @copyright  2021 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$courseid = required_param('courseid', PARAM_INT);


require_login($courseid);

$manager = \block_stash\manager::get($courseid);

$userid = $USER->id;

$sql = "SELECT s.id, u.firstname, u.lastname, s.timecreated
        FROM {block_stash_swap} s
        LEFT JOIN {user} u ON s.initiator = u.id
        WHERE s.receiver = :userid AND s.status IS NULL";

$params = ['userid' => $userid];


$records = $DB->get_records_sql($sql, $params);

$data = [
    'requests' => array_values($records),
    'courseid' => $courseid
];

$url = new moodle_url('/blocks/stash/swaprequests.php', ['courseid' => $courseid]);
$PAGE->set_url($url);

echo $OUTPUT->header();
echo $OUTPUT->heading('Add heading');


echo $OUTPUT->render_from_template('block_stash/swap_requests', $data);

// print_object($data);

// print_object($records);
echo $OUTPUT->footer();
