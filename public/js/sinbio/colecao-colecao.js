$().ready(function() {
	$(".grid").flexigrid({
		url : '/colecao/gera-xml',
		dataType : 'xml',
		colModel : [ {
			display : 'Id',
			name : 'id',
			width : 40,
			sortable : true,
			align : 'center'
		},
                {
			display : 'Nome da Coleção',
			name : 'nm_colecoes',
			width : 200,
			sortable : true,
			align : 'left'
		},
                {
			display : 'Sigla',
			name : 'sigla',
			width : 200,
			sortable : true,
			align : 'left'
		},
                {
			display : 'Curador',
			name : 'nm_usuario',
			width : 200,
			sortable : true,
			align : 'left'
		}],
		buttons : [ {
			name : 'Novo',
			bclass : 'add',
			onpress : novoModulo
		}, {
			name : 'Alterar',
			bclass : 'edit',
			onpress : alterarModulo
		}, {
			name : 'Excluir',
			bclass : 'delete',
			onpress : excluirModulo
		},
		 {
			separator : true
		} ],
		searchitems : [ {
			display : 'Nome',
			name : 'nm_colecoes',
			isdefault : false
		}
		],
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





function novoModulo() {
	$.post('/colecao/verifica-permissao', {sOP: "Cadastrar"} ,function(data) {
		if (data) {
			$(window.document.location).attr('href',"/colecao/cadastrar");
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}

function alterarModulo() {
	$.post('/colecao/verifica-permissao', { sOP: "Alterar" }, function(data) {
		if (data) {
			if ($('.trSelected').length > 1) {
				jAlert("Você deve selecionar apenas um item para alterar.","Atenção");
			}
			else if ($('.trSelected').length == 0) {
				jAlert("Você deve selecionar um item para alterar.","Atenção");
			}
			else {
				var nId = $('.trSelected td:first-child div').text();
				$(window.document.location).attr('href',"/colecao/alterar/nId/"+nId);
			}
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}

function excluirModulo() {
	if ($('.trSelected').length == 0) {
		jAlert("Você deve selecionar ao menos um ítem.","Atenção");
	}
	else {
		if (confirm('Você tem certeza que deseja excluir ' + $('.trSelected').length + ' Coleção(ões)?')) {
			$.post('/colecao/verifica-permissao', { sOP: "Excluir" } ,function(data) {
				if (data) {
					var selArry = [];
					var nI = 1;
					$("div.flexigrid .trSelected").each(function(trI,tr){
						selArry.push($(tr).attr("id").substr(3));
					});
					$.post('/colecao/excluir', { fId: selArry, sOP: "Excluir" } ,function(data) {
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

