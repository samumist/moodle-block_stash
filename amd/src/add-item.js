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

import Templates from 'core/templates';

const addItem = (e) => {
    e.preventDefault();

    let addbtn = e.currentTarget;

    let selectedtype = addbtn.getAttribute('data-add-item');
    let selectedobject = document.getElementById(selectedtype);
    let optionelement = selectedobject.options[selectedobject.selectedIndex];
    let context = {
        id: optionelement.getAttribute('data-itemid'),
        itemid: optionelement.getAttribute('data-itemid'),
        name: optionelement.innerText,
        imageurl: optionelement.getAttribute('data-imgurl'),
        useritemid: optionelement.value,
        quantity: optionelement.getAttribute('data-amount'),
        selecttype: selectedtype
    };

    let tableelement = document.querySelector('table[data-type="' + selectedtype + '"]');
    Templates.render('block_stash/add_item_detail', context).then((html, js) => {
        if (tableelement.getAttribute('data-status') == 'empty') {
            Templates.replaceNodeContents(tableelement, html, js);
            tableelement.setAttribute('data-status', 'thing');
        } else {
            Templates.appendNodeContents(tableelement, html, js);
        }
    });
};

export const init = () => {
    let addbtns = document.querySelectorAll('[data-add-item]');
    addbtns.forEach((addbutton) => {
        addbutton.addEventListener('click', addItem);
    });
};
