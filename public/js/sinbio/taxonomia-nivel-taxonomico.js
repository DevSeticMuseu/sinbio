$().ready(function() {
	$(".grid").flexigrid({
		url : '/nivel-taxonomico/gera-xml',
		dataType : 'xml',
		colModel : [ {
			display : 'Id',
			name : 'id',
			width : 40,
			sortable : true,
			align : 'center'
		}, {
			display : 'Nivel Taxonomico',
			name : 'nivel_taxonomico',
			width : 200,
			sortable : true,
			align : 'left'
		}, {
			display : 'Nivel Superior',
			name : 'nivel_superior',
			width : 200,
			sortable : true,
			align : 'left'
		},{
			display : 'Nome',
			name : 'nm_nivel_taxonomico',
			width : 200,
			sortable : true,
			align : 'left'
		}],
		buttons : [ {
			name : 'Novo',
			bclass : 'add',
			onpress : novoNivel
		}, {
			name : 'Alterar',
			bclass : 'edit',
			onpress : alterarNivel
		}, {
			name : 'Excluir',
			bclass : 'delete',
			onpress : excluirNivel
		},
		 {
			separator : true
		} ],
		searchitems : [ {
			display : 'Nome',
			name : 'nm_nivel_taxonomico',
			isdefault : false
		}
		],
		sortname : "nm_nivel_taxonomico",
		sortorder : "asc",
		usepager : true,
		useRp : true,
		rp : 15,
		showTableToggleBtn : true,
		width : '100%',
		height : 460
	});
});


function novoNivel() {
	$.post('/nivel-taxonomico/verifica-permissao', {sOP: "Cadastrar"} ,function(data) {
		if (data) {
			$(window.document.location).attr('href',"/nivel-taxonomico/cadastrar");
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}

function alterarNivel() {
	$.post('/nivel-taxonomico/verifica-permissao', { sOP: "Alterar" }, function(data) {
		if (data) {
			if ($('.trSelected').length > 1) {
				jAlert("Você deve selecionar apenas um item para alterar.","Atenção");
			}
			else if ($('.trSelected').length == 0) {
				jAlert("Você deve selecionar um item para alterar.","Atenção");
			}
			else {
				var nId = $('.trSelected td:first-child div').text();
				$(window.document.location).attr('href',"/nivel-taxonomico/alterar/nId/"+nId);
			}
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}

function excluirNivel() {
	if ($('.trSelected').length == 0) {
		jAlert("Você deve selecionar ao menos um ítem.","Atenção");
	}
	else {
		if (confirm('Você tem certeza que deseja excluir ' + $('.trSelected').length + ' Módulos(s)?')) {
			$.post('/nivel-taxonomico/verifica-permissao', { sOP: "Excluir" } ,function(data) {
				if (data) {
					var selArry = [];
					var nI = 1;
					$("div.flexigrid .trSelected").each(function(trI,tr){
						selArry.push($(tr).attr("id").substr(3));
					});
					$.post('/nivel-taxonomico/excluir', { fId: selArry, sOP: "Excluir" } ,function(data) {
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

