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
 * Add items to a table.
 *
 * @package    block_stash
 * @copyright  2019 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'core/templates',
    'core/ajax'
], function(Templates, Ajax) {

    function AddItem(useritemid, itemid, name, quantity, imageurl, tablenode, selecttype) {

        var context = {
            id: itemid,
            itemid: itemid,
            name: name,
            imageurl: imageurl,
            useritemid: useritemid,
            quantity: quantity,
            selecttype: selecttype
        };

        Templates.render('block_stash/add_item_detail', context).then(function(html, js) {
            var tablecontentstatus = tablenode.attr('data-status');
            if (tablecontentstatus == 'empty') {
                Templates.replaceNodeContents(tablenode, html, js);
                tablenode.attr('data-status', 'thing');
            } else {
                Templates.appendNodeContents(tablenode, html, js);
            }
        }.bind(this));

    }

    function RemoveItem() {
        window.console.log('lets get this out of here');
    }

    function requestSwap(userid, myuserid, courseid, items, myitems) {
        return Ajax.call([{
            methodname: 'block_stash_create_swap_request',
            args: {
                userid: userid,
                myuserid: myuserid,
                courseid: courseid,
                items: items,
                myitems: myitems
            }
        }])[0].then(function(allitems) {
            return allitems;
        });
    }

    return {
        add: function(useritemid, itemid, name, quantity, imageurl, tablenode, selecttype) {
            AddItem(useritemid, itemid, name, quantity, imageurl, tablenode, selecttype);
        },
        remove: function() {
            RemoveItem();
        },
        submitSwap: function(userid, myuserid, courseid, items, myitems) {
            requestSwap(userid, myuserid, courseid, items, myitems);
        }
    };

});