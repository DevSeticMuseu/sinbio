$().ready(function() {
	$(".grid").flexigrid({
		url : '/programa/gera-xml',
		dataType : 'xml',
		colModel : [ {
			display : 'Id',
			name : 'id',
			width : 40,
			sortable : true,
			align : 'center'
		}, {
			display : 'Nome',
			name : 'nm_display',
			width : 200,
			sortable : true,
			align : 'left'
		}, {
			display : 'Controller',
			name : 'nm_programa',
			width : 200,
			sortable : true,
			align : 'left'
		}, {
			display : 'Módulo',
			name : 'nm_modulo',
			width : 200,
			sortable : true,
			align : 'left'
		}],
		buttons : [ {
			name : 'Novo',
			bclass : 'add',
			onpress : novoPrograma
		}, {
			name : 'Alterar',
			bclass : 'edit',
			onpress : alterarPrograma
		}, {
			name : 'Excluir',
			bclass : 'delete',
			onpress : excluirPrograma
		}, {
			separator : true
		} ],
		searchitems : [ {
			display : 'Nome',
			name : 'nm_programa',
			isdefault : true
		} ],
		sortname : "id",
		sortorder : "asc",
		usepager : true,
		useRp : true,
		rp : 15,
		showTableToggleBtn : true,
		width : '100%',
		height : 460
	});
});


function novoPrograma() {
	$.post('/programa/verifica-permissao', {sOP: "Cadastrar"} ,function(data) {
		if (data) {
			$(window.document.location).attr('href',"/programa/cadastrar");
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}

function alterarPrograma() {
	$.post('/programa/verifica-permissao', { sOP: "Alterar" }, function(data) {
		if (data) {
			if ($('.trSelected').length > 1) {
				jAlert("Você deve selecionar apenas um item para alterar.","Atenção");
			}
			else if ($('.trSelected').length == 0) {
				jAlert("Você deve selecionar um item para alterar.","Atenção");
			}
			else {
				var nId = $('.trSelected td:first-child div').text();
				$(window.document.location).attr('href',"/programa/alterar/nId/"+nId);
			}
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}

function excluirPrograma() {
	if ($('.trSelected').length == 0) {
		jAlert("Você deve selecionar ao menos um ítem.","Atenção");
	}
	else {
		if (confirm('Você tem certeza que deseja excluir ' + $('.trSelected').length + ' Programa(s)?')) {
			$.post('/programa/verifica-permissao', { sDescricao: "Programa", sOP: "Alterar" } ,function(data) {
				if (data) {
					var selArry = [];
					var nI = 1;
					$("div.flexigrid .trSelected").each(function(trI,tr){
						selArry.push($(tr).attr("id").substr(3));
					});
					$.post('/programa/excluir', { fId: selArry, sOP: "Excluir" } ,function(data) {
						location.reload();
					});
				}
				else {
					jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
				}
			});
		}
	}
}
