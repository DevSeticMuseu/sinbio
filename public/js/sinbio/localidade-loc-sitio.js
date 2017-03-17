$().ready(function() {
	$(".grid").flexigrid({
		url : '/sitio/gera-xml',
		dataType : 'xml',
		colModel : [ {
			display : 'Id',
			name : 'id',
			width : 40,
			sortable : true,
			align : 'center'
		}, {
			display : 'Nome',
			name : 'nm_sitio',
			width : 300,
			sortable : true,
			align : 'left'
		},{
			display : 'Nucleo',
			name : 'nm_nucleo',
			width : 200,
			sortable : true,
			align : 'left'
		}, {
			display : 'Municipio',
			name : 'nm_municipio',
			width : 200,
			sortable : true,
			align : 'left'
		}, {
			display : 'Latitude',
			name : 'latitude',
			width : 200,
			sortable : true,
			align : 'left'
		}, {
			display : 'Longitude',
			name : 'longitude',
			width : 200,
			sortable : true,
			align : 'left'
		}],
		buttons : [ {
			name : 'Novo',
			bclass : 'add',
			onpress : novoUsuario
		}, {
			name : 'Alterar',
			bclass : 'edit',
			onpress : alterarUsuario
		}, {
			name : 'Excluir',
			bclass : 'delete',
			onpress : excluirUsuario
		}, {
			separator : true
		} , {
			name : 'Atibuir Projetos/Programas',
			bclass : 'variavel',
			onpress : setProjetoPrograma
		}],
		searchitems : [ {
			display : 'Nome Sitio',
			name : 'nm_sitio'
		},
            {
                display : 'Nome Municipio',
			name : 'nm_municipio'
            }],
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

function setProjetoPrograma(){
	$.post('/sitio-projetos-programas/verifica-permissao', { sOP: "Cadastrar" }, function(data) {
		if (data) {
			if ($('.trSelected').length > 1) {
				jAlert("Você deve selecionar apenas um item para alterar.","Atenção");
			}
			else if ($('.trSelected').length == 0) {
				jAlert("Você deve selecionar um item para alterar.","Atenção");
			}
			else {
				var nId = $('.trSelected td:first-child div').text();
				$(window.document.location).attr('href',"/sitio-projetos-programas/index/nId/"+nId);
			}
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}

function novoUsuario() {
	$.post('/sitio/verifica-permissao', {sOP: "Cadastrar"} ,function(data) {
		if (data) {
			$(window.document.location).attr('href',"/sitio/cadastrar");
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}

function alterarUsuario() {
	$.post('/sitio/verifica-permissao', { sOP: "Alterar" }, function(data) {
		if (data) {
			if ($('.trSelected').length > 1) {
				jAlert("Você deve selecionar apenas um item para alterar.","Atenção");
			}
			else if ($('.trSelected').length == 0) {
				jAlert("Você deve selecionar um item para alterar.","Atenção");
			}
			else {
				var nId = $('.trSelected td:first-child div').text();
				$(window.document.location).attr('href',"/sitio/alterar/nId/"+nId);
			}
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}

function excluirUsuario() {
	if ($('.trSelected').length == 0) {
		jAlert("Você deve selecionar ao menos um ítem.","Atenção");
	}
	else {
		if (confirm('Você tem certeza que deseja excluir ' + $('.trSelected').length + ' Sítio(s)?')) {
			$.post('/sitio/verifica-permissao', { sOP: "Excluir" } ,function(data) {
				if (data) {
					var selArry = [];
					var nI = 1;
					$("div.flexigrid .trSelected").each(function(trI,tr){
						selArry.push($(tr).attr("id").substr(3));
					});
					$.post('/sitio/excluir', { fId: selArry, sOP: "Excluir" } ,function(data) {
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
