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

    public const BLOCK_STASH_SWAP_APPROVE = 1;
    public const BLOCK_STASH_SWAP_DECLINE = 2;
    public const BLOCK_STASH_SWAP_COMPLETED = 3;

    private $id;
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

    public function __construct($stashid, $initiator, $receiver, $initiatoritems = [], $receiveritems = [], $message = '',
            $messageformat = 1, $status = null) {
        $this->stashid = $stashid;
        $this->initiator = $initiator;
        $this->receiver = $receiver;
        $this->initiatoritems = $initiatoritems;
        $this->receiveritems = $receiveritems;
        $this->message = $message;
        $this->messageformat = $messageformat;
        $this->status = $status;
    }

    public function set_id($id) {
        $this->id = $id;
    }

    public function set_status($status) {
        $this->status = $status;
    }

    public function get_initiator_items() {
        return $this->initiatoritems;
    }

    public function get_receiver_items() {
        return $this->receiveritems;
    }

    public function get_receiver_id() {
        return $this->receiver;
    }

    public function get_initiator_id() {
        return $this->initiator;
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
            'status' => $this->status
        ];

        if (isset($this->id)) {
            $rawdata->id = $this->id;
            unset($rawdata->timecreated);
            $DB->update_record(self::TABLE, $rawdata);
        } else {
            $swapid = $DB->insert_record(self::TABLE, $rawdata);
            // Save swap detail.
            // Quick and dirty.
            $this->save_detail($this->initiatoritems, $swapid);
            $this->save_detail($this->receiveritems, $swapid);
        }

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

    public static function load($swapid, $detailsaswell = false) {
        global $DB;
        $record = $DB->get_record(self::TABLE, ['id' => $swapid]);
        $initiatoritems = [];
        $receiveritems = [];
        if ($detailsaswell) {
            $sql = "SELECT sd.id, sd.quantity, ui.id as useritemid, ui.itemid, ui.userid, ui.quantity as uiquantity, ui.timecreated, ui.timemodified, ui.version
                    FROM {" . self::TABLE_DETAIL. "} sd
                    LEFT JOIN {block_stash_user_items} ui ON sd.useritemid = ui.id
                    WHERE sd.swapid = :swapid";

            $params = ['swapid' => $swapid];
            $records = $DB->get_records_sql($sql, $params);
            foreach ($records as $detail) {
                $data = (object) [
                    'id' => $detail->useritemid,
                    'itemid' => $detail->itemid,
                    'userid' => $detail->userid,
                    'quantity' => $detail->uiquantity,
                    'timecreated' => $detail->timecreated,
                    'timemodified' => $detail->timemodified,
                    'version' => $detail->version
                ];
                $useritem = new user_item($detail->useritemid, $data);
                if ($record->initiator == $detail->userid) {
                    $initiatoritems[] = ['useritem' => $useritem, 'quantity' => $detail->quantity];
                }
                if ($record->receiver == $detail->userid) {
                    $receiveritems[] = ['useritem' => $useritem, 'quantity' => $detail->quantity];
                }
            }
        }

        $swap = new swap($record->stashid, $record->initiator, $record->receiver, $initiatoritems, $receiveritems, $record->message, $record->messageformat);
        $swap->set_id($swapid);
        return $swap;
    }

}
