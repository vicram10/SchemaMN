<?php
/**
 * LoadSchemaMN.php
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

//Load Class
class LoadSchemaMN
{
    /**
     * https://www.mercado-negro.net
     */
    public static function setCredits(){
        echo '
        <div style="text-align:center;font-size:11px;color:grey !important;">
            <span>Copyright &copy; 2020 - SchemaMN 0.1 Beta 1</span> - <a style="text-decoration:none;" href="https://www.mercado-negro.net" target="_blank">Mercado Negro</a>
        </div>';
    }

    /**
     * @return $schemasmn = array();
     */
    public static function getEnabledSchemas(){
        global $context, $txt, $scripturl,  $smcFunc;

        $sql = $smcFunc['db_query']('',
            "SELECT m.id, m.schema_id, m.schema_desc, m.schema_label, m.schema_boards
            FROM {db_prefix}mnschemas m
            WHERE m.schema_status = 'ENABLED'",
            array()
        );
        $schemas = array();
        while($row = $smcFunc['db_fetch_assoc']($sql)){
            $schemas[$row['id']] = array(
                'schema' => $row['schema_id'],
                'desc' => $row['schema_desc'],
                'label' => $row['schema_label'],
                'boards' => !empty($row['schema_boards']) ? explode(',', $row['schema_boards']) : array(),
            );
        }
        $smcFunc['db_free_result']($sql);
        return $schemas;
    }

    //item prop
    public static function getItempropValues($prop_id){
        global $smcFunc;
        $sql = $smcFunc['db_query']('',
            'SELECT m.schema_id as schema_main, p.*
            FROM {db_prefix}mnschemas m, {db_prefix}mnschemas_prop p
            WHERE m.id = p.schema_id
            AND p.id = {int:prop_id}',
            array(
                'prop_id' => $prop_id
            )
        );
        $prop_values = array();
        while($row = $smcFunc['db_fetch_assoc']($sql)){
            $prop_values[$prop_id] = array(
                'itemprop' => $row['schema_prop'],
                'schema_main' => $row['schema_main'],
                'label' => $row['schema_prop_label'],
                'url_schema' => 'https://schema.org/'.$row['schema_main'],
            );
        }
        $smcFunc['db_free_result']($sql);
        return $prop_values;
    }

    //itemprop subtypes
    public static function getSubItempropValues($prop_id){
        global $smcFunc;

        $sql = $smcFunc['db_query']('',
            'SELECT p.schema_prop AS itemprop_main, p.schema_prop_type AS sub_type,
                    pp.schema_prop, schema_subprop_label AS label_subprop,
                    p.schema_prop_label AS prop_label
            FROM {db_prefix}mnschemas_prop p, {db_prefix}mnschemas_subprop pp
            WHERE p.id = pp.schema_prop_id
            AND pp.id = {int:subprop_id}',
            array(
                'subprop_id' => $prop_id,
            )
        );
        $prop_values = array();
        while($row = $smcFunc['db_fetch_assoc']($sql)){
            $prop_values[$prop_id] = array(
                'itemprop' => $row['schema_prop'],
                'itemprop_main' => $row['itemprop_main'],
                'subtype' => $row['sub_type'],
                'prop_label' => $row['prop_label'],
                'label' => $row['label_subprop'],
                'url_schema' => 'https://schema.org/'.$row['sub_type'],
            );
        }
        $smcFunc['db_free_result']($sql);
        return $prop_values;
    }

    //list of fields
    public static function getFieldsSchemaBoard($schema_id){
        global $board, $context, $txt, $smcFunc;
        $context['fields_itemprop'] = array();
        $context['fields_itemprop_sub'] = array();
        $sql = $smcFunc['db_query']('',
            'SELECT p.id, p.schema_prop itemprop, p.schema_prop_label label, p.schema_prop_type subtype
            FROM {db_prefix}mnschemas_prop p, {db_prefix}mnschemas m
            WHERE p.schema_id = m.id
            AND m.id = {int:id_schema}',
            array(
                'id_schema' => (int) $schema_id,
            )
        );
        while($row = $smcFunc['db_fetch_assoc']($sql)){
            $context['fields_itemprop'][$row['id']] = array(
                'itemprop' => empty($row['subtype']) ? $row['itemprop'] : '',
                'label' => $row['label'],
                'subtype' => !empty($row['subtype']) ? $row['subtype'] : '',
            );
        }
        $smcFunc['db_free_result']($sql);

        //itemprop with subtype
        $sql = $smcFunc['db_query']('',
            'SELECT pp.id, pp.schema_prop_id itemprop_id, p.schema_prop_label label_prop, pp.schema_subprop_label label, p.schema_prop itemprop
            FROM {db_prefix}mnschemas m, {db_prefix}mnschemas_prop p, {db_prefix}mnschemas_subprop pp
            WHERE m.id = {int:id_schema}
            AND m.id = p.schema_id
            AND p.id = pp.schema_prop_id',
            array(
                'id_schema' => (int) $schema_id,
            )
        );
        while($row = $smcFunc['db_fetch_assoc']($sql)){
            $context['fields_itemprop_sub'][$row['id']] = array(
                'itemprop' => $row['itemprop'],
                'label' => $row['label'],
                'label_prop' => $row['label_prop'],
                'itemprop_id' => $row['itemprop_id'], //father
            );
        }
        $smcFunc['db_free_result']($sql);
    }

    //topic schema value
    public static function getTopicSchemaOptions($id_topic){
        global $smcFunc;
        $schema_value = '';
        $sql = $smcFunc['db_query']('',
            'SELECT t.mnschema_id, t.mnschema_values
            FROM {db_prefix}topics t
            WHERE t.id_topic = {int:topic_id}',
            array(
                'topic_id' => $id_topic,
            )
        );
        list ($schema_id, $schema_value) = $smcFunc['db_fetch_row']($sql);
        $smcFunc['db_free_result']($sql);
        return $schema_id.'|'.$schema_value;
    }

    /**
     * Get the id of the topic first message
     *
	 * @param int $topic
	 * @return int
	 */
	public static function getTopicFirstMessageId($topic){
		global $smcFunc;

		$request = $smcFunc['db_query']('', '
			SELECT id_first_msg
			FROM {db_prefix}topics
			WHERE id_topic = {int:current_topic}
			LIMIT 1',
			array(
				'current_topic' => $topic
			)
		);

		list ($first_message_id) = $smcFunc['db_fetch_row']($request);
		$smcFunc['db_free_result']($request);

		return (int) $first_message_id;
    }

    //generic function
    public static function getFieldsValuesJson($schema_id){
        global $context;
        $schema_values = array();
        //load all fields
        //{"7":{"value":"body"},"8":{"value":"Ejemplo de acerca de"}}
        LoadSchemaMN::getFieldsSchemaBoard($schema_id);
        if (count($context['fields_itemprop']) > 0){
            foreach($context['fields_itemprop'] as $id => $values){
                $itemprop = !empty($_POST['itemprop_'.$id]) ? $_POST['itemprop_'.$id] : '';
                if (!empty($itemprop)){
                    $schema_values[$id] = array(
                        'value' => $itemprop,
                    );
                }
            }
        }
        if (count($context['fields_itemprop_sub']) > 0){
            foreach($context['fields_itemprop_sub'] as $id => $values){
                $itemprop = !empty($_POST['itemprop_sub_'.$id]) ? $_POST['itemprop_sub_'.$id] : '';
                if (!empty($itemprop)){
                    $schema_values[$values['itemprop_id']]['subtypes'][] = array(
                        'id' => $id,
                        'sub_value' => $itemprop,
                    );
                }
            }
        }
        return json_encode($schema_values);
    }
}


?>
