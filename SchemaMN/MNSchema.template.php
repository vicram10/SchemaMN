<?php
    /**
     * MNSchema.template.php
     *
     * @package MNSchema
     * @link https://www.mercado-negro.net
     * @author thedarkness https://www.mercado-negro.net/profile/thedarkness/
     * @copyright 2020 thedarkness
     *
     * @version 0.1
     */

    //credits
    function template_schema_credits(){
        LoadSchemaMN::setCredits();
    }

    ///Admin template
    function template_schema_admin(){
        global $txt, $context, $scripturl, $boardurl;

        if ($context['infobox']){
            echo '
            <div class="infobox">
                ', $txt['schmn_save_succesfully'] ,'
            </div>';
        }

        echo '
        <form id="admin_form_mnschema" action="', $scripturl ,'?action=admin;area=mnschema;save;sa=main" method="post" accept-charset="', $context['character_set'] ,'">
            <div class="cat_bar">
                <h3 class="catbg">', $txt['schmn_admin_list_title'] ,'</h3>
            </div>
            <table class="table_grid" id="mnschema_list">
                <thead>
                    <tr class="title_bar">
                        <th scope="col" id="header_mnschema_list_id_schema" style="width:20%;">
                            ', $txt['schmn_admin_col_id'] ,'
                        </th>
                        <th scope="col" id="header_mnschema_list_desc">
                            ', $txt['schmn_admin_col_desc'] ,'
                        </th>
                        <th scope="col" id="header_mnschema_list_options">
                            ', $txt['schmn_admin_col_options'] ,'
                        </th>
                    </tr>
                </thead>
                <tbody>';
                    if (count($context['mnschemas_list']) > 0){
                        foreach($context['mnschemas_list'] as $id => $schema){
                            echo '
                            <tr class="windowbg">
                                <td>', $id ,' - ', $schema['type'] ,'</td>
                                <td>
                                    ', $schema['status'] == 'ENABLED' ? '<span class="badge badge-success">'.$txt['schmn_enable'].'</span>' : '<span class="badge badge-error">'.$txt['schmn_disable'].'</span>' ,' ', $schema['desc'] ,'
                                </td>
                                <td class="centercol">
                                    <a href="', $scripturl ,'?action=admin;area=mnschema;sa=edit;id=', $id ,';', $context['session_var'] ,'=', $context['session_id'] ,'">
                                        <img src="', $boardurl ,'/Sources/SchemaMN/img/pencil.png" alt="', $txt['schmn_edit'] ,'" />
                                    </a>
                                    <a href="javascript:(0);" class="status_change" data-id="', $id ,'" data-status="', $schema['status'] ,'" data-var="', $context['session_var'] ,'" data-session="', $context['session_id'] ,'">
                                        <img src="', $boardurl ,'/Sources/SchemaMN/img/', $schema['status'] == 'ENABLED' ? 'exclamation' : 'accept' ,'.png" alt="', $schema['status'] == 'ENABLED' ? $txt['schmn_disable'] : $txt['schmn_enable'] ,'" title="', $schema['status'] == 'ENABLED' ? $txt['schmn_disable'] : $txt['schmn_enable'] ,'" />
                                    </a>
                                </td>
                            </tr>';
                        }
                    }else{
                        echo '
                        <tr>
                           <td colspan="3"><div class="warningbox">', $txt['schmn_not_list'] ,'</div></td> 
                        </tr>';
                    }
                    echo '
                </tbody>
            </table>
        </form>';
    }

    ///Add Schemas template
    function template_schema_add(){
        global $txt, $context, $scripturl, $boardurl, $board_info;

        echo '
        <form id="admin_form_mnschema" action="', $scripturl ,'?action=admin;area=mnschema;save;sa=add" method="post" accept-charset="', $context['character_set'] ,'">
            <div class="cat_bar">
                <h3 class="catbg">', $txt['schmn_admin_add_title'] ,'</h3>
            </div>
            <div class="windowbg">
                <dl class="settings">
                    <dt>
                        <strong>', $txt['schmn_admin_type']  ,'</strong>
                        <br>
                        <span class="smalltext">', $txt['schmn_admin_type_help'] ,'</span>
                    </dt>
                    <dd>
						<input type="text" name="schmn_type" value="" size="60" />
                    </dd>
                    <dt>
                        <strong>', $txt['schmn_type_label']  ,'</strong>
                        <br>
                        <span class="smalltext">', $txt['schmn_type_label_desc'] ,'</span>
                    </dt>
                    <dd>
						<input type="text" name="schmn_label" value="" size="60" />
                    </dd>
                    <dt>
                        <strong>', $txt['schmn_admin_type_desc']  ,'</strong>
                    </dt>
                    <dd>
                        <textarea id="schmn_type_desc" name="schmn_type_desc"></textarea>
                    </dd>';
                    if (count($context['mnschema_boards']) > 0){
                        echo '
                        <dt>
                            <strong>', $txt['schmn_boards']  ,'</strong>
                        </dt>
                        <dd>
                            <select style="height:150px;width:200px;" multiple name="schmn_boards[]">';
                            foreach($context['mnschema_boards'] as $id => $value){
                                echo '
                                <option value="', $id ,'"> ', $value['name'];
                            }
                        echo '</select>
                        </dd>';
                    }
                echo '    
                </dl>
                <hr />
                <input type="hidden" id="schmn_prop_count" name="schmn_prop_count" value="0" />
                <dl id="schmn_prop" class="settings">
                    <dt>
                        <strong>', $txt['schmn_admin_prop']  ,'</strong>
                        <br>
                        <span class="smalltext">', $txt['schmn_admin_prop_help'] ,'</span>
                    </dt>
                    <dd>
                        <input type="text" name="schmn_prop" value="" size="30" />
                    </dd>
                    <dt>
                        <strong>', $txt['schmn_prop_label']  ,'</strong>
                        <br>
                        <span class="smalltext"></span>
                    </dt>
                    <dd>
                        <input type="text" name="schmn_prop_label" value="" size="30" />
                        <a id="schmn_add_prop" href="javascript:(0);" title="', $txt['schmn_admin_prop_add'] ,'">
                            <img src="', $boardurl ,'/Sources/SchemaMN/img/add.png"/>
                        </a>
                    </dd>
                </dl>
                <hr />
                <input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
                <input type="submit" name="add" value="', $txt['schmn_save'] ,'" onclick="return !isEmptyText(this.form.schema_type);" class="button">
            </div>
        </form>';
    }

    //Edit Schema Template
    function template_schema_edit(){
        global $txt, $context, $scripturl, $boardurl;
        $schema = $context['mnschema'][$context['mnschema_id']];
        echo '
        <form id="admin_form_mnschema" action="', $scripturl ,'?action=admin;area=mnschema;save;sa=edit" method="post" accept-charset="', $context['character_set'] ,'">
            <div class="cat_bar">
                <h3 class="catbg">', $txt['schmn_admin_add_title'] ,'</h3>
            </div>
            <div class="windowbg">
                <dl class="settings">
                    <dt>
                        <strong>', $txt['schmn_admin_type']  ,'</strong>
                        <br>
                        <span class="smalltext">', $txt['schmn_admin_type_help'] ,'</span>
                    </dt>
                    <dd>
						<input type="text" name="schmn_type" value="', $schema['type'] ,'" size="60" disabled />
                    </dd>
                    <dt>
                        <strong>', $txt['schmn_type_label']  ,'</strong>
                        <br>
                        <span class="smalltext">', $txt['schmn_type_label_desc'] ,'</span>
                    </dt>
                    <dd>
						<input type="text" name="schmn_prop_label" value="', $schema['label'] ,'" size="60"/>
                    </dd>
                    <dt>
                        <strong>', $txt['schmn_admin_type_desc']  ,'</strong>
                    </dt>
                    <dd>
                        <textarea id="schmn_type_desc" name="schmn_type_desc">', $schema['desc'] ,'</textarea>
					</dd>';
                    if (count($context['mnschema_boards']) > 0){
                        echo '
                        <dt>
                            <strong>', $txt['schmn_boards']  ,'</strong>
                        </dt>
                        <dd>
                            <select style="height:150px;width:200px;" multiple name="schmn_boards[]">';
                            foreach($context['mnschema_boards'] as $id => $value){
                                echo '
                                <option value="', $id ,'"', in_array($id, $schema['boards']) ? 'selected' : '' ,'> ', $value['name'];
                            }
                            
                        echo '</select>
                        </dd>';
                    }
                echo '
                </dl>
                <hr />
                <input type="hidden" id="schmn_prop_count" name="schmn_prop_count" value="0" />';
                if (count($schema['prop']) > 0){
                    $count=1;
                    foreach($schema['prop'] as $id => $value){
                        echo '
                        <dl ', ($count == count($schema['prop']) ? 'id="schmn_prop"' : '') ,' class="settings">
                            <dt>
                                <strong>', $txt['schmn_admin_prop']  ,'</strong>
                                <br>
                                <span class="smalltext">', $txt['schmn_admin_prop_help'] ,'</span>
                            </dt>
                            <dd>
                                <input type="text" name="prop_', $id ,'" value="', $value['name'] ,'" size="30" disabled />';
                                if ($count == count($schema['prop'])){
                                    echo '
                                    <a id="schmn_add_prop" href="javascript:(0);" title="', $txt['schmn_admin_prop_add'] ,'">
                                        <img src="', $boardurl ,'/Sources/SchemaMN/img/add.png"/>
                                    </a>';
                                }
                            echo '
                            </dd>
                            <dt>
                                <strong>', $txt['schmn_prop_label']  ,'</strong>
                                <br>
                                <span class="smalltext">', $txt['schmn_type_label_desc'] ,'</span>
                            </dt>
                            <dd>
                                <input type="text" name="schmn_prop_label" value="', $value['label'] ,'" size="30" disabled/>
                            </dd>
                        </dl>';
                        $count++;
                    }
                }
                echo '
                <hr />
                <input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
                <input type="hidden" name="schema_id" value="', $context['mnschema_id'] ,'"/>
                <input type="submit" name="edit" value="', $txt['schmn_edit'] ,'" onclick="return !isEmptyText(this.form.schema_type);" class="button">
            </div>
        </form>';
    }

    //SubTypes
    function template_schema_subtype(){
        global $context, $txt, $scripturl, $boardurl;

        if ($context['infobox']){
            echo '
            <div class="infobox">
                ', $txt['schmn_save_succesfully'] ,'
            </div>';
        }

        //template
        echo '
        <div class="cat_bar">
                <h3 class="catbg">', $txt['schmn_admin_subtype_title'] ,'</h3>
            </div>
            <table class="table_grid" id="mnschema_list">
                <thead>
                    <tr class="title_bar">
                        <th scope="col" id="header_mnschema_list_id_schema" style="width:20%;">
                            ', $txt['schmn_admin_col_id'] ,'
                        </th>
                        <th scope="col" id="header_mnschema_list_desc">
                            ', $txt['schmn_admin_col_desc'] ,'
                        </th>
                        <th scope="col" id="header_mnschema_list_options">
                            ', $txt['schmn_admin_col_options'] ,'
                        </th>
                    </tr>
                </thead>
                <tbody>';
                if (count($context['mnschemas_prop_list']) > 0){
                    foreach($context['mnschemas_prop_list'] as $id => $schema){
                        echo '
                            <tr class="windowbg">
                                <td>', $id ,' - ', $schema['type'] ,'</td>
                                <td>
                                    ', $schema['status'] == 'ENABLED' ? '<span class="badge badge-success">'.$txt['schmn_enable'].'</span>' : '<span class="badge badge-error">'.$txt['schmn_disable'].'</span>' ,' ', $schema['itemprop'], ' (', $schema['itemprop_label'] ,')' , !empty($schema['prop_itemtype']) ? ' - <span class="badge badge-warning">'. $schema['prop_itemtype'] . '</span>' : '' ,'&nbsp;&nbsp;', count($schema['subprop']) > 0 ? '<img class="schm_view_subtype" data-child="subtypes_list_'.$id.'" style="vertical-align:middle;cursor:pointer;" src="'. $boardurl .'/Sources/SchemaMN/img/view.png" alt="'. $txt['schmn_view'] .'" />' : '';
                                    if (count($schema['subprop']) > 0){
                                        echo '
                                        <div id="subtypes_list_',$id,'" style="display:none;padding:10px;">
                                            <ol>';
                                        foreach($schema['subprop'] as $value){
                                            echo '
                                            <li>', $value['subprop_id'] ,' - ', $value['schema_subprop'] ,' (', $value['schema_subprop_label'] ,')</li>';
                                        }
                                        echo '
                                            </ol>
                                        </div>';
                                    }    
                                echo '
                                </td>
                                <td class="centercol">
                                    <a href="', $scripturl ,'?action=admin;area=mnschema;sa=subtypes;edit;id=', $schema['prop_id'] ,';', $context['session_var'] ,'=', $context['session_id'] ,'">
                                        <img src="', $boardurl ,'/Sources/SchemaMN/img/pencil.png" alt="', $txt['schmn_edit'] ,'" />
                                    </a>
                                </td>
                            </tr>';
                    }
                }else{
                    echo '
                    <tr>
                        <td colspan="3"><div class="warningbox">', $txt['schmn_not_list'] ,'</div></td> 
                    </tr>';
                }
                echo '
                </tbody>
            </table>
        </div>';
    }

    //Edit Subtypes
    function template_schema_subtype_edit(){
        global $txt, $context, $scripturl, $boardurl;
        $prop_id = $context['prop_id'];
        $schema = $context['mnschema_prop'][$prop_id];
        echo '
        <form id="admin_form_mnschema" action="', $scripturl ,'?action=admin;area=mnschema;sa=subtypes;edit;save" method="post" accept-charset="', $context['character_set'] ,'">
            <div class="cat_bar">
                <h3 class="catbg">', $txt['schmn_admin_subtype_edit'] ,'</h3>
            </div>
            <div class="windowbg">
                <dl class="settings">
                    <dt>
                        <strong>', $txt['schmn_admin_prop']  ,'</strong>
                        <br>
                        <span class="smalltext">', $txt['schmn_admin_prop_help'] ,'</span>
                    </dt>
                    <dd>
						<input type="text" value="', $schema['itemprop'] ,'" size="60" disabled />
                    </dd>
                    <dt>
                        <strong>', $txt['schmn_admin_type']  ,'</strong>
                    </dt>
                    <dd>
                        <textarea disabled>', $schema['schema_id'] ,': ', $schema['schema_desc'] ,'
                        </textarea>
                    </dd>
                    <dt>
                        <strong>', $txt['schmn_admin_type']  ,'</strong>
                        <br>
                        <span class="smalltext">', $txt['schmn_admin_type_help'] ,'</span>
                    </dt>
                    <dd>
						<input type="text" id="schm_type" name="schmn_type" value="', !empty($schema['itemprop_type']) ? $schema['itemprop_type'] : '' ,'" size="60" />
                    </dd>
                </dl>
                <hr />
                <input type="hidden" id="schmn_prop_count" name="schmn_prop_count" value="0" />
                <a class="badge badge-warning" id="schmn_add_prop" href="javascript:(0);" title="', $txt['schmn_admin_prop_add'] ,'">
                    <img style="vertical-align:middle;" src="', $boardurl ,'/Sources/SchemaMN/img/add.png"/> ', $txt['schmn_add'] ,'
                </a>';
                if (count($schema['subprop']) > 0){
                    $count=1;
                    foreach($schema['subprop'] as $id => $value){
                        echo '
                        <dl ', ($count == count($schema['subprop']) ? 'id="schmn_prop"' : '') ,' class="settings">
                            <dt>
                                <strong>', $txt['schmn_admin_prop']  ,'</strong>
                                <br>
                                <span class="smalltext">', $txt['schmn_admin_prop_help'] ,'</span>
                            </dt>
                            <dd>
                                <input type="text" name="prop_', $id ,'" value="', $value['name'] ,'" size="30" disabled />
                            </dd>
                            <dt>
                                <strong>', $txt['schmn_prop_label']  ,'</strong>
                                <br>
                                <span class="smalltext">', $txt['schmn_type_label_desc'] ,'</span>
                            </dt>
                            <dd>
                                <input type="text" name="schmn_prop_label_', $id ,'" value="', $value['label'] ,'" size="30" disabled />
                            </dd>
                        </dl>';
                        $count++;
                    }
                }else{
                    echo '
                    <dl id="schmn_prop" class="settings"></dl>';
                }
            echo '
            </div>
            <hr />
            <input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
            <input type="hidden" name="prop_id" value="', $prop_id ,'"/>
            <input type="submit" name="edit" value="', $txt['schmn_edit'] ,'" onclick="return !isEmptyText(this.form.schema_type);" class="button">
        </form>';
    }

    //above template select schema
    function template_schema_select_post(){
        global $context, $txt, $scripturl, $boardurl, $board;
        
        //ini -> schema choose
        echo '
        <div style="width:70%;margin:auto;margin-top:20px;">
            <div class="cat_bar">
                <h3 class="catbg">', $txt['schmn_select_post_title'] ,'</h3>
            </div>';
        if (count($context['schema_templates']) > 0){
            echo '
            <table class="table_grid" id="mnschema_list">
                <thead>
                    <tr class="title_bar">
                        <th scope="col" id="header_mnschema_list_desc">
                            ', $txt['schmn_admin_col_desc'] ,'
                        </th>
                        <th scope="col" id="header_mnschema_list_options">
                            ', $txt['schmn_admin_col_options'] ,'
                        </th>
                    </tr>
                </thead>
                <tbody>';
                foreach($context['schema_templates'] as $id => $value){
                    if (in_array($board, $value['boards'])){
                        echo '
                        <tr class="windowbg">
                            <td width="70%">
                                <strong>', $value['label'] ,':</strong> ', $value['desc'] ,'
                                <p>(', $value['schema'] ,')</p>
                            </td>
                            <td style="text-align:center;">
                                <a class="badge badge-warning" href="', $scripturl ,'?action=post;board=',$board,'.0;sch=', $id ,'">
                                    ', $txt['schmn_new_topic'] ,'
                                </a>
                            </td>
                        </tr>';
                    }
                }
            echo '</tbody>
            </table>';
        }else{
            echo '
            <div class="infobox">
                ', $txt['schmn_not_list'] ,'
            </div>';
        }
        echo '
        </div>';
        //end -> schema choose

        //credits
        template_schema_credits();
    }
?>