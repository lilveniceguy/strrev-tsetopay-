jQuery(document).ready(function($){
	$('#UserLoginForm').submit(function(e){
		e.preventDefault();
		var url = $(this).attr('action');
		$.ajax({
		    type: "POST",
		    url: url,
		    dataType: 'json',
		    data: $(this).serialize(),
		    success: function(data){
		      console.log(data);
		        if (data.respuesta==0) {
		        	$(".resultado").text(data.msj).addClass('box-error');
		        }else{
		        	$(".resultado").text(data.msj).removeClass('box-error').addClass('box-success');
		        	setTimeout(function(){
		        		page = data.page;
		        		page = page.split('-');
		        		window.location.href = '/pages/'+page[1];
		        	}, 2000);
		        };
		    },
		    error: function(XMLHttpRequest, textStatus, errorThrown) {
		       console.log(XMLHttpRequest);
		    }
		});
	});
});