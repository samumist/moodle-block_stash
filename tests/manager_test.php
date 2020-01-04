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

    public function test_create_swap_request() {

        $this->resetAfterTest();


        $plugin = $this->getDataGenerator()->get_plugin_generator('block_stash');
        $course = $this->getDataGenerator()->create_course();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $stash = $plugin->create_stash(['courseid' => $course->id]);

        $manager = manager::get($course->id);

        $items = [
            ['id' => 23, 'quantity' => 3],
            ['id' => 567, 'quantity' => 2],
            ['id' => 23, 'quantity' => 2]
        ];

        $this->setUser($user2);

        $manager->create_swap_request($user1->id, $user2->id, $items, []);


        // print_object($manager->is_enabled());
    }


}