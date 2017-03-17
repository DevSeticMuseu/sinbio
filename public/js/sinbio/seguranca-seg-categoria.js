$().ready(function() {
	$(".seg-categoria").flexigrid({
		url : 'post-xml',
		dataType : 'xml',
		colModel : [ {
			display : 'Id',
			name : 'id',
			width : 40,
			sortable : true,
			align : 'center'
		}, {
			display : 'Nome',
			name : 'descricao',
			width : 200,
			sortable : true,
			align : 'left'
		}, {
			display : 'Ações',
			name : '',
			showToggleBtn: false,
			width : 500,
			sortable : true,
			align : 'left'
		}],
		buttons : [ {
			name : 'Novo',
			bclass : 'add',
			onpress : novoCategoria
		}, {
			name : 'Alterar',
			bclass : 'edit',
			onpress : alterarCategoria
		}, {
			name : 'Excluir',
			bclass : 'delete',
			onpress : excluirCategoria
		}, {
			separator : true
		} ],
		searchitems : [ {
			display : 'Nome',
			name : 'descricao',
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

function novoCategoria() {
	$.post('/verifica-permissao', { sDescricao: "Transacao", sOP: "Cadastrar" } ,function(data) {
		if (data) {
			$(window.document.location).attr('href',"/seguranca/transacao/insere-altera");
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}

function alterarCategoria() {
	$.post('/verifica-permissao', { sDescricao: "Transacao", sOP: "Alterar" }, function(data) {
		if (data) {
			if ($('.trSelected').length > 1) {
				jAlert("Você deve selecionar apenas um item para alterar.","Atenção");
			}
			else if ($('.trSelected').length == 0) {
				jAlert("Você deve selecionar um item para alterar.","Atenção");
			}
			else {
				var nId = $('.trSelected td:first-child div').text();
				$(window.document.location).attr('href',"/seguranca/transacao/insere-altera/sOP/Alterar/nId/"+nId);
			}
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}

function excluirCategoria() {
	if ($('.trSelected').length == 0) {
		jAlert("Você deve selecionar ao menos um ítem.","Atenção");
	}
	else {
		if (confirm('Você tem certeza que deseja excluir ' + $('.trSelected').length + ' Categoria(s)?')) {
			$.post('/verifica-permissao', { sDescricao: "Transacao", sOP: "Excluir" } ,function(data) {
				if (data) {
					var selArry = [];
					var nI = 1;
					$("div.flexigrid .trSelected").each(function(trI,tr){
						selArry.push($(tr).attr("id").substr(3));
					});
					$.post('/seguranca/transacao/processa', { fId: selArry, sOP: "Excluir" } ,function(data) {
						//location.reload();
					});
				}
				else {
					jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
				}
			});
		}
	}
}
