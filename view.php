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
require_once("lib.php");
$id = required_param('id', PARAM_INT);  // Course Module ID

if (! $cm = get_coursemodule_from_id('ishikawa', $id)) {
    print_error("Course Module ID was incorrect");
}

if (! $ishikawa = $DB->get_record("ishikawa", array('id' => $cm->instance))) {
    print_error("ishikawa ID was incorrect");
}

if (! $course = $DB->get_record("course", array('id' => $ishikawa->course))) {
    print_error("Course is misconfigured");
}

require_login($course, true, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/ishikawa:view', $context);
add_to_log($course->id, "ishikawa", "view", "view.php?id={$cm->id}", $ishikawa->id, $cm->id);

/// Print the page header

/// Some capability checks.
if (empty($cm->visible) and !has_capability('moodle/course:viewhiddenactivities', $context)) {
    notice(get_string("activityiscurrentlyhidden"));
}
$submission = ishikawa_get_submission($USER->id, $ishikawa->id);
$strishikawa = get_string('modulename', 'ishikawa');
$navigation = build_navigation('', $cm);
$PAGE->set_url('/mod/ishikawa/view.php', array('id'=>$course->id));
$PAGE->navbar->add($strishikawa);
$PAGE->set_title($strishikawa);
$PAGE->set_heading($course->fullname);
$PAGE->set_button($OUTPUT->update_module_button($cm->id, 'ishikawa')); //New function update_module_button
echo $OUTPUT->header();

$img = '<img src="' . $OUTPUT->pix_url('icon', 'ishikawa') . '" class="activityicon" alt="" />' ;
echo $OUTPUT->heading( $img.'&nbsp;'. $ishikawa->name);
$groupmode = groups_get_activity_groupmode($cm);
groups_print_activity_menu($cm, $CFG->wwwroot.'/mod/ishikawa/view.php?id='.$id);
$group = groups_get_activity_group($cm, true);

if (has_capability('mod/ishikawa:grade', $context)) {
    echo '<div class="reportlink">';
    if ($count = ishikawa_count_submissions($ishikawa->id,$context, $group)) {
        echo '<a href="submissions.php?id=',$cm->id,'">',get_string('viewsubmissions', 'ishikawa', $count),'</a>';
    } else {
        echo '<a href="submissions.php?id=',$cm->id,'">',get_string('noattempts', 'ishikawa'),'</a>';
    }
    echo '</div>';
}

echo $OUTPUT->box(format_text($ishikawa->intro), 'generalbox', 'intro');

ishikawa_view_dates($ishikawa);

ishikawa_view_submission_feedback($ishikawa, $submission, $course);

$now = time();
if ($submission) {
    if (ishikawa_isopen($ishikawa)) {
       echo  '<p><a href="edit.php?id=',$cm->id,'" >',get_string('edit_blocks', 'ishikawa'),'</a></p>',
             '<p><a href="connections.php?id=',$cm->id,'" >',get_string('edit_connections', 'ishikawa'),'</a></p>';
    }
    echo '<p><a href="image.php?id=',$cm->id,'&amp;userid=',$USER->id,'&amp;download=1">',get_string('save_image', 'ishikawa'), '</a></p>',
         '<p><img src="image.php?id=',$cm->id,'&amp;userid=',$USER->id,'" /></p>';

} else {
    if (ishikawa_isopen($ishikawa)) {
        echo "<a href='edit.php?id={$id}'>",get_string('create_diagram', 'mod_ishikawa'),'</a>';
    }
}

echo $OUTPUT->footer();
