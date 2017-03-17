$().ready(function() {
	$(".grid").flexigrid({
		url : '/protocolo/gera-xml',
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
			name : 'nm_protocolo',
			width : 300,
			sortable : true,
			align : 'left'
		}, {
			display : 'Descrição',
			name : 'descricao',
			width : 400,
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
		} , {
			name : 'Atibuir Metodo',
			bclass : 'variavel',
			onpress : setMetodo
		}, {
			name : 'Atibuir Responsavel pelo Protocolo',
			bclass : 'variavel',
			onpress : setResponsavel
		}, {
			name : 'Atibuir Curador(es) a Coleção',
			bclass : 'variavel',
			onpress : setCurador
		} , {
			name : 'Atibuir Projetos/Programas',
			bclass : 'variavel',
			onpress : setProjetoPrograma
		}],
		searchitems : [ {
			display : 'Nome',
			name : 'nm_protocolo',
			isdefault : false
		}
		],
		sortname : "sigla",
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
	$.post('/protocolo-projetos-programas/verifica-permissao', { sOP: "Cadastrar" }, function(data) {
		if (data) {
			if ($('.trSelected').length > 1) {
				jAlert("Você deve selecionar apenas um item para alterar.","Atenção");
			}
			else if ($('.trSelected').length == 0) {
				jAlert("Você deve selecionar um item para alterar.","Atenção");
			}
			else {
				var nId = $('.trSelected td:first-child div').text();
				$(window.document.location).attr('href',"/protocolo-projetos-programas/index/nId/"+nId);
			}
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}


function setCurador(){
	$.post('/curador/verifica-permissao', { sOP: "Cadastrar" }, function(data) {
		if (data) {
			if ($('.trSelected').length > 1) {
				jAlert("Você deve selecionar apenas um item para Atibuir Responsavel pelo(s) Protocolo(s).","Atenção");
			}
			else if ($('.trSelected').length == 0) {
				jAlert("Você deve selecionar apenas um item para Atibuir Responsavel pelo(s) Protocolo(s).","Atenção");
			}
			else {
				var nId = $('.trSelected td:first-child div').text();
				$(window.document.location).attr('href',"/curador/index/nId/"+nId);
			}
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}


function setResponsavel(){
	$.post('/responsaveis-protocolo/verifica-permissao', { sOP: "Cadastrar" }, function(data) {
		if (data) {
			if ($('.trSelected').length > 1) {
				jAlert("Você deve selecionar apenas um item para Atibuir Responsavel pelo(s) Protocolo(s).","Atenção");
			}
			else if ($('.trSelected').length == 0) {
				jAlert("Você deve selecionar apenas um item para Atibuir Responsavel pelo(s) Protocolo(s).","Atenção");
			}
			else {
				var nId = $('.trSelected td:first-child div').text();
				$(window.document.location).attr('href',"/responsaveis-protocolo/index/nId/"+nId);
			}
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}


function setMetodo(){
	$.post('/protocolos-metodos/verifica-permissao', { sOP: "Cadastrar" }, function(data) {
		if (data) {
			if ($('.trSelected').length > 1) {
				jAlert("Você deve selecionar apenas um item para alterar.","Atenção");
			}
			else if ($('.trSelected').length == 0) {
				jAlert("Você deve selecionar um item para alterar.","Atenção");
			}
			else {
				var nId = $('.trSelected td:first-child div').text();
				$(window.document.location).attr('href',"/protocolos-metodos/index/nId/"+nId);
			}
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}


function novoModulo() {
	$.post('/protocolo/verifica-permissao', {sOP: "Cadastrar"} ,function(data) {
		if (data) {
			$(window.document.location).attr('href',"/protocolo/cadastrar");
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}

function alterarModulo() {
	$.post('/protocolo/verifica-permissao', { sOP: "Alterar" }, function(data) {
		if (data) {
			if ($('.trSelected').length > 1) {
				jAlert("Você deve selecionar apenas um item para alterar.","Atenção");
			}
			else if ($('.trSelected').length == 0) {
				jAlert("Você deve selecionar um item para alterar.","Atenção");
			}
			else {
				var nId = $('.trSelected td:first-child div').text();
				$(window.document.location).attr('href',"/protocolo/alterar/nId/"+nId);
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
		if (confirm('Você tem certeza que deseja excluir ' + $('.trSelected').length + ' Metodo(s)?')) {
			$.post('/protocolo/verifica-permissao', { sOP: "Excluir" } ,function(data) {
				if (data) {
					var selArry = [];
					var nI = 1;
					$("div.flexigrid .trSelected").each(function(trI,tr){
						selArry.push($(tr).attr("id").substr(3));
					});
					$.post('/protocolo/excluir', { fId: selArry, sOP: "Excluir" } ,function(data) {
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

