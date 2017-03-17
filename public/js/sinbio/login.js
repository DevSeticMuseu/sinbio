$(document).ready(function(){

	var login = $('#loginform');
	var recover = $('#recoverform');
	var speed = 400;

	$('#to-recover').click(function(){

		$("#loginform").slideUp();
		$("#recoverform").fadeIn();
	});
	$('#to-login').click(function(){

		$("#recoverform").hide();
		$("#loginform").fadeIn();
	});



	if($.browser.msie == true && $.browser.version.slice(0,3) < 10) {
		$('input[placeholder]').each(function(){ 

			var input = $(this);       

			$(input).val(input.attr('placeholder'));

			$(input).focus(function(){
				if (input.val() == input.attr('placeholder')) {
					input.val('');
				}
			});

			$(input).blur(function(){
				if (input.val() == '' || input.val() == input.attr('placeholder')) {
					input.val(input.attr('placeholder'));
				}
			});
		});
	}
	
	$("#loginform").submit(function() {
		var usuario = $("#fUsuario").val();
		var senha = $("#fSenha").val();
		
		if (usuario == "") {
			jAlert("Usuário é um campo obrigatório");
			return false;
		}
		else{
			if (senha == "") {
				jAlert("Senha é um campo obrigatório");
				return false;
			}
			else {
				return true;
			}
		}
	});
});