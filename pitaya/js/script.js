$(document).ready(function(){
	
	$('#save_' + product_id).click(function(){
		ajax("save_+product_id");
	});

	$('#add_new_' + product_id).click(function(){
		$('#entry-form_' + product_id).fadeIn("fast");	
	});
	
	$('#close_' + product_id).click(function(){
		$('#entry-form_' +product_id).fadeOut("fast");	
	});
	
	$('#cancel_' + product_id).click(function(){
		$('#entry-form_' +product_id).fadeOut("fast");	
	});
	
	$(".del").live("click",function(){
		ajax("delete",$(this).attr("id"));
	});

	function ajax(action,id){
		if(action == 'save_' + product_id)
			data = $('#buyfruit_' + product_id).serialize()+"&action="+action;
		else if(action == "delete"){
			data = "action="+action+"&item_id="+id;
		}

		$.ajax({
			type: "POST", 
			url: "ajax.php", 
			data : data,
			dataType: "json",
			success: function(response){
				if(response.success == "1"){
					if(action == 'save_' + product_id){
						$('#entry-form_' + product_id).fadeOut("fast",function(){
							$('#table-list_' + product_id).append("<tr><td>"+response.product_id+"</td><td>"+response.product_description+"</td><td>"+response.peso_bruto+"</td><td>"+response.cajillas+"</td><td>"+response.peso_neto+"</td><td><a href='#' id='"+response.row_id+"' class='del'>Delete</a></a></td></tr>");	
							$("#table-list_'"+product_id+"' tr:last").effect("highlight", {
								color: '#4BADF5'
							}, 1000);
						});	
					}else if(action == "delete"){
						var row_id = response.item_id;
						$("a[id='"+row_id+"']").closest("tr").effect("highlight", {
							color: '#4BADF5'
						}, 1000);
						$("a[id='"+row_id+"']").closest("tr").fadeOut();
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
