$(document).ready(function() {
	$('ul.items>li.active>ul').slideDown();
	$(function() {
		$('.items').click(clickFn);
	});

	function clickFn(e) {
		var $el = $(e.target);
		if (!$el.parent().children('ul').is(':visible')) {
			if ($el.parent().parent().is('ul.items')) {
				var $visibles=$('ul.items>li>ul:visible');
				if ($visibles.length>0){
					$visibles.slideUp('medium', function(){
						$el.parent().children("ul").slideDown('slow');
					}
					);
				}
				else{
					$el.parent().children("ul").slideDown('slow');
				}
			}
		}
	}	

	function getEventTarget(e) {
		e = e || window.event;
		return e.target || e.srcElement;
	}
	
	$(".close").click(function(){
		$("#msg").hide("100");
	});
});

function ajaxCarregaEndereco() {
	if($('input[name=fCep]').val() != '') {
		sCep = $('input[name=fCep]').val();
		sCep.replace('-','');
		parametros = "nCep="+sCep;
		$("#loading").show();
		
		$.ajax({
			type: 'POST',
			url: '/site/processa-cep',
			data: parametros,
			success: function(xml){
				if ($('resultado', xml).text() == '0') {
					jAlert('Não foi possivel carregar o endereço, por favor digite manualmente.', 'ERRO - Cliente');
				}
				else {
					$("#loading").hide();
		            $('input[name=fEndereco]').val($('tipo_logradouro', xml).text()+" "+$('logradouro', xml).text());
		            $('input[name=fNmBairro]').val($('bairro', xml).text());
		            $('input[name=fCidade]').val($('cidade', xml).text());
		            $('input[name=fUf]').val($('uf', xml).text());
				}
			}
		});	
	}
}