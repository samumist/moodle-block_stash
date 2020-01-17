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
 * Swap class
 *
 * @package    block_stash
 * @copyright  2020 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash;

/**
 * Swap class
 *
 * @package    block_stash
 * @copyright  2020 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class swap {

    public const TABLE = 'block_stash_swap';
    public const TABLE_DETAIL = 'block_stash_swap_detail';

    private $stashid;
    private $intiator;
    private $receiver;
    /** @var array $initatoritems an array of items */
    private $initiatoritems;
    /** @var array $receiveritems an array of items */
    private $receiveritems;
    private $message;
    private $messageformat;
    private $status;

    public function __construct($stashid, $initiator, $receiver, $initiatoritems, $receiveritems, $message, $messageformat) {
        $this->stashid = $stashid;
        $this->initiator = $initiator;
        $this->receiver = $receiver;
        $this->initiatoritems = $initiatoritems;
        $this->receiveritems = $receiveritems;
        $this->message = $message;
        $this->messageformat = $messageformat;
    }


    public function save() {
        global $DB;
        // Save swap.
        $rawdata = (object) [
            'stashid' => $this->stashid,
            'initiator' => $this->initiator,
            'receiver' => $this->receiver,
            'message' => $this->message,
            'messageformat' => $this->messageformat,
            'timecreated' => time(),
        ];
        $swapid = $DB->insert_record(self::TABLE, $rawdata);
        // Save swap detail.
        // Quick and dirty.
        $this->save_detail($this->initiatoritems, $swapid);
        $this->save_detail($this->receiveritems, $swapid);
    }

    private function save_detail($swapitems, $swapid) {
        global $DB;
        foreach ($swapitems as $items) {
            $data = (object) [
                'swapid' => $swapid,
                'useritemid' => $items['useritem']->get_id(),
                'quantity' => $items['quantity']
            ];
            $DB->insert_record(self::TABLE_DETAIL, $data);
        }
    }

}