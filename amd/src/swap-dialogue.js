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
 * Form for swapping items.
 *
 * @package    block_stash
 * @copyright  2016 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'core/templates',
    'block_stash/dialogue-base',
], function(Templates, DialogueBase) {

    /**
     * Detail dialogue class.
     *
     * @param {Number} itemId The item ID.
     */
    function SwapForm(courseid) {
        this._courseid = courseid;
        DialogueBase.prototype.constructor.apply(this, []);
    }
    SwapForm.prototype = Object.create(DialogueBase.prototype);
    SwapForm.prototype.constructor = SwapForm;

    SwapForm.prototype._courseid = null;


    /**
     * Render the dialogue.
     *
     * @method _render
     * @return {Promise}
     */
    SwapForm.prototype._render = function() {
        var context = {
            courseid: this._courseid
        };

        return Templates.render('block_stash/swap_form', context).then(function(html, js) {
            this._setDialogueContent(html);
            this.center();
            Templates.runTemplateJS(js);
        }.bind(this));
    };

    return SwapForm;

});
