<?php
header('Content-Type: application/x-javascript');
$products = array(123,456);
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
foreach ($products as $product_id) {
$str .= <<<EOT
        $("#save_$product_id").click(function(){
                ajax("save_$product_id");
        });

        $("#add_new_$product_id").click(function(){
                $("#entry-form_$product_id").fadeIn("fast");
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
}
echo $str . $str2 . $str3 . $str4 . $str5 . $str6;
?>
