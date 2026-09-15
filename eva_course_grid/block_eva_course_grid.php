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
 * Block eva_custom_exhibition definition.
 *
 * @package    block_eva_custom_exhibition
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_eva_custom_exhibition extends block_base {

    /**
     * Initializes the block title and name.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_eva_custom_exhibition');
    }

    /**
     * Defines where the block can be added.
     * 
     * @return array
     */
    public function applicable_formats() {
        return array('all' => true);
    }

    /**
     * Generates the content of the block.
     *
     * @return stdClass
     */
    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        // Obtém o renderizador personalizado do componente
        $renderer = $this->page->get_renderer('block_eva_custom_exhibition');
        $this->content->text = $renderer->render_course_grid($this->config);

        return $this->content;
    }

    /**
     * Allows multiple instances of the block on the same page.
     *
     * @return bool
     */
    public function instance_allow_multiple() {
        return true;
    }
}
