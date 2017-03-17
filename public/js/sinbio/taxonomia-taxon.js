$().ready(function() {
	$(".grid").flexigrid({
		url : '/taxon/gera-xml',
		dataType : 'xml',
		colModel : [ {
			display : 'Id',
			name : 'id',
			width : 40,
			sortable : true,
			align : 'center'
		},
            {
			display : 'Nivel Taxonomico',
			name : 'nivel_taxonomico',
			width : 140,
			sortable : true,
			align : 'left'
		} ,    
            {
			display : 'Taxon de Refer&ecirc;ncia',
			name : 'taxon',
			width : 140,
			sortable : true,
			align : 'left'
		} ,    
            {
			display : 'Nome do Taxon',
			name : 'taxon_superior',
			width : 200,
			sortable : true,
			align : 'left'
		} ,    
                  {
			display : 'Nome do Autor',
			name : 'autor_ano',
			width : 200,
			sortable : true,
			align : 'left'
		} ,{
			display : 'Ano',
			name : 'data_2',
			width : 200,
			sortable : true,
			align : 'left'
		} ,{
			display : 'Refer&ecirc;ncia Bibliogr&aacute;fica',
			name : 'referencia',
			width : 200,
			sortable : true,
			align : 'left'
		}
            
        ],
		buttons : [ {
			name : 'Novo',
			bclass : 'add',
			onpress : novoTaxon
		}, {
			name : 'Alterar',
			bclass : 'edit',
			onpress : alterarTaxon
		}, {
			name : 'Excluir',
			bclass : 'delete',
			onpress : excluirTaxon
		}, {
			separator : true
		} ],
		searchitems : [ {
			display : 'Nome do Taxon',
			name : 'taxon'
		}],
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




function novoTaxon() {
	$.post('/taxon/verifica-permissao', {sOP: "Cadastrar"} ,function(data) {
		if (data) {
			$(window.document.location).attr('href',"/taxon/cadastrar");
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}

function alterarTaxon() {
	$.post('/taxon/verifica-permissao', { sOP: "Alterar" }, function(data) {
		if (data) {
			if ($('.trSelected').length > 1) {
				jAlert("Você deve selecionar apenas um item para alterar.","Atenção");
			}
			else if ($('.trSelected').length == 0) {
				jAlert("Você deve selecionar um item para alterar.","Atenção");
			}
			else {
				var nId = $('.trSelected td:first-child div').text();
				$(window.document.location).attr('href',"/taxon/alterar/nId/"+nId);
			}
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}

function excluirTaxon() {
	if ($('.trSelected').length == 0) {
		jAlert("Você deve selecionar ao menos um ítem.","Atenção");
	}
	else {
		if (confirm('Você tem certeza que deseja excluir ' + $('.trSelected').length + ' Usuário(s)?')) {
			$.post('/taxon/verifica-permissao', { sOP: "Excluir" } ,function(data) {
				if (data) {
					var selArry = [];
					var nI = 1;
					$("div.flexigrid .trSelected").each(function(trI,tr){
						selArry.push($(tr).attr("id").substr(3));
					});
					$.post('/taxon/excluir', { fId: selArry, sOP: "Excluir" } ,function(data) {
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