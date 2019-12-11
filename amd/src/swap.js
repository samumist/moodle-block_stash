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
 * User swap code
 *
 * @package    block_stash
 * @copyright  2019 Adrian Greeve - adriangreeve.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(["core/ajax"], function(Ajax) {

    function init(courseid, userid, myuserid) {
        window.console.log(courseid);
        window.console.log(userid);
        window.console.log(myuserid);
        window.console.log('how about now?');
        get_user_stash(courseid, userid);
    }

    function get_user_stash(courseid, userid) {
        return Ajax.call([{
            methodname: 'block_stash_get_user_stash_items',
            args: {
                courseid: courseid,
                userid: userid
            }
        }])[0].then(function(data) {
            window.console.log(data);
        });
    }

    return init;

});