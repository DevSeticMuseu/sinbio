$().ready(function() {
	$(".grid").flexigrid({
		url : '/usuario/gera-xml',
		dataType : 'xml',
		colModel : [ {
			display : 'Id',
			name : 'id',
			width : 40,
			sortable : true,
			align : 'center'
		}, {
			display : 'Nome',
			name : 'nm_usuario',
			width : 300,
			sortable : true,
			align : 'left'
		}, {
			display : 'Login',
			name : 'login',
			width : 200,
			sortable : true,
			align : 'left'
		}, {
			display : 'Grupo',
			name : 'nm_grupo',
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
			name : 'Ficha do Usuário',
			bclass : 'variavel',
			onpress : setFichaUsuario
		}],
		searchitems : [  {
			display : 'Nome',
			name : 'nm_usuario',
			isdefault : true
		}, {
			display : 'Login',
			name : 'login'
		}, {
			display : 'Grupo',
			name : 'nm_grupo'
		}],
		sortname : "nm_usuario",
		sortorder : "asc",
		usepager : true,
		useRp : true,
		rp : 15,
		showTableToggleBtn : true,
		width : '100%',
		height : 460
	});
});


function setFichaUsuario(){
	$.post('/usuario-ficha/verifica-permissao', { sOP: "Cadastrar" }, function(data) {
		if (data) {
			if ($('.trSelected').length > 1) {
				jAlert("Você deve selecionar apenas um item para alterar.","Atenção");
			}
			else if ($('.trSelected').length == 0) {
				jAlert("Você deve selecionar um item para alterar.","Atenção");
			}
			else {
				var nId = $('.trSelected td:first-child div').text();
				$(window.document.location).attr('href',"/usuario-ficha/index/nId/"+nId);
			}
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}


function novoUsuario() {
	$.post('/usuario/verifica-permissao', {sOP: "Cadastrar"} ,function(data) {
		if (data) {
			$(window.document.location).attr('href',"/usuario/cadastrar");
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}

function alterarUsuario() {
	$.post('/usuario/verifica-permissao', { sOP: "Alterar" }, function(data) {
		if (data) {
			if ($('.trSelected').length > 1) {
				jAlert("Você deve selecionar apenas um item para alterar.","Atenção");
			}
			else if ($('.trSelected').length == 0) {
				jAlert("Você deve selecionar um item para alterar.","Atenção");
			}
			else {
				var nId = $('.trSelected td:first-child div').text();
				$(window.document.location).attr('href',"/usuario/alterar/nId/"+nId);
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
		if (confirm('Você tem certeza que deseja excluir ' + $('.trSelected').length + ' Usuário(s)?')) {
			$.post('/usuario/verifica-permissao', { sOP: "Excluir" } ,function(data) {
				if (data) {
					var selArry = [];
					var nI = 1;
					$("div.flexigrid .trSelected").each(function(trI,tr){
						selArry.push($(tr).attr("id").substr(3));
					});
					$.post('/usuario/excluir', { fId: selArry, sOP: "Excluir" } ,function(data) {
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
