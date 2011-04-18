<?php

    require_once("../../config.php");
    require_once("Ishikawa.class.php");
    require_once("lib.php");

    $id = required_param('id', PARAM_INT);  // Course Module ID
    $userid = required_param('userid', PARAM_INT); // user id to get submission

    if (! $cm = get_coursemodule_from_id('ishikawa', $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $ishikawa = get_record("ishikawa", "id", $cm->instance)) {
        error("ishikawa ID was incorrect");
    }

    if (! $course = get_record("course", "id", $ishikawa->course)) {
        error("Course is misconfigured");
    }

    require_login($course, true, $cm);

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('mod/ishikawa:view', $context);

    add_to_log($course->id, "ishikawa", "view", "view.php?id={$cm->id}", $ishikawa->id, $cm->id);

    $submission = ishikawa_get_submission($userid, $ishikawa->id);

    /// Print the page header
    $blocks = ishikawa_blocks_from_submission($submission);

    $connections = array();

    $ishikawa = new Ishikawa($blocks, $connections);
    $ishikawa->draw();
?>
