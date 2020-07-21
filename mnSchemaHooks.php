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
    
    //add the important files and functions
    add_integration_function('integrate_pre_include', '$sourcedir/SchemaMN/SubsSchemaMN.php',TRUE);
    add_integration_function('integrate_pre_include', '$sourcedir/SchemaMN/LoadSchemaMN.php',TRUE);
    add_integration_function('integrate_load_theme', 'SchemaMN::load_css',TRUE);
    add_integration_function('integrate_pre_load', 'SchemaMN::Load',TRUE);
    add_integration_function('integrate_admin_areas', 'SchemaMN::Admin',TRUE);
    add_integration_function('integrate_messageindex_buttons', 'SchemaMN::MessageIndexButtons', TRUE);
    add_integration_function('integrate_actions', 'SchemaMN::schemaAction', TRUE);
    add_integration_function('integrate_post_end', 'SchemaMN::TopicFieldsHooks', TRUE);
    add_integration_function('integrate_before_create_topic', 'SchemaMN::BeforeCreateTopic', TRUE);
    add_integration_function('integrate_modify_post', 'SchemaMN::TopicFieldsHooks', TRUE);
    add_integration_function('integrate_display_topic', 'SchemaMN::DisplayTopic', TRUE);
    add_integration_function('integrate_prepare_display_context', 'SchemaMN::PrepareDisplayContext', TRUE);

    //table
    db_extend('packages');
    $tables[] = array(
        'name'    => 'mnschemas',
        'columns' => array(
            array(
                'name'     => 'id',
                'type'     => 'int',
                'size'     => 10,
                'unsigned' => true,
                'auto'     => true
            ),
            array(
                'name'     => 'schema_id',
                'type'     => 'varchar',
                'size'     => 255,
                'null' => false,
            ),
            array(
                'name'     => 'schema_desc',
                'type'     => 'varchar',
                'size'     => 255,
                'null' => true,
            ),
            array(
                'name'     => 'schema_label',
                'type'     => 'varchar',
                'size'     => 255,
                'null' => true,
            ),
            array(
                'name'     => 'schema_boards',
                'type'     => 'varchar',
                'size'     => 255,
                'null' => true,
            ),
            array(
                'name'     => 'schema_status',
                'type'     => 'varchar',
                'size'     => 255,
                'null'     => false,
            ),
        ),
        'indexes' => array(
            array(
                'type'    => 'primary',
                'columns' => array('id')
            ),
            array(
                'columns' => array('schema_id',)
            ),
        )
    );
    $tables[] = array(
        'name'    => 'mnschemas_prop',
        'columns' => array(
            array(
                'name'     => 'id',
                'type'     => 'int',
                'size'     => 10,
                'unsigned' => true,
                'auto'     => true
            ),
            array(
                'name'     => 'schema_id',
                'type'     => 'int',
                'size'     => 10,
                'null' => false,
            ),
            array(
                'name' => 'schema_prop',
                'type' => 'varchar',
                'size' => 255,
                'null' => false
            ),
            array(
                'name'     => 'schema_prop_label',
                'type'     => 'varchar',
                'size'     => 255,
                'null' => true,
            ),
            array(
                'name' => 'schema_prop_type',
                'type' => 'varchar',
                'size' => 255,
                'null' => false
            ),
        ),
        'indexes' => array(
            array(
                'type'    => 'primary',
                'columns' => array('id')
            ),
            array(
                'columns' => array('schema_id',)
            ),
        )
    );
    $tables[] = array(
        'name'    => 'mnschemas_subprop',
        'columns' => array(
            array(
                'name'     => 'id',
                'type'     => 'int',
                'size'     => 10,
                'unsigned' => true,
                'auto'     => true
            ),
            array(
                'name' => 'schema_prop_id',
                'type' => 'int',
                'size' => 10,
                'null' => false
            ),
            array(
                'name' => 'schema_prop',
                'type' => 'varchar',
                'size' => 255,
                'null' => false
            ),
            array(
                'name'     => 'schema_subprop_label',
                'type'     => 'varchar',
                'size'     => 255,
                'null' => true,
            ),
        ),
        'indexes' => array(
            array(
                'type'    => 'primary',
                'columns' => array('id')
            ),
            array(
                'columns' => array('schema_prop_id'),
            ),
        )
    );
    foreach($tables as $table)
        $smcFunc['db_create_table']('{db_prefix}' . $table['name'], $table['columns'], $table['indexes'], array(), 'ignore');

    //Schema Field for topics table
    $smcFunc['db_add_column'](
        '{db_prefix}topics',
        array(
            'name'    => 'mnschema_values',
            'type'    => 'text',
            'null'    => true,
        ),
        array(),
        'default',
        'do_nothing'
    );
    //id_schema from topics
    $smcFunc['db_add_column'](
        '{db_prefix}topics',
        array(
            'name'    => 'mnschema_id',
            'type'    => 'int',
            'null'    => true,
        ),
        array(),
        'default',
        'do_nothing'
    );
?>