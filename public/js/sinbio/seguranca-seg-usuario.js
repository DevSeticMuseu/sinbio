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


function novoModulo() {
	$.post('/usuario/verifica-permissao', {sOP: "Cadastrar"} ,function(data) {
		if (data) {
			$(window.document.location).attr('href',"/usuario/cadastrar");
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}

function alterarModulo() {
	$.post('/usuario/verifica-permissao', { sOP: "Alterar" }, function(data) {
		if (data) {
			if ($('tr.selected').length > 1) {
				jAlert("Você deve selecionar apenas um item para alterar.","Atenção");
			}
			else if ($('tr.selected').length == 0) {
				jAlert("Você deve selecionar um item para alterar.","Atenção");
			}
			else {
				var nId = table.row(table.$('tr.selected')).data()[0];
				$(window.document.location).attr('href',"/usuario/alterar/nId/"+nId);
			}
		}
		else {
			jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
		}
	});
}

    function excluirModulo() {
        if ($('tr.selected').length == 0) {
            jAlert("Você deve selecionar ao menos um ítem.", "Atenção");
        }
        else {
            var info = [];
            var columnInfo;
            table.$('tr.selected').each(function () {
                columnInfo = table.row(this).data()[1];
                info.push(columnInfo);
            });

            if (confirm('Você tem certeza que deseja excluir o(s) usuários(s): ' + info + ' ?')) {
                $.post('/usuario/verifica-permissao', {sOP: "Excluir"}, function (data) {
                    if (data) {
                        var selArry = [];
                        var nId;

                        table.$('tr.selected').each(function () {
                            nId = table.row(this).data()[0];
                            selArry.push(nId);
                        });

                        $.post('/usuario/excluir', {fId: selArry, sOP: "Excluir"}, function (data) {
                            location.reload();
                        });
                    }
                    else {
                        jAlert("Voce não possui permissão de acessar essa área.", "Acesso Negado");
                    }
                });
            }
        }
    }