<?php
    global $user_info;

    if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF')) {
        require_once(dirname(__FILE__) . '/SSI.php');
    } elseif (!defined('SMF')) {
        die('<b>Error:</b> Cannot install - please verify that you put this file in the same place as SMF\'s index.php and SSI.php files.');
    }

    if ((SMF == 'SSI') && !$user_info['is_admin']) {
        die('Admin privileges required.');
    }
    
    //remove the important files and functions
    remove_integration_function('integrate_pre_include', '$sourcedir/SchemaMN/SubsSchemaMN.php',TRUE);
    remove_integration_function('integrate_pre_include', '$sourcedir/SchemaMN/LoadSchemaMN.php',TRUE);
    remove_integration_function('integrate_load_theme', 'SchemaMN::load_css',TRUE);
    remove_integration_function('integrate_pre_load', 'SchemaMN::Load',TRUE);
    remove_integration_function('integrate_admin_areas', 'SchemaMN::Admin',TRUE);
    remove_integration_function('integrate_messageindex_buttons', 'SchemaMN::MessageIndexButtons', TRUE);
    remove_integration_function('integrate_actions', 'SchemaMN::schemaAction', TRUE);
    remove_integration_function('integrate_post_end', 'SchemaMN::TopicFieldsHooks', TRUE);
    remove_integration_function('integrate_before_create_topic', 'SchemaMN::BeforeCreateTopic', TRUE);
    remove_integration_function('integrate_modify_post', 'SchemaMN::TopicFieldsHooks', TRUE);
    remove_integration_function('integrate_display_topic', 'SchemaMN::DisplayTopic', TRUE);
    remove_integration_function('integrate_prepare_display_context', 'SchemaMN::PrepareDisplayContext', TRUE);
?>