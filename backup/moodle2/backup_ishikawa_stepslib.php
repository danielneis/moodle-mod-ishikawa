<?php
/**
  * Define all the backup steps that will be used by the backup_ishikawa_activity_task
  */
/**
 * Define the complete choice structure for backup, with file and id annotations
 */     

class backup_choice_activity_structure_step extends backup_activity_structure_step {
       
      protected function define_structure() {
          // To know if we are including userinfo
          $userinfo = $this->get_setting_value('userinfo');
          
          // Define each element separated
          $ishikawa = new backup_nested_element('ishikawa', array('id'), array(
                                              'name', 'intro', 'maxchar', 'maxlines',
                                              'maxcolumns', 'grade', 'preventlate', 'timedue',
                                              'timeavaliable', 'introformat', 'timemodified'));
          
          $ishikawa_axis_blocks = new backup_nested_element('ishikawa_axis_blocks');
          $ishikawa_axis_block = new backup_nested_element('ishikawa_axis_block', array('id'), array(
                                              'submissionid', 'nivel_x', 'texto', 'cor'));
                            
          $ishikawa_causes_blocks = new backup_nested_element('ishikawa_causes_blocks');
          $ishikawa_causes_block = new backup_nested_element('ishikawa_causes_block', array('id'), array(
                                              'submissionid', 'nivel_x', 'nivel_y', 'texto' ,'cor'));

          $ishikawa_connections = new backup_nested_element('ishikawa_connections');
          $ishikwa_connection = new backup_nested_element('ishikawa_connection', array('id'), array(
                                              'submissionid', 'src_id', 'src_type', 'dst_id' ,'dst_type'));
          
          $ishikawa_consequences_blocks = new backup_nested_element('ishikawa_consequences_blocks');
          $ishikawa_consequences_block = new backup_nested_element('ishikawa_consequences_block', array('id'), array(
                                              'submissionid', 'nivel_x', 'nivel_y', 'texto' ,'cor'));
          
          $ishikawa_grades = new backup_nested_element('ishikawa_grades');
          $ishikawa_grade = new backup_nested_element('ishikawa_grade', array('id'), array(
                                              'userid', 'grade', 'feedback', 'timecreated' ,'timemodified'));
           
          $ishikawa_submissions = new backup_nested_element('ishikawa_submissions');
          $ishikawa_submission = new backup_nested_element('ishikawa_submission', array('id'), array(
                                              'ishikawaid', 'userid', 'tail_text', 'head_text' ,'timecreated', 'timemodified'));
          
          // Build the tree
          $ishikawa->add_child($ishikawa_axis_blocks);
          $ishikawa_axis_blocks->add_child($ishikawa_axis_block);

          $ishikawa->add_child($ishikawa_causes_blocks);
          $ishikawa_causes_blocks->add_child($ishikawa_causes_block);
          
          $ishikawa->add_child($ishikawa_connections);
          $ishikawa_connections->add_child($ishikawa_connection);

          $ishikawa->add_child($ishikawa_consequences_blocks);
          $ishikawa_consequences_blocks->add_child($ishikawa_consequences_block);          
          
          $ishikawa->add_child($ishikawa_grades);
          $ishikawa_grades->add_child($ishikawa_grade);          

          $ishikawa->add_child($ishikawa_submissions);
          $ishikawa_submissions->add_child($ishikawa_submission);
          
          
          // Define sources
          
          // Define id annotations
          
          // Define file annotations
          
          // Return the root element (choice), wrapped into standard activity structure
          return $this->prepare_activity_structure($ishikawa); 
         }
  }
