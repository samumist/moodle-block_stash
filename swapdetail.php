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
$swapid = required_param('id', PARAM_INT);
$decision = optional_param('decision', null, PARAM_RAW); // TODO change to something else.


require_login($courseid);

$manager = \block_stash\manager::get($courseid);

$userid = $USER->id;

if (isset($decision)) {
    if ($decision == \block_stash\swap::BLOCK_STASH_SWAP_DECLINE) { // TODO This needs to be a constant.
        $manager->decline_swap($swapid);
        // Redirect to requests page.
        redirect(new moodle_url('/blocks/stash/swaprequests.php', ['courseid' => $courseid]));
    }
    if ($decision == \block_stash\swap::BLOCK_STASH_SWAP_APPROVE) { // TODO This needs to be a constant.
        $manager->swap_items($swapid);
        // Redirect to requests page.
        redirect(new moodle_url('/blocks/stash/swaprequests.php', ['courseid' => $courseid]));
    }
}


$sql = "SELECT sd.id, ui.userid, i.name, sd.quantity
        FROM {block_stash_swap_detail} sd
        LEFT JOIN {block_stash_user_items} ui ON sd.useritemid = ui.id
        LEFT JOIN {block_stash_items} i ON ui.itemid = i.id
        WHERE sd.swapid = :swapid";

$params = ['swapid' => $swapid];

$records = $DB->get_records_sql($sql, $params);

$myitems = [];
$otheritems = [];
$requestpossible = true;
foreach ($records as $record) {
    if ($record->userid == $userid) {
        $myitems[] = $record;
    } else {
        if (empty($record->userid)) {
            // This request can no longer be fulfilled.
            $requestpossible = false;

            // TODO - could try to see if the user has aquired this item later with a different entry in the user item table.
            // The teacher may have reset / deleted / returned the items which would result in a new entry with these items.
        }
        $otheritems[] = $record;
    }
}



$url = new moodle_url('/blocks/stash/swapdetail.php', ['courseid' => $courseid, 'id' => $swapid]);
$PAGE->set_url($url);

echo $OUTPUT->header();
echo $OUTPUT->heading('Add heading');


// echo $OUTPUT->render_from_template('block_stash/swap_requests', $data);

// print_object($data);
// print_object($records);
echo 'Mine';
print_object($myitems);
echo 'Other';
print_object($otheritems);

if ($requestpossible) {
    echo '<a class="btn btn-primary" href="swapdetail.php?courseid=' . $courseid . '&id=' . $swapid . '&decision=' . \block_stash\swap::BLOCK_STASH_SWAP_APPROVE . '">Accept</a>';
}
echo '<a class="btn btn-secondary" href="swapdetail.php?courseid=' . $courseid . '&id=' . $swapid . '&decision=' . \block_stash\swap::BLOCK_STASH_SWAP_DECLINE . '">Decline</a>';

echo $OUTPUT->footer();
