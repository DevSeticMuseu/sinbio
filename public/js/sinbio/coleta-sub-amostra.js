$().ready(function() {
	$(".grid").flexigrid({
		url : '/sub-amostra/gera-xml',
		dataType : 'xml',
		colModel : [ {
			display : 'Id',
			name : 'id',
			width : 40,
			sortable : true,
			align : 'center'
		},
                {
			display : 'Taxon',
			name : 'taxon',
			width : 180,
			sortable : true,
			align : 'left'
		},
                {
			display : 'Id Amostra',
			name : 'coleta_amostra_id',
			width : 160,
			sortable : true,
			align : 'left'
		}, 
                {
			display : 'Quantidade',
			name : 'quantidade',
			width : 120,
			sortable : true,
			align : 'left'
		},{
			display : 'Status',
			name : 'status_2',
			width : 80,
			sortable : true,
			align : 'left'
		}],
		buttons : [ {
			name : 'Novo',
			bclass : 'add',
			onpress : novoSubAmostra
		}, {
			name : 'Alterar',
			bclass : 'edit',
			onpress : alterarSubAmostra
		}, {
			name : 'Excluir',
			bclass : 'delete',
			onpress : excluirSubAmostra
		}, {
			separator : true
		}],
		searchitems : [ {
			display : 'Taxon',
			name : 'taxon',
			isdefault : true
		} ],
		sortname : "id",
		sortorder : "desc",
		usepager : true,
		useRp : true,
		rp : 15,
		showTableToggleBtn : true,
		width : '100%',
		height : 460
	});
});


function novoSubAmostra() {
	$.post('/sub-amostra/verifica-permissao', {sOP: "Cadastrar"} ,function(data) {
		if (data) {
			$(window.document.location).attr('href',"/sub-amostra/cadastrar");
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}

function alterarSubAmostra() {
	$.post('/sub-amostra/verifica-permissao', { sOP: "Alterar" }, function(data) {
		if (data) {
			if ($('.trSelected').length > 1) {
				jAlert("Você deve selecionar apenas um item para alterar.","Atenção");
			}
			else if ($('.trSelected').length == 0) {
				jAlert("Você deve selecionar um item para alterar.","Atenção");
			}
			else {
				var nId = $('.trSelected td:first-child div').text();
				$(window.document.location).attr('href',"/sub-amostra/alterar/nId/"+nId);
			}
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}

function excluirSubAmostra() {
	if ($('.trSelected').length == 0) {
		jAlert("Você deve selecionar ao menos um ítem.","Atenção");
	}
	else {
		if (confirm('Você tem certeza que deseja excluir ' + $('.trSelected').length + ' Sub-Amostra(s)?')) {
		$.post('/sub-amostra/verifica-permissao', { sOP: "Excluir" } ,function(data) {
				if (data) {
					var selArry = [];
					var nI = 1;
					$("div.flexigrid .trSelected").each(function(trI,tr){
						selArry.push($(tr).attr("id").substr(3));
					});
					$.post('/sub-amostra/excluir', { fId: selArry, sOP: "Excluir" } ,function(data) {
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
