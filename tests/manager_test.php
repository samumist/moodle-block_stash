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
 * Manager API tests.
 *
 * @package    block_stash
 * @category   test
 * @copyright  2020 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

use block_stash\manager;
use block_stash\drop_pickup;
use block_stash\user_item;

/**
 * Manager API testcase class.
 *
 * @package    block_stash
 * @category   test
 * @copyright  2020 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_stash_manager_testcase extends advanced_testcase {

    private function add_block_instance($context) {
        global $DB;

        $record = (object) [
            'blockname' => 'stash',
            'parentcontextid' => $context->id,
            'showinsubcontexts' => 0,
            'requiredbytheme' => 0,
            'pagetypepattern' => 'course-view-*',
            'defaultregion' => 'side-pre',
            'defaultweight' => 1,
            'timecreated' => time(),
            'timemodified' => time()
        ];
        $DB->insert_record('block_instances', $record);
    }

    public function test_create_swap_request() {

        $this->resetAfterTest();

        $plugin = $this->getDataGenerator()->get_plugin_generator('block_stash');
        $course = $this->getDataGenerator()->create_course();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Need to enrol these users into the course.
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student');

        $stash = $plugin->create_stash(['courseid' => $course->id]);

        $item1 = $plugin->create_item(['stash' => $stash, 'name' => 'gold idol']);
        $item2 = $plugin->create_item(['stash' => $stash, 'name' => 'coins']);
        $item3 = $plugin->create_item(['stash' => $stash, 'name' => 'marshmallow']);

        $plugin->create_user_item(['item' => $item1, 'userid' => $user1->id, 'quantity' => 1]);
        $plugin->create_user_item(['item' => $item2, 'userid' => $user2->id, 'quantity' => 4]);
        $plugin->create_user_item(['item' => $item3, 'userid' => $user2->id, 'quantity' => 2]);

        $manager = manager::get($course->id);
        $context = $manager->get_context();
        $this->add_block_instance($context);

        $items = [['id' => $item1->get_id(), 'quantity' => 1]];
        // Double up of the first item to check that merging of items works.
        $myitems = [
            ['id' => $item2->get_id(), 'quantity' => 1],
            ['id' => $item2->get_id(), 'quantity' => 2],
            ['id' => $item3->get_id(), 'quantity' => 1],
        ];

        $this->setUser($user2);

        $manager->create_swap_request($user1->id, $user2->id, $items, $myitems);

        global $DB;

        // $result = $DB->get_records('block_stash_user_items');
        // print_object($result);


        $result = $DB->get_records('block_stash_swap');
        // print_object($result);

        $result = $DB->get_records('block_stash_swap_detail');
        // print_object($result);

        // print_object($manager->is_enabled());
    }


}