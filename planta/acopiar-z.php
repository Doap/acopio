<?php
//$conn = mysql_connect("localhost","root","");
//mysql_select_db("phppot_examples",$conn);
require_once("../wp-config.php");
require_once("../wp-load.php");
$wp->init(); $wp->parse_request(); $wp->query_posts();
$wp->register_globals(); $wp->send_headers();
get_header();
if ( !is_user_logged_in() ) {
    echo 'Tiene que ingresar su usuario y contraseña para continuar...';
        get_footer();
} else {
$current_user = wp_get_current_user();
$username = $current_user->user_login;
$user_display_name = $current_user->display_name;
echo '<br><b>Persona recibiendo la fruta: </b>' . $user_display_name . '<br><b># Usuario: </b>' . $username;
if(isset($_POST["submit"]) && $_POST["submit"]!="") {
$productCount = count($_POST["products"]);
$manifiesto_id = $_POST["manifiesto_id"];
if (!isset($acopio_insert)){ 
$acopio_insert = $wpdb->insert(
        'wp_reception',
        array(
                'received_by' => $username,
                'manifiesto_id' => $manifiesto_id,
                'status' => 0
        ),
        array(
                '%s',
                '%s',
                '%d'
        )
);
}
if ($acopio_insert) {
$acopio_id = $wpdb->insert_id;
}

for($i=0;$i<$productCount;$i++) {
//mysql_query("UPDATE users set userName='" . $_POST["userName"][$i] . "', password='" . $_POST["password"][$i] . "', firstName='" . $_POST["firstName"][$i] . "', lastName='" . $_POST["lastName"][$i] . "' WHERE userId='" . $_POST["userId"][$i] . "'");
echo '<br>Submitted!';
}
//header("Location:list_user.php");
}
echo '<title>Acopiar Fruta</title>';
echo '<script src="/planta/js/jquery.js"></script>';  
//echo '<script src="javascript.php" type="text/javascript"></script>';
echo '<script src="/planta/js/jquery-ui-1.8.17.custom.min.js"></script>';
echo '<script src="/planta/products.js"></script>';
echo ' <link rel="stylesheet" href="/planta/css/style.css">';
 echo '<link rel="stylesheet" type="text/css" href="/planta/styles.css" />';
// echo var_dump($_POST["products"]); 
//Next line commented 4-27-2014
//$product_id=8;
// echo '<br>' . $product_id;
$rowCount = count($_POST["products"]);
echo '<div class="add-all-button"><form name="add_all" method="post" action="">';
echo '<input type="hidden" name="acopio_id" value="' . $acopio_id . '">';
echo '<input type="button" name="add_to_cart" value="Add All to Cart" onClick="addAllToCartAction();">';
echo '</form></div>'; 
for($i=0;$i<$rowCount;$i++) {
?>
   <div style="margin-top: 5%;">
          <input type="button" value="Add Record" id="add_new_<?php echo $_POST["products"][$i]; ?>"><?php echo $_POST["product_descriptions"][$i]; ?>
          <table width="70%" border="0" cellpadding="0" cellspacing="0" class="table-list" id="table-list_<?php echo $_POST["products"][$i]; ?>">
                <tr>
                        <th width="20%">Product ID</th>
                        <th width="20%">Product Description</th>
                        <th width="40%">Peso Bruto</th>
                        <th width="20%">Cajillas</th>
                        <th width="20%">Peso Neto</th>
                        <th width="20%">Remove</th>
                </tr>
          </table>
        </div>
        <div class="entry-form" name="entry-form_<?php echo $_POST["products"][$i]; ?>" id="entry-form_<?php echo $_POST["products"][$i]; ?>">
                <form name="buyfruit" id="buyfruit_<?php echo $_POST["products"][$i]; ?>">
                <table width="100%" border="0" cellpadding="4" cellspacing="0">
                        <tr>
                                <td colspan="2" align="right"><a href="#" id="close_<?php echo $_POST["products"][$i]; ?>">Close</a></td>
                        </tr>
                        <tr>
                                <td>Product ID</td>
                                <td><input type="hidden" name="product_id" value="<?php echo $_POST["products"][$i]; ?>"><?php echo $_POST["products"][$i]; ?>
                                <input type="hidden" name="acopio_id" value="<?php echo $acopio_id; ?>"></td>
                        </tr>
                        <tr>
                                <td>Description</td>
                                <td><input type="hidden" name="product_description" value="<?php echo $_POST["product_descriptions"][$i]; ?>"><?php echo $_POST["product_descriptions"][$i]; ?></td>
                        </tr>
                        <tr>
                                <td>Peso Bruto</td>
                                <td><input type="text" name="peso_bruto" id="peso_bruto_<?php echo $_POST["products"][$i]; ?>" value="" autofocus></td>
                        </tr>
                        <tr>
                                <td>Cajillas</td>
                                <td><input type="number" name="cajillas" value="3"></td>
                        </tr>
                        <tr>
                                <td>Peso Neto</td>
                                <td><input type="hidden" name="peso_neto" value="0"></td>
                        </tr>
                        <tr>
                                <td align="right"></td>
                                <td><input type="button" value="Save" id="save_<?php echo $_POST["products"][$i]; ?>"><input type="button" value="Cancel" id="cancel_<?php echo $_POST["products"][$i]; ?>"></td>
                        </tr>
                </table>
                </form>
        </div>
<?php
}
//get_footer();
}

//$products = $_POST["products"];
$str = <<<EOD
$(document).ready(function(){
EOD;
$str2 = <<<EOU

        function ajax(action,id){
                if(action == "delete")
                        data = "action="+action+"&item_id="+id;

EOU;
$str4 = <<<EOW
                $.ajax({
                        type: "POST",
                        url: "ajax.php",
                        data : data,
                        dataType: "json",
                        success: function(response){
                                if(response.success == "1"){

                                        if(action == "delete"){
                                                var row_id = response.item_id;
                                                $("a[id='"+row_id+"']").closest("tr").effect("highlight", {
                                                        color: '#4BADF5'
                                                }, 1000);
                                                $("a[id='"+row_id+"']").closest("tr").fadeOut();

EOW;
$str6 = <<<EOY
					}
                                }else{
                                        alert(response.msg);
                                }
                        },
                        error: function(res){
                                alert("Unexpected error! Try again.");
                        }
                });
        }
});
EOY;
foreach ($_POST['products'] as $product_id) {
//if ($key == 'products') {
//$product_id = $value;
$str .= <<<EOT
	$("#entry-form_$product_id").keyup(function(event){
	    if(event.keyCode == 13){
	        $("#save_$product_id").click();
	    }
	});

        $("#save_$product_id").click(function(){
                ajax("save_$product_id");
        });

        $("#add_new_$product_id").click(function(){
                $("#entry-form_$product_id").fadeIn("fast");
		var text_input = document.getElementById("peso_bruto");
		$("#peso_bruto_$product_id").focus ();
		$("#peso_bruto_$product_id").select ();
        });

        $("#close_$product_id").click(function(){
                $("#entry-form_$product_id").fadeOut("fast");
        });

        $("#cancel_$product_id").click(function(){
                $("#entry-form_$product_id").fadeOut("fast");
        });

        $(".del").live("click",function(){
                ajax("delete",$(this).attr("id"));
        });

EOT;
$str3 .= <<<EOV
                else if(action == "save_$product_id"){
                        data = $("#buyfruit_$product_id").serialize()+"&action="+action;
		}
EOV;
$str5 .= <<<EOX
                                        }else if(action == "save_$product_id"){
                                                $("#entry-form_$product_id").fadeOut("fast",function(){
                                                        $("#table-list_$product_id").append("<tr><td>"+response.product_id+"</td><td>"+response.product_description+"</td><td>"+response.peso_bruto+"</td><td>"+response.cajillas+"</td><td>"+response.peso_neto+"</td><td><a href='#' id='"+response.row_id+"' class='del'>Delete</a></a></td></tr>");
                                                        $("#table-list_$product_id tr:last").effect("highlight", {
                                                                color: '#4BADF5'
                                                        }, 1000);
                                                });
                                        

EOX;
//}
}
echo '<script>';
echo $str . $str2 . $str3 . $str4 . $str5 . $str6;
//echo '<!---' .  var_dump($_POST) . '---!>';
echo '</script>';
?>
