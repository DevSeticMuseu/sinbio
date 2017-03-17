$().ready(function() {
	$(".grid").flexigrid({
		url : '/variaveis/gera-xml',
		dataType : 'xml',
		colModel : [ {
			display : 'Id',
			name : 'id',
			width : 40,
			sortable : true,
			align : 'center'
		},
                {
			display : 'Nome Unidade de Medida',
			name : 'nm_unidade',
			width : 300,
			sortable : true,
			align : 'left'
		},{
			display : 'Nome da Variavel',
			name : 'nm_variavel',
			width : 200,
			sortable : true,
			align : 'left'
		}, {
			display : 'Descrição',
			name : 'descricao',
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
			display : 'Nome Variavel',
			name : 'nm_variavel',
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
	$.post('/variaveis/verifica-permissao', {sOP: "Cadastrar"} ,function(data) {
		if (data) {
			$(window.document.location).attr('href',"/variaveis/cadastrar");
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}

function alterarPrograma() {
	$.post('/variaveis/verifica-permissao', { sOP: "Alterar" }, function(data) {
		if (data) {
			if ($('.trSelected').length > 1) {
				jAlert("Você deve selecionar apenas um item para alterar.","Atenção");
			}
			else if ($('.trSelected').length == 0) {
				jAlert("Você deve selecionar um item para alterar.","Atenção");
			}
			else {
				var nId = $('.trSelected td:first-child div').text();
				$(window.document.location).attr('href',"/variaveis/alterar/nId/"+nId);
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
		if (confirm('Você tem certeza que deseja excluir ' + $('.trSelected').length + ' Expedição(ões)?')) {
		$.post('/variaveis/verifica-permissao', { sOP: "Excluir" } ,function(data) {
				if (data) {
					var selArry = [];
					var nI = 1;
					$("div.flexigrid .trSelected").each(function(trI,tr){
						selArry.push($(tr).attr("id").substr(3));
					});
					$.post('/variaveis/excluir', { fId: selArry, sOP: "Excluir" } ,function(data) {
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
