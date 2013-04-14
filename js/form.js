/**
 * Init Form with AJAX
 * @returns data in selected div as response
 */

$(function(){	
	$.ajax({
		type:'POST',
		url: 'form.ajax.php',
		success:function(data){
			$('#formWindow').html(data);
		}
	});
});
