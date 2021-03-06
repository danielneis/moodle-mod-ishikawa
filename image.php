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
//
/**
 * @package mod_ishikawa
 *
 * @author Luis Henrique Mulinari
 * @author Daniel Neis Araujo
 * @upgrade moodle 2.0+ Caio Bressan Doneda
 **/
      
require_once("../../config.php");
require_once("Ishikawa.class.php");
require_once("lib.php");
$id     = required_param('id', PARAM_INT);  // Course Module ID
$userid = required_param('userid', PARAM_INT); // user id to get submission
$editing  = optional_param('editing', false, PARAM_BOOL);
$src      = optional_param('src', 0, PARAM_INT);
$src_type = optional_param('src_type', 0, PARAM_ALPHA);

$download = optional_param('download', false, PARAM_BOOL);

if (! $cm = get_coursemodule_from_id('ishikawa', $id)) {
    error("Course Module ID was incorrect");
}

if (! $ishikawa = $DB->get_record("ishikawa", array("id" => $cm->instance))) {
    error("ishikawa ID was incorrect");
}

if (! $course = $DB->get_record("course", array("id" => $ishikawa->course))) {
    error("Course is misconfigured");
}

require_login($course, true, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/ishikawa:view', $context);
add_to_log($course->id, "ishikawa", "view", "view.php?id={$cm->id}", $ishikawa->id, $cm->id);

$submission = ishikawa_get_submission($userid, $ishikawa->id);

$blocks = ishikawa_blocks_from_submission($submission);

$connections = ishikawa_connections_from_submission($submission);

$footer = fullname($DB->get_record('user', array('id' => $userid)));

$params = array($userid, $course->id);
$sql = "SELECT g.id, g.name
          FROM {groups} g
          JOIN {groups_members} gm
            ON gm.groupid = g.id
         WHERE gm.userid = ?
           AND g.courseid = ?";
if (!$groups = $DB->get_records_sql($sql, $params)) {
    $groups = array();
} else {
    $footer .= ' - ';
}
// aí conhece! ou não ...
$footer .= implode(array_map(create_function('$a', 'return $a->name;'), $groups), ', ');
$ishikawa = new Ishikawa($blocks, $connections, $src, $src_type, $ishikawa->name, $footer);
$ishikawa->draw($editing, $download);
?>
