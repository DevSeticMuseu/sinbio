$().ready(function() {
	$(".grid").flexigrid({
		url : '/projeto-programa/gera-xml',
		dataType : 'xml',
		colModel : [ {
			display : 'Id',
			name : 'id',
			width : 40,
			sortable : true,
			align : 'center'
		}, {
			display : 'Sigla',
			name : 'sigla',
			width : 150,
			sortable : true,
			align : 'left'
		}, {
			display : 'Nome',
			name : 'nm_projeto_programa',
			width : 200,
			sortable : true,
			align : 'left'
		}, {
			display : 'Descrição',
			name : 'descricao',
			width : 200,
			sortable : true,
			align : 'left'
		}, {
			display : 'Cordenador',
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
			name : 'nm_uf',
			isdefault : false
		}
		],
		sortname : "nm_projeto_programa",
		sortorder : "asc",
		usepager : true,
		useRp : true,
		rp : 15,
		showTableToggleBtn : true,
		width : '100%',
		height : 460
	});
});


function novoModulo() {
	$.post('/projeto-programa/verifica-permissao', {sOP: "Cadastrar"} ,function(data) {
		if (data) {
			$(window.document.location).attr('href',"/projeto-programa/cadastrar");
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}

function alterarModulo() {
	$.post('/projeto-programa/verifica-permissao', { sOP: "Alterar" }, function(data) {
		if (data) {
			if ($('.trSelected').length > 1) {
				jAlert("Você deve selecionar apenas um item para alterar.","Atenção");
			}
			else if ($('.trSelected').length == 0) {
				jAlert("Você deve selecionar um item para alterar.","Atenção");
			}
			else {
				var nId = $('.trSelected td:first-child div').text();
				$(window.document.location).attr('href',"/projeto-programa/alterar/nId/"+nId);
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
		if (confirm('Você tem certeza que deseja excluir ' + $('.trSelected').length + ' Projeto(s)/Programa(s)?')) {
			$.post('/projeto-programa/verifica-permissao', { sOP: "Excluir" } ,function(data) {
				if (data) {
					var selArry = [];
					var nI = 1;
					$("div.flexigrid .trSelected").each(function(trI,tr){
						selArry.push($(tr).attr("id").substr(3));
					});
					$.post('/projeto-programa/excluir', { fId: selArry, sOP: "Excluir" } ,function(data) {
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

