$().ready(function() {
    $(".grid").flexigrid({
        url : '/instituicao/gera-xml',
        dataType : 'xml',
        colModel : [ {
            display : 'Id',
            name : 'id',
            width : 40,
            sortable : true,
            align : 'center'
        }, {
            display : 'Nome da Instituicao',
            name : 'razao_social',
            width : 400,
            sortable : true,
            align : 'left'
        }, {
            display : 'Município',
            name : 'nm_municipio',
            width : 150,
            sortable : true,
            align : 'left'
        }, {
            display : 'Url',
            name : 'url',
            width : 250,
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
            name : 'Atibuir Projetos/Programas',
            bclass : 'variavel',
            onpress : setProjetoPrograma
        }],
        searchitems : [ {
            display : 'Nome',
            name : 'razao_rocial',
            isdefault : false
        },
        {
            display : 'Url',
            name : 'url',
            isdefault : false
        }
        ],
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
    $.post('/instituicao-projetos-programas/verifica-permissao', {
        sOP: "Cadastrar"
    }, function(data) {
        if (data) {
            if ($('.trSelected').length > 1) {
                jAlert("Você deve selecionar apenas um item para alterar.","Atenção");
            }
            else if ($('.trSelected').length == 0) {
                jAlert("Você deve selecionar um item para alterar.","Atenção");
            }
            else {
                var nId = $('.trSelected td:first-child div').text();
                $(window.document.location).attr('href',"/instituicao-projetos-programas/index/nId/"+nId);
            }
        }
        else {
            jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
        }
    });
}



function novoModulo() {
    $.post('/instituicao/verifica-permissao', {
        sOP: "Cadastrar"
    } ,function(data) {
        if (data) {
            $(window.document.location).attr('href',"/instituicao/cadastrar");
        }
        else {
            jAlert("Voce não possui permissão de acessar essa área.","Acesso Negado");
        }
    });
}

function alterarModulo() {
    $.post('/instituicao/verifica-permissao', {
        sOP: "Alterar"
    }, function(data) {
        if (data) {
            if ($('.trSelected').length > 1) {
                jAlert("Você deve selecionar apenas um item para alterar.","Atenção");
            }
            else if ($('.trSelected').length == 0) {
                jAlert("Você deve selecionar um item para alterar.","Atenção");
            }
            else {
                var nId = $('.trSelected td:first-child div').text();
                $(window.document.location).attr('href',"/instituicao/alterar/nId/"+nId);
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
        if (confirm('Você tem certeza que deseja excluir ' + $('.trSelected').length + ' Instituição(ões)?')) {
            $.post('/instituicao/verifica-permissao', {
                sOP: "Excluir"
            } ,function(data) {
                if (data) {
                    var selArry = [];
                    var nI = 1;
                    $("div.flexigrid .trSelected td:first-child").each(function(){
                        selArry.push($(this).text());
                    });
                    $.post('/instituicao/excluir', {
                        fId: selArry, 
                        sOP: "Excluir"
                    } ,function(data) {
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

