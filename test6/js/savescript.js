$(document).ready(function(){
	
	$("#save").click(function(){
		ajax("save");
	});

	$("#save_456").click(function(){
		ajax("save_456");
	});

	$("#add_new").click(function(){
		$("#entry-form_123").fadeIn("fast");	
	});

	$("#add_new_456").click(function(){
		$("#entry-form_456").fadeIn("fast");	
	});
	
	$("#close").click(function(){
		$("#entry-form_123").fadeOut("fast");	
	});
	
	$("#close_456").click(function(){
		$("#entry-form_456").fadeOut("fast");	
	});
	
	$("#cancel").click(function(){
		$("#entry-form_123").fadeOut("fast");	
	});
	
	$("#cancel_456").click(function(){
		$("#entry-form_456").fadeOut("fast");	
	});
	
	$(".del").live("click",function(){
		ajax("delete",$(this).attr("id"));
	});

	function ajax(action,id){
		if(action =="save")
			data = $("#buyfruit_123").serialize()+"&action="+action;
		else if(action == "delete"){
			data = "action="+action+"&item_id="+id;
		}else if(action =="save_456"){
                        data = $("#buyfruit_456").serialize()+"&action="+action;
		}

		$.ajax({
			type: "POST", 
			url: "ajax.php", 
			data : data,
			dataType: "json",
			success: function(response){
				if(response.success == "1"){
					if(action == "save"){
						$("#entry-form_123").fadeOut("fast",function(){
							$("#table-list_123").append("<tr><td>"+response.product_id+"</td><td>"+response.product_description+"</td><td>"+response.peso_bruto+"</td><td>"+response.cajillas+"</td><td>"+response.peso_neto+"</td><td><a href='#' id='"+response.row_id+"' class='del'>Delete</a></a></td></tr>");
							$("#table-list_123 tr:last").effect("highlight", {
								color: '#4BADF5'
							}, 1000);
						});	
					}else if(action == "delete"){
						var row_id = response.item_id;
						$("a[id='"+row_id+"']").closest("tr").effect("highlight", {
							color: '#4BADF5'
						}, 1000);
						$("a[id='"+row_id+"']").closest("tr").fadeOut();
					}else if(action == "save_456"){
                                                $("#entry-form_456").fadeOut("fast",function(){
                                                        $("#table-list_456").append("<tr><td>"+response.product_id+"</td><td>"+response.product_description+"</td><td>"+response.peso_bruto+"</td><td>"+response.cajillas+"</td><td>"+response.peso_neto+"</td><td><a href='#' id='"+response.row_id+"' class='del'>Delete</a></a></td></tr>");
                                                        $("#table-list_456 tr:last").effect("highlight", {
                                                                color: '#4BADF5'
                                                        }, 1000);
                                                });
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
