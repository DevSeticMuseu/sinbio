    $().ready(function () {

        table = $('.tabela').DataTable({
            responsive: true,
            dom: 'rB<"container-buttons"><"container-pes-bas"><"container-pes-avan">tip',
            buttons: [
                {
                    text: 'Novo',
                    action: function () {
                        novoModulo();
                    }
                },
                {
                    text: 'Editar',
                    action: function () {
                        alterarModulo();
                    }
                },
                {
                    text: 'Apagar',
                    action: function () {
                        excluirModulo();
                    }
                }
            ],
            columnDefs: [
                {
                    "targets": [0],
                    "visible": false,
                    "searchable": false
                }
            ],
            oLanguage: {
                "sInfo": "Mostrando _START_ até _END_ de _TOTAL_ entradas",
                "oPaginate": {
                    "sPrevious": "Anterior",
                    "sNext": "Próximo"
                }
            },
            initComplete: function () {
                $('.tabela tbody').on('click', 'tr', function () {
                    if ($(this).hasClass('selected')) {
                        $(this).removeClass('selected');
                    }
                    else {
                        $(this).addClass('selected');
                    }
                });
            }

        });

        criarPesqBasica();
        criarPesqAvan();
        addButton();

    });
    
    function addButton() {

       

        new $.fn.dataTable.Buttons(table, {
            buttons: [
                {
                    text: 'teste',
                    action: function () {
                    }
                }
            ]
        });

        table.buttons(3, null).container().appendTo('.container-buttons');

    }


    //Métodos para troca de filtros ao clique dos botões
    function pesquisaAvancada() {
        clearInput();

        $('.container-pes-avan').show();
        $('.container-pes-bas').hide();
    }

    function pesquisaBasica() {
        clearInput();

        $('.container-pes-bas').show();
        $('.container-pes-avan').hide();

    }

    //Cria filtro e botão utilizado no container de pesquisa básica
    function criarPesqBasica() {

        var htmlFiltro = '<input class="filtro-basico" type="text" placeholder="Buscar">';
        $('.container-pes-bas').append(htmlFiltro);

        $('.filtro-basico').keyup(function () {
            table.search($(this).val()).draw();
        });

        new $.fn.dataTable.Buttons(table, {
            buttons: [
                {
                    text: 'Pesquisa Avançada',
                    action: function () {
                        pesquisaAvancada();
                    }
                }
            ]
        });

        table.buttons(1, null).container().appendTo('.container-pes-bas');

        $('.container-pes-bas').addClass('pull-right');
    }

    //Cria botão para retorno à pesquisa básica, filtros para cada coluna e adiona-os ao container
    function criarPesqAvan() {

        $('.tabela th').each(function () {
            var column = $(this);

            var htmlFiltro = '<input id="filtro-comp' + column.index() + '" class="filtro-composto" \n\
                                    type="text" placeholder="Buscar em ' + column.text() + '"/>';

            $('.container-pes-avan').append(htmlFiltro);

            $('#filtro-comp' + column.index() + '').keyup(function () {
                table.column(column.index() + 1).search($(this).val()).draw();
            });

        });

        new $.fn.dataTable.Buttons(table, {
            buttons: [
                {
                    text: 'Pesquisa Básica',
                    action: function () {
                        pesquisaBasica();
                    }
                }
            ]
        });

        table.buttons(2, null).container().appendTo('.container-pes-avan');

        $('.container-pes-avan').addClass('pull-right');
        $('.container-pes-avan').hide();

    }

    function clearInput() {

        $('.filtro-basico').val('');
        table.search('').draw();
        $('.tabela th').each(function () {
            var column = $(this).index();
            $('#filtro-comp' + column + '').val('');
            table.columns().search('').draw();
        });

    }