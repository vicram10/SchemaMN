<?php
/**
 * SubsSchemaMN.php
 *
 * @package MNSchema
 * @link https://www.mercado-negro.net
 * @author thedarkness https://www.mercado-negro.net/profile/thedarkness/
 * @copyright 2020 thedarkness
 *
 * @version 0.1
 */

if (!defined('SMF'))
    die('Hacking attempt...');

//Principal Class
class SchemaMN
{
    //read the specific css
    public static function load_css(){
        global $boarddir,$context, $boardurl, $txt, $modSettings, $language;
        $context['html_headers'] .= '
        <script>
            var mn_schema_prop = "'. $txt['schmn_admin_prop'] .'";
            var mn_schema_prop_help = "'. $txt['schmn_admin_prop_help'] .'";
            var mn_schema_prop_label = "'. $txt['schmn_prop_label'] .'";
            var mn_schema_prop_label_help = "'. $txt['schmn_type_label_desc'] .'";
        </script>
        <link rel="stylesheet" href="'. $boardurl .'/Sources/SchemaMN/mnschema.css" />
        <link rel="stylesheet" href="'. $boardurl .'/Sources/SchemaMN/sweetalert2.min.css" />
        <script src="'. $boardurl .'/Sources/SchemaMN/sweetalert2.all.min.js"></script>
        <script src="'. $boardurl .'/Sources/SchemaMN/mnschema.js" async defer></script>';
    }

    /**
     * Important Things
     */
    public static function Load(){
        global $sourcedir;
        //load template
        require_once($sourcedir.'/SchemaMN/MNSchema.template.php');
        loadLanguage('SchemaMN');
    }
    /**
     * AdminArea
     */
    public static function Admin(&$admin_areas){
        global $settings, $txt, $context, $boardurl;
        addInlineCss('
        .main_icons.mnschema::before {
                background:url(' . $boardurl . '/Sources/SchemaMN/img/icon-admin-2.png) no-repeat 0 0 !important;
                width: 32px;
                height: 32px;
        }
        .large_admin_menu_icon.mnschema::before {
                background:url(' . $boardurl . '/Sources/SchemaMN/img/icon-admin-2.png) no-repeat 0 0;
                width: 32px;
                height: 32px;
        }');
        //admin areas
        $admin_areas['config']['areas']['mnschema'] = array(
                'label' => $txt['schmn_admin_title'],
                'icon' => 'mnschema',
                'function' => function () {
                    self::settingActions();
                },
                'subsections' => array(
                        'main'    => array($txt['schmn_admin_list_title']),
                        'add'    => array($txt['schmn_admin_add_title']),
                        'subtypes' => array($txt['schmn_admin_subtype_title']),
                )
        );
    }

    ///subactions
    public static function settingActions(){
        global $context, $txt, $sourcedir, $boarddir, $smcFunc;
        // Make sure the administrator has a valid session...
        validateSession();

        $context['page_title'] = $txt['schmn_admin_title'];
        $subActions = array(
                'main' => 'mainTabSettings',
                'add' => 'addTabSettings',
                'edit' => 'editTabSettings',
                'status' => 'statusTabSettings',
                'subtypes' => 'subtypesTabSettings',
        );

        $context[$context['admin_menu_name']]['tab_data'] = array(
            'title' => $txt['schmn_admin_main'],
            'tabs' => array(
                'main' => array(
                    'description' => $txt['schmn_admin_main_desc'],
                ),
                'add' => array(
                    'description' => $txt['schmn_admin_main_desc'],
                ),
                'subtypes' => array(
                    'description' => $txt['schmn_admin_main_desc'],
                ),
            ),
        );
        $_REQUEST['sa'] = !empty($_REQUEST['sa']) ? $_REQUEST['sa'] : 'main';
        call_helper(__CLASS__ . '::' . $subActions[$_REQUEST['sa']]);
    }

    ///Settings
    public static function mainTabSettings(){
        global $context, $smcFunc, $txt;
        // Make sure the administrator has a valid session...
        validateSession();
        //list of schemas
        $sql = $smcFunc['db_query']('', '
            SELECT id, schema_id, schema_desc, schema_status
            FROM {db_prefix}mnschemas',
            array()
        );
        $context['mnschemas_list'] = array();
        while($row = $smcFunc['db_fetch_assoc']($sql)){
            $context['mnschemas_list'][$row['id']] = array(
                'type' => $row['schema_id'],
                'desc' => $row['schema_desc'],
                'status' => $row['schema_status'],
            );
        }
        $smcFunc['db_free_result']($sql);
        $context['infobox'] = isset($_GET['infobox']) ? true : false;
        //template
        $context['sub_template'] = 'schema_admin';
    }

    ///Add new schemas
    public static function addTabSettings(){
        global $context, $sourcedir, $txt, $smcFunc;

        // Make sure the administrator has a valid session...
        validateSession();

        //save?
        if (isset($_GET['save'])){
            if (empty($_POST['schmn_type']))
                fatal_lang_error('schmn_msg_no_type', true);
            if (empty($_POST['schmn_prop']))
                fatal_lang_error('schmn_msg_no_prop');

            //variables
            $type = $_POST['schmn_type'];
            $description = $_POST['schmn_type_desc'];
            $schema_label = $_POST['schmn_label'];
            $prop_one = $_POST['schmn_prop'];
            $prop_label_one = $_POST['schmn_prop_label'];
            $prop_count = (int) $_POST['schmn_prop_count'];
            $prop_boards = '';
            //boards
            if (!isset($_POST['schmn_boards'])){
                fatal_lang_error('schmn_msg_no_boards');
            }else{
                foreach($_POST['schmn_boards'] as $id => $key){
                    $prop_boards = empty($prop_boards) ? $key : $prop_boards . ','. $key;
                }
            }
            //add the type
            $id_type = $smcFunc['db_insert']('ignore', '{db_prefix}mnschemas',
                array('id' => 'int', 'schema_id' => 'string-255', 'schema_desc' => 'string-255', 'schema_label' => 'string-255', 'schema_boards' => 'string-255', 'schema_status' => 'string-255'),
                array(0, $type, $description, $schema_label, $prop_boards, 'ENABLED'),
                array('id'),
                1
            );
            //add the first prop with type
            $prop_one_id = $smcFunc['db_insert']('ignore', '{db_prefix}mnschemas_prop',
                array('id' => 'int', 'schema_id' => 'int', 'schema_prop' => 'string-255', 'schema_prop_label' => 'string-255'),
                array(0, $id_type, $prop_one, $prop_label_one),
                array('id'),
                1
            );
            //others prop
            if ($prop_count > 0){
                for ($i = 1; $i <= $prop_count;$i++){
                    if (isset($_POST['schmn_prop_'.$i])){
                        $prop_other = $_POST['schmn_prop_'.$i];
                        $prop_other_label = $_POST['schmn_prop_label_'.$i];
                        //add others prop
                        $smcFunc['db_insert']('ignore', '{db_prefix}mnschemas_prop',
                        array('id' => 'int', 'schema_id' => 'int', 'schema_prop' => 'string-255', 'schema_prop_label' => 'string-255'), //fixed issue #6, 13032022, vicram10
                            array(0, $id_type, $prop_other, $prop_other_label),
                            array('id'),
                            1
                        );
                    }
                }
            }

            //redirect to main admin Schema Mod
            redirectexit('action=admin;area=mnschema;sa=main;infobox;'.$context['session_var'].'='.$context['session_id']);
        }

        //load all boards
        $sql = $smcFunc['db_query']('',
            'SELECT id_board, name
            FROM {db_prefix}boards',
            array()
        );
        $context['mnschema_boards'] = array();
        while($row = $smcFunc['db_fetch_assoc']($sql)){
            $context['mnschema_boards'][$row['id_board']] = array(
                'name' => $row['name'],
            );
        }

        //template
        $context['sub_template'] = 'schema_add';
    }

    //Edit Template
    public static function editTabSettings(){
        global $context, $sourcedir, $txt, $smcFunc;

        // Make sure the administrator has a valid session...
        validateSession();

        //save?
        if (isset($_GET['save'])){

            $prop_boards = '';
            //boards
            if (!isset($_POST['schmn_boards'])){
                fatal_lang_error('schmn_msg_no_boards');
            }else{
                foreach($_POST['schmn_boards'] as $id => $key){
                    $prop_boards = empty($prop_boards) ? $key : $prop_boards . ','. $key;
                }
            }

            //id principal
            $id_type = (int) $_POST['schema_id'];
            $prop_label = $_POST['schmn_prop_label'];
            $description = $_POST['schmn_type_desc'];

            //we update the main schema
            $smcFunc['db_query']('',
            "UPDATE {db_prefix}mnschemas
            SET schema_boards = {string:prop_boards},
                schema_label = {string:label},
                schema_desc = {string:description}
            WHERE id = {int:schema_id}",
            array(
                'schema_id' => $id_type,
                'prop_boards' => $prop_boards,
                'label' => $prop_label,
                'description' => $description,
            )
            );

            //variables
            $prop_count = (int) $_POST['schmn_prop_count'];
            //edit prop?
            if ($prop_count > 0){
                for ($i = 1; $i <= $prop_count;$i++){
                    if (isset($_POST['schmn_prop_'.$i])){
                        $prop_other = $_POST['schmn_prop_'.$i];
                        $prop_other_label = $_POST['schmn_prop_label_'.$i];
                        if (empty($prop_other))
                            fatal_lang_error('schmn_msg_no_prop');

                        //add others prop
                        $smcFunc['db_insert']('ignore', '{db_prefix}mnschemas_prop',
                        array('id' => 'int', 'schema_id' => 'int', 'schema_prop' => 'string-255', 'schema_prop_label' => 'string-255'),
                            array(0, $id_type, $prop_other, $prop_other_label),
                            array('id'),
                            1
                        );
                    }
                }
            }

            //redirect to main admin Schema Mod
            redirectexit('action=admin;area=mnschema;sa=main;infobox;'.$context['session_var'].'='.$context['session_id']);
        }

        //not found ID?
        if (!isset($_GET['id']))
            fatal_lang_error('schmn_msg_no_id', false);

        $id = (int) $_GET['id'];
        $context['mnschema_id'] = $id;

        //list of schemas
        $sql = $smcFunc['db_query']('', '
            SELECT id, schema_id, schema_label, schema_desc, schema_boards
            FROM {db_prefix}mnschemas
            WHERE id = {int:id_schema}',
            array(
                'id_schema' => $id,
            )
        );
        //head
        $context['mnschema'] = array();
        while($row = $smcFunc['db_fetch_assoc']($sql)){
            $context['mnschema'][$row['id']] = array(
                'type' => $row['schema_id'],
                'label' => $row['schema_label'],
                'desc' => $row['schema_desc'],
                'boards' => !empty($row['schema_boards']) ? explode(',', $row['schema_boards']) : array(),
                'prop' => array(),
            );
        }
        $smcFunc['db_free_result']($sql);
        //itemprop
        $sqlProp = $smcFunc['db_query']('', '
            SELECT id, schema_prop, schema_prop_label
            FROM {db_prefix}mnschemas_prop
            WHERE schema_id = {int:id_schema}',
            array(
                'id_schema' => $id,
            )
        );
        while($row = $smcFunc['db_fetch_assoc']($sqlProp)){
            $context['mnschema'][$id]['prop'][$row['id']] = array(
                'name' => $row['schema_prop'],
                'label' => $row['schema_prop_label'],
            );
        }
        $smcFunc['db_free_result']($sqlProp);

        //load all boards
        $sql = $smcFunc['db_query']('',
            'SELECT id_board, name
            FROM {db_prefix}boards',
            array()
        );
        $context['mnschema_boards'] = array();
        while($row = $smcFunc['db_fetch_assoc']($sql)){
            $context['mnschema_boards'][$row['id_board']] = array(
                'name' => $row['name'],
            );
        }

        //template
        $context['sub_template'] = 'schema_edit';
    }

    //Edit Template
    public static function statusTabSettings(){
        global $context, $sourcedir, $txt, $smcFunc;

        // Make sure the administrator has a valid session...
        validateSession();
        $resp = array(
            'cod' => 0,
            'msg' => '',
            'title' => $txt['schmn_title_notify'],
        );
        $msgError = "";
        //not found ID?
        if (!isset($_GET['id']))
            $msgError = $txt['schmn_msg_no_id'];

        if (!empty($msgError)){
            $resp = array(
                'cod' => 1,
                'msg' => $msgError,
                'title' => $txt['schmn_title_notify'],
            );
        }else{

            $id = (int) $_GET['id'];
            $status = $_GET['status'];
            //list of schemas
            $smcFunc['db_query']('', '
                UPDATE {db_prefix}mnschemas
                SET schema_status = {string:status}
                WHERE id = {int:id_schema}',
                array(
                    'id_schema' => $id,
                    'status' => $status == 'ENABLED' ? 'DISABLED' : 'ENABLED',
                )
            );
            $resp = array(
                'cod' => 0,
                'msg' => $status == 'ENABLED' ? $txt['schmn_msg_disable'] : $txt['schmn_msg_enable'],
                'title' => $txt['schmn_title_notify'],
            );
        }

        //response
        // Print the data.
        smf_serverResponse($smcFunc['json_encode']($resp));
        die;
    }

    //subtypes schemas
    public static function subtypesTabSettings(){
        global $context, $sourcedir, $txt, $smcFunc;

        // Make sure the administrator has a valid session...
        validateSession();

        if (!isset($_GET['edit'])){
            //list of schemas
            $sql = $smcFunc['db_query']('',
                'SELECT m.schema_id, m.schema_status, p.id, p.schema_prop, p.schema_prop_label, p.schema_prop_type
                FROM {db_prefix}mnschemas_prop p, {db_prefix}mnschemas m
                WHERE p.schema_id = m.id
                ORDER BY m.id ASC',
                array()
            );
            $context['mnschemas_prop_list'] = array();
            while($row = $smcFunc['db_fetch_assoc']($sql)){
                $context['mnschemas_prop_list'][$row['id']] = array(
                    'type' => $row['schema_id'],
                    'status' => $row['schema_status'],
                    'prop_id' => (int) $row['id'],
                    'itemprop' => $row['schema_prop'],
                    'itemprop_label' => $row['schema_prop_label'],
                    'prop_itemtype' => !empty($row['schema_prop_type']) ? $row['schema_prop_type'] : '',
                    'subprop' => array(),
                );
            }
            $smcFunc['db_free_result']($sql);

            //subprop
            $sql = $smcFunc['db_query']('',
                'SELECT id, schema_prop_id, schema_prop, schema_subprop_label
                FROM {db_prefix}mnschemas_subprop',
                array()
            );

            while($row = $smcFunc['db_fetch_assoc']($sql)){
                $context['mnschemas_prop_list'][$row['schema_prop_id']]['subprop'][] = array(
                    'subprop_id' => (int) $row['id'],
                    'schema_subprop' => $row['schema_prop'],
                    'schema_subprop_label' => $row['schema_subprop_label'],
                );
            }
            $smcFunc['db_free_result']($sql);

            //emitiremos el mensaje
            $context['infobox'] = isset($_GET['infobox']) ? true : false;

            //template
            $context['sub_template'] = 'schema_subtype';
        }else{

            //save?
            if (isset($_GET['save'])){

                //variables
                $prop_id = (int) $_POST['prop_id'];
                $schema_type = $_POST['schmn_type'];
                $prop_count = (int) $_POST['schmn_prop_count'];

                //empty type?
                if (empty($schema_type))
                    fatal_lang_error('schmn_msg_no_type');

                //we update the main schema
                $smcFunc['db_query']('',
                    "UPDATE {db_prefix}mnschemas_prop
                    SET schema_prop_type = {string:prop_type}
                    WHERE id = {int:id_prop}",
                    array(
                        'id_prop' => $prop_id,
                        'prop_type' => $schema_type,
                    )
                );
                //edit prop?
                if ($prop_count > 0){
                    for ($i = 1; $i <= $prop_count;$i++){
                        if (isset($_POST['schmn_prop_'.$i])){
                            $prop_other = $_POST['schmn_prop_'.$i];
                            $prop_other_label = $_POST['schmn_prop_label_'.$i];
                            if (empty($prop_other))
                                fatal_lang_error('schmn_msg_no_prop');

                            //add others prop
                            $smcFunc['db_insert']('ignore', '{db_prefix}mnschemas_subprop',
                            array('id' => 'int', 'schema_prop_id' => 'int', 'schema_prop' => 'string-255', 'schema_subprop_label' => 'string-255'),
                                array(0, $prop_id, $prop_other, $prop_other_label),
                                array('id'),
                                1
                            );
                        }
                    }
                }

                //redirect to main admin Schema Mod
                redirectexit('action=admin;area=mnschema;sa=subtypes;infobox;'.$context['session_var'].'='.$context['session_id']);
            }

            //not found ID?
            if (!isset($_GET['id']))
                fatal_lang_error('schmn_msg_no_id', false);

            $id = (int) $_GET['id'];
            $context['prop_id'] = $id;

            //itemprop
            $sqlProp = $smcFunc['db_query']('',
                'SELECT p.id, p.schema_prop, p.schema_prop_type, m.schema_id, m.schema_desc, m.schema_status
                FROM {db_prefix}mnschemas_prop p, {db_prefix}mnschemas m
                WHERE p.id = {int:id_prop}
                AND p.schema_id = m.id',
                array(
                    'id_prop' => $id,
                )
            );
            while($row = $smcFunc['db_fetch_assoc']($sqlProp)){
                $context['mnschema_prop'][$row['id']] = array(
                    'itemprop' => $row['schema_prop'],
                    'itemprop_type' => $row['schema_prop_type'],
                    'schema_id' => $row['schema_id'],
                    'schema_desc' => $row['schema_desc'],
                    'status' => $row['schema_status'],
                    'subprop' => array(),
                );
            }
            $smcFunc['db_free_result']($sqlProp);

            //subtype
            $sqlSub = $smcFunc['db_query']('',
                'SELECT s.id, s.schema_prop subprop, s.schema_subprop_label
                FROM {db_prefix}mnschemas_subprop s
                WHERE s.schema_prop_id = {int:id_prop}',
                array(
                    'id_prop' => $id,
                )
            );
            while($row = $smcFunc['db_fetch_assoc']($sqlSub)){
                $context['mnschema_prop'][$id]['subprop'][$row['id']] = array(
                    'name' => $row['subprop'],
                    'label' => $row['schema_subprop_label']
                );
            }
            $smcFunc['db_free_result']($sqlSub);

            //template
            $context['sub_template'] = 'schema_subtype_edit';
        }
    }

    //schemaOnBoard
    public static function viewSchemaOnBoard($id_board){
        global $smcFunc;

        $sql = $smcFunc['db_query']('',
            "SELECT schema_boards
            FROM {db_prefix}mnschemas
            WHERE schema_status = 'ENABLED'
            ORDER BY id ASC",
            array()
        );
        while($row = $smcFunc['db_fetch_assoc']($sql))  {
            $prop_boards = !empty($row['schema_boards']) ? explode(',', $row['schema_boards']) : array();
            if (in_array($id_board, $prop_boards)){ return true ;}
        }
        return false;
    }

    //MessageIndex buttons
    public static function MessageIndexButtons(&$buttons){
        global $board, $scripturl;
        if (!empty($board)){
            if (SchemaMN::viewSchemaOnBoard($board)){
                $buttons['new_topic']['url'] = $scripturl . '?action=mnschema;board=' . $board . '.0';
            }
        }
    }

    //action array
    public static function schemaAction(&$actions){
        $actions['mnschema'] = array('SchemaMN/SubsSchemaMN.php', array('SchemaMN', 'viewSchemasTemplates'));
    }

    //Template to select the types of schemes
    public static function viewSchemasTemplates(){
        global $context, $txt, $modSettings;
        $context['can_post_new'] = allowedTo('post_new') || ($modSettings['postmod_active'] && allowedTo('post_unapproved_topics'));
        if (!$context['can_post_new']){ fatal_lang_error('schmn_msg_no_permission'); }

        //templates
        $context['page_title'] = strip_tags($txt['schmn_select_post_title']);
        $context['schema_templates'] = LoadSchemaMN::getEnabledSchemas();
        //template with credits
        $context['sub_template'] = 'schema_select_post';
    }

    //Extra Fields
    public static function TopicFieldsHooks(){
        global $context, $txt, $topic, $smcFunc;
        if ($context['current_action'] == 'post2'){
            $schema_id = !empty($_GET['sch']) ? (int) $_GET['sch'] : 0;
            if (empty($schema_id)){return;}
            $smcFunc['db_query']('',
                'UPDATE {db_prefix}topics
                SET mnschema_id = {int:id_schema}
                WHERE id_topic = {int:topic_id}',
                array(
					'id_schema' => $schema_id,
                    'topic_id' => $topic,
                )
            );
            //update or insert
            LoadSchemaMN::setFieldsValues($schema_id, $topic);
        }else{
            $schema_id = 0;
            //catch, all values, editing first message
            if (!$context['is_new_topic']){
                LoadSchemaMN::getTopicSchemaOptions($topic);
                $schema_id = $context['id_schema'];
            }else{
                $schema_id = !empty($_GET['sch']) ? (int) $_GET['sch'] : 0;
            }
            if (empty($schema_id)){return;}

            //add id of schema, form action url
            $context['destination'] .= ';sch='.$schema_id;

            //load all fields
            LoadSchemaMN::getFieldsSchemaBoard($schema_id);
            $id_first_message = 0;
            if (!empty($topic)){ $id_first_message = LoadSchemaMN::getTopicFirstMessageId($topic); }
            
            //ok, go it
            if (!empty($context['is_first_post'])
                || (isset($_REQUEST['msg']) && $_REQUEST['msg'] == $id_first_message)
                ) {
                foreach($context['fields_itemprop'] as $id => $values){
                    if (empty($values['subtype'])){
                        $value_prop = !empty($context['schema_props'][$id]['itemprop_value']) ? $context['schema_props'][$id]['itemprop_value'] : '';
                        $context['posting_fields']['itemprop_'.$id] = array(
                            'label' => array(
                                'text' => $values['label'],
                                'class' => '',
                            ),
                            'input' => array(
                                'type' => 'text',
                                'attributes' => array(
                                    'size' => 80,
                                    'maxlength' => 255,
                                    'value' => $value_prop,
                                    'required' => false,
                                ),
                            ),
                        );
                    }
                }
                $idx = 0;
                
                foreach($context['fields_itemprop_sub'] as $id => $values){
                    $value_prop = !empty($context['schema_subprops'][$id]['itemprop_value']) ? $context['schema_subprops'][$id]['itemprop_value'] : '';
                    $context['posting_fields']['itemprop_sub_'.$id] = array(
                        'label' => array(
                            'text' => $values['label'] . ' ('. $values['label_prop'] .')',
                            'class' => '',
                        ),
                        'input' => array(
                            'type' => 'text',
                            'attributes' => array(
                                'size' => 80,
                                'maxlength' => 80,
                                'value' => $value_prop,
                                'required' => false,
                            ),
                        ),
                    );
					$idx++;
                }
            }
        }
    }

    /**
     * Add the necessary data before creating a topic
     *
	 * @param array $msgOptions
	 * @param array $topicOptions
	 * @param array $posterOptions
	 * @param array $topic_columns
	 * @param array $topic_parameters
	 *
	 * @return void
	 */
	public static function BeforeCreateTopic(&$msgOptions, &$topicOptions, &$posterOptions, &$topic_columns, &$topic_parameters)
	{
		global $context;
        $schema_id = !empty($_GET['sch']) ? (int) $_GET['sch'] : 0;
        if (empty($schema_id)){return;}
		$topic_columns['mnschema_id'] = 'int';
        $topic_parameters[] = $schema_id;
    }

    //What we do after creating the topic
    public static function AfterCreateTopic(&$msgOptions, &$topicOptions, &$posterOptions){
        global $context;
        $schema_id = !empty($_GET['sch']) ? (int) $_GET['sch'] : 0;
        if (empty($schema_id)){return;}

        LoadSchemaMN::setFieldsValues($schema_id, $topicOptions['id']);
    }

    //prepare display context hooks
    public static function PrepareDisplayContext(&$output, &$message, $counter){
        global $topic, $context, $txt;
        $id_first_message = LoadSchemaMN::getTopicFirstMessageId($topic);
        $body = $output['body'];
        $schema = '';
        if ($output['id'] == $id_first_message)
        {
            LoadSchemaMN::getTopicSchemaOptions($topic);
            
            if (empty($context['id_schema'])){return;}

            if (empty($context['schema_props']) && empty($context['schema_subprops'])){return;}
            
            $print_div = true;
            
            foreach($context['schema_props'] as $id => $value)
            {
                $prop_values = LoadSchemaMN::getItempropValues($value['itemprop_id']);
                
                //ini -> div
                if ($print_div){
                    $schema .= '
                    <div style="background:lightyellow;border:1px solid lightyellow;padding:10px;margin-top:20px;margin-bottom:20px;box-shadow: 0px 10px 10px gainsboro;" itemscope itemtype="'. $prop_values[$id]['url_schema'] .'">
                        <p style="font-weight:bold;text-decoration:underline;margin-bottom:10px;">'. $txt['schmn_title_topic'] .'</p>';                    
                    $print_div = false;
                }
                
                //itemprops
                $schema .= '
                    <p><span style="font-weight:bold;">'. $prop_values[$value['itemprop_id']]['label'] .':</span> <span itemprop="'. $prop_values[$value['itemprop_id']]['itemprop'] .'">'. (in_array($prop_values[$value['itemprop_id']]['itemprop'], array('image')) ? $value['itemprop_value'] : $value['itemprop_value']) .'</span></p>';
                
            }
            
            $show_subtypes = false;
            
            $itemprop_main = '';

            foreach($context['schema_subprops'] as $id => $value)
            {
                $show_subtypes = true;
                
                $prop_values = LoadSchemaMN::getSubItempropValues($value['itemprop_id']);
                
                //ini -> div
                if ($itemprop_main != $prop_values[$value['itemprop_id']]['itemprop_main']){

                    if (!empty($itemprop_main)){
                        $schema .= '
                        </div>';//end -> last div schema                         
                    }

                    $schema .= '
                    <div itemprop="'. $prop_values[$value['itemprop_id']]['itemprop_main'] .'" itemscope itemtype="'. $prop_values[$value['itemprop_id']]['url_schema'] .'">
                    <p><strong>'. $prop_values[$value['itemprop_id']]['prop_label'].'</strong></p>';
                    $itemprop_main = $prop_values[$value['itemprop_id']]['itemprop_main'];
                }
                //itemprops
                $schema .= '
                    <p><span><u>'. $prop_values[$value['itemprop_id']]['label'] .'</u>:</span> <span itemprop="'. $prop_values[$value['itemprop_id']]['itemprop'] .'">'. $value['itemprop_value'] .'</span></p>';                
            }
            if ($show_subtypes){
                $schema .= '
                </div>';//end -> div schema
            }
            //end principal div
            $schema .= '
            </div>';
        }

        //finally
        $output['body'] .= !empty($schema) ? $schema : '';
    }
}

?>
