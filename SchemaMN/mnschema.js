$(document).ready(function() {
    $('#schmn_add_prop').click(function(event){
        var prop_count = parseInt($('#schmn_prop_count').val()) + 1;
        var item_formulario = "<dt style='margin-top:10px;'>";
        item_formulario += "<strong>"+ prop_count +" - "+ mn_schema_prop +"</strong>";
        item_formulario += '<br><span class="smalltext">'+ mn_schema_prop_help +'</span>';
        item_formulario += '</dt>';
        item_formulario += '<dd><input type="text" name="schmn_prop_'+ prop_count +'" value="" size="30" /></dd>';
        item_formulario += "<dt>";
        item_formulario += "<strong>"+ prop_count +" - "+ mn_schema_prop_label +"</strong>";
        item_formulario += '<br><span class="smalltext">'+ mn_schema_prop_label_help +'</span>';
        item_formulario += '</dt>';
        item_formulario += '<dd><input type="text" name="schmn_prop_label_'+ prop_count +'" value="" size="30" /></dd>';
        $('#schmn_prop').append(item_formulario);
        $('#schmn_prop_count').val(prop_count);
    });

    $(".status_change").click(function(){
        var id_schema = $(this).data('id');
        var status = $(this).data('status');
        var session_var = $(this).data('var');
        var session_id = $(this).data('session');
        
        console.log('Id.Schema -> '+id_schema+', Status -> '+status + ', ' + session_var + '='+session_id);
        $.ajax({
            url: smf_scripturl + "?action=admin;area=mnschema;sa=status;id="+id_schema+";status="+status+";"+session_var+"="+session_id,
            success: function(data){
                console.log(data);
                var icon_n = data["cod"] == 0 ? (status == 'ENABLED' ? 'warning' : 'success') : 'error';
                Notify(icon_n, data["title"], data["msg"], true);
            },
        });
    });

    $('#schm_view_subtype').click(function(){
        $('#subtypes_list').toggle();('slow');
    });
});

//Sweet Alert
function Notify(v_icon, v_title, v_message, v_reload){
    Swal.fire({
        icon: v_icon,
        title: v_title,
        html: v_message,
        onClose: () => {
            if (v_reload){
                location.reload();
            }
        }
    });
}

//Change Status Schema
function SchemaStatusAdmin(id_schema, status, session_var, session_id){
    
}