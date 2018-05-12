var arrayVeiculos = '';

$(document).ready(function(){
    $('#prProprietario').on('change',function(){
        $.ajax({
            type: "POST",
            url: ROOT+'/painel/roteirizador/dados/parametrizacao',
            // headers: { 'X-CSRF-TOKEN': $("#token").attr('value')},
            data: {
                prproprietario:$('#prProprietario').val()
            },
            dataType: "json",
            'success': function (d) {
                var dados = d.ponto_saida
                arrayVeiculos = d.ponto_saida;
                opt = '';
                for(i in dados) {
                    if(i == 0){
                        opt += '<option selected value="'+dados[i].id+'">'+dados[i].descricao+'</option>'
                    }else{
                        opt += '<option value="'+dados[i].id+'">'+dados[i].descricao+'</option>'
                    }
                }
                $("#prPontoSaida").html(opt);

                dados = d.ponto_retorno;
                opt = '';
                for(i in dados) {
                    if(i == 0){
                        opt += '<option selected value="'+dados[i].id+'">'+dados[i].descricao+'</option>'
                    }else{
                        opt += '<option value="'+dados[i].id+'">'+dados[i].descricao+'</option>'
                    }
                }
                $("#prPontoRetorno").html(opt);

                dados = d.regioes;
                opt = '';
                for(i in dados) {
                    opt += '<option value="'+dados[i].recodigo+'">'+dados[i].redescricao+'</option>'
                }
                $("#prRegioes").html(opt);

            }
        });
    })
    $('#prProprietario').trigger('change');
})


$('#carregarParametrosRoterizacao').on('click',function(){
    $.ajax({
        type: "POST",
        url: ROOT+'/painel/roteirizador/carregar/parametros',
        headers: { 'X-CSRF-TOKEN': $("#token").attr('value')},
        data: {
            dtsaida:$('#prDataSaida').val(),
            prproprietario:$('#prProprietario').val(),
            regioes:$('#prRegioes').val(),
            prsaida:$('#prPontoSaida').val(),
            prretorno:$('#prPontoRetorno').val()
        },
        dataType: "json",
        'success': function (dados) {
            preencherListaPedidos(dados.itens_rotas);
            preencherListaVeiculos(dados.veiculos);

            $("#cadastroParametrizacao .hidden").removeClass('hidden');
        }
    });
});

var tableP;
var vol = 0;
var cub = 0.0;
var kg = 0.0;
var val = 0.0;
var totalVol = 0;
var totalCub = 0;
var totalkg = 0;
var totalval = 0;

function preencherListaPedidos(itrotas){
    var dataSet = [];
    vol = 0;
    cub = 0.0;
    kg = 0.0;
    val = 0.0;
    for(i in itrotas) {
        var tr = ''
        var data = []
        tr += ' <input type="checkbox" class="check-pedido" name="pedidos[]" checked value="'+itrotas[i].ircodigo+'">'
        data.push(tr);
        data.push(itrotas[i].irnome);
        data.push(itrotas[i].irdocumento);
        data.push(itrotas[i].irqtde);
        vol = vol + parseInt(itrotas[i].irqtde);
        data.push(itrotas[i].ircubagem);
        cub = cub + parseFloat(itrotas[i].ircubagem);
        data.push(itrotas[i].irpeso);
        kg = kg + parseFloat(itrotas[i].irpeso);
        data.push(itrotas[i].irvalor);
        val = val + parseFloat(itrotas[i].irvalor);
        data.push(Number(itrotas[i].irvalor).formatMoney());

        if(itrotas[i].potipo == 'C'){
            itrotas[i].potipo = "Coleta";
        }else if (itrotas[i].potipo == 'E'){
            itrotas[i].potipo = "Entrega";
        }
        data.push(itrotas[i].potipo);
        tr = '';
        tr += '<a title="Excluir Pedido" data-url="'+ROOT+'/painel/roteirizador/destroy" data-id="'+itrotas[i].ircodigo+'" class="excluir-pedido btn btn-tb btn-danger" data-toggle="modal" data-target="#modalDelataDesativa" data-delete-action="#">'
            tr += '<span class="fa fa-minus"></span>'
        tr += '</a>'
        tr += '<a title="Editar Data Saída" data-url="'+ROOT+'/painel/roteirizador/edit" data-id="'+itrotas[i].ircodigo+'" class="btn editar-data-saida btModalEdit btn-info">'
            tr += '<span class="fa fa-pencil"></span>'
        tr += '</a>'

        data.push(tr)
        dataSet.push(data)
    }
    totalVol = vol;
    if(cub.toFixed(2) == "-0.00"){
        cub = "0.00"
    }else {
        cub = cub.toFixed(2);
    }
    if(kg.toFixed(2) == "-0.00"){
        kg = "0.00"
    }else {
        kg = kg.toFixed(2);
    }
    totalCub = cub;
    totalkg = kg;
    totalval = Number(val.toFixed(2)).formatMoney();

    $('.ped-vol').html(Number(vol).formatMoney(2, "", ".", ","));
    $('.ped-cub').html(Number(cub).formatMoney(2, "", ".", ","));
    $('.ped-kg').html(Number(kg).formatMoney(2, "", ".", ","));
    $('.ped-val').html(Number(val.toFixed(2)).formatMoney());
    $('.totais').show();
    $('#incluirTodos').prop('disabled', false);
    $('#incluirTodosVeiculos').prop('disabled', false);

    if ($.fn.DataTable.isDataTable('#tableRListarPedidos')) {
        $('#tableRListarPedidos').DataTable().destroy();
    }
    $(".divFooter1").remove();
    tableP =  $('#tableRListarPedidos').DataTable({
        paging: false,
        retrieve: true,
        scrollY:  "200px",
        scrollCollapse: true,
        language: traducao,
        data: dataSet,
        columnDefs: [{"targets": [6], "visible": false}],
    });

    checkHasPedidoOrVeiculoChecked();

    setTimeout(function(){
        $(".th-pedido-automatico").trigger('click')
        $(".th-placa-automatico").trigger('click')

    }, 500);
}

$(document).on('click','.check-pedido',function(){
        val = $(this).parent().siblings()[5];
    if ($(this).is(':checked')) {
        vol = 0 + parseInt($($(this).parent().siblings()[2]).html());
        cub = 0 + parseFloat($($(this).parent().siblings()[3]).html());
        kg = 0 + parseFloat($($(this).parent().siblings()[4]).html());
        soma = 0 + parseFloat($(val).html().replace(/[^0-9-,]/g, ''));
    }else{
        vol = 0 - parseInt($($(this).parent().siblings()[2]).html());
        cub = 0 - parseFloat($($(this).parent().siblings()[3]).html());
        kg = 0 - parseFloat($($(this).parent().siblings()[4]).html());
        soma = 0 - parseFloat($(val).html().replace(/[^0-9-,]/g, ''));
    }

    if($('.check-pedido:checked').length < $('.check-pedido').length) {
        $("#incluirTodos").attr('checked', false);
    }else{
        $("#incluirTodos").attr('checked', true);
    }

    var replaceVal = $('.ped-val').html().replace(/[^0-9-,]/g, '')
    var replaceVol = $('.ped-vol').html().replace(/[^0-9-,]/g, '')
    var replaceCub = $('.ped-cub').html().replace(/[^0-9-,]/g, '')
    var replaceKg  = $('.ped-kg').html().replace(/[^0-9-,]/g, '')

    var valor = parseFloat(replaceVal.replace(',', '.'));
    var totalVoll = parseFloat(replaceVol.replace(',', '.'));
    var totalCubb = parseFloat(replaceCub.replace(',', '.'));
    var totalKG = parseFloat(replaceKg.replace(',', '.'));

    $('.ped-vol').html(Number(parseFloat(vol) + parseFloat(totalVoll)).formatMoney(2, "", ".", ","));
    $('.ped-cub').html(Number(Number(parseFloat(cub) + parseFloat(totalCubb)).toFixed(2)).formatMoney(2, "", ".", ","));
    $('.ped-kg').html(Number(Number(parseFloat(kg) + parseFloat(totalKG)).toFixed(2)).formatMoney(2, "", ".", ","));
    $('.ped-val').html(Number(parseFloat(valor + parseFloat(soma))).formatMoney());

    checkHasPedidoOrVeiculoChecked();
});



$(document).on('click', '.btModalEdit', modalEdit);

function modalEdit(){

    var thad = $(".btModalEdit");
    var url = $(".btModalEdit").data('url')
    var id = $(".btModalEdit").data('id')
    bts = '';
    bts += '<button type="button" class="btn btn-default bt-cancelar" data-dismiss="modal">Cancelar</button>';
    bts += '<button type="button" data-url="'+url+'" data-id="'+id+'" class="btn btn-primary salvar-edit-item-jornada">Salvar</button>';

    $("#modalEditItensRota #dtSaidaEdited").val($("#prDataSaida").val())
    $("#modalEditItensRota .modal-footer").html(bts)
    $("#modalEditItensRota").modal('show');


    $(".bt-cancelar").click(function(){
        $("#dtSaidaEdited").val($("#prDataSaida").val());
    })

    $('.salvar-edit-item-jornada').click(function(){
        var url = $(this).data('url')
        var id = $(this).data('id')
        var value = $("#dtSaidaEdited").val()
        $("#dtSaidaEdited").val($("#prDataSaida").val());
        $.post(url,{'id':id,'value':value}, function(dados){
            $('#modalEditItensRota').modal('hide');
            $('#carregarParametrosRoterizacao').trigger('click');
        })
    })

}

var tableV;
var cap_v = 0.0;
var cub_v = 0.0;
var maxe_v = 0.0;
var totalCap = 0;
var totalVCub = 0;
var totalMaxC = 0;
var veCapacidade = 0;


function preencherListaVeiculos(veiculos){
    var dataSet = []
    cap_v = 0.0;
    cub_v = 0.0;
    maxe_v = 0.0;

    for(i in veiculos) {
        var tr = ''
        var data = []

        tr += ' <input class="check-veiculo " name="veiculos[]" type="checkbox" checked value="'+veiculos[i].vecodigo+'">'

        data.push(tr);

        data.push(veiculos[i].veplaca);
        data.push(veiculos[i].veprefixo);
        data.push(veiculos[i].vemaxpeso);
        cap_v = cap_v + parseFloat(veiculos[i].vemaxpeso);
        data.push(veiculos[i].vecubagem);
        cub_v = cub_v + parseFloat(veiculos[i].vecubagem);
        data.push(veiculos[i].vemaxentregas);
        maxe_v = maxe_v + parseFloat(veiculos[i].vemaxentregas);
        dataSet.push(data)
    }

    totalCap = cap_v;
    totalVCub = cub_v;
    totalMaxC = maxe_v;

    $('.veic-capacidade').html(Number(cap_v).formatMoney(2, "", ".", ","));
    $('.veic-cubagem').html(Number(cub_v).formatMoney(2, "", ".", ","));
    $('.veic-max-entregas').html(Number(maxe_v).formatMoney(2, "", ".", ","));

    if ($.fn.DataTable.isDataTable('#tableRListarVeiculos')) {
        $('#tableRListarVeiculos').DataTable().destroy();
    }
    $(".divFooter2").remove();
    tableV = $('#tableRListarVeiculos').DataTable({
        paging: false,
        retrieve: true,
        scrollY:  "200px",
        scrollCollapse: true,
        language: traducao,
        data: dataSet
    });
    checkHasPedidoOrVeiculoChecked();
}

$(document).on('click','.check-veiculo',function(){

    if($(this).is(':checked')){
        cap_v = + parseFloat($($(this).parent().siblings()[2]).html());
        cub_v = + parseFloat($($(this).parent().siblings()[3]).html());
        maxe_v = + parseInt($($(this).parent().siblings()[4]).html());
    }else {
        cap_v = 0 - parseFloat($($(this).parent().siblings()[2]).html());
        cub_v = 0 - parseFloat($($(this).parent().siblings()[3]).html());
        maxe_v = 0 - parseInt($($(this).parent().siblings()[4]).html());
    }
    if($('.check-veiculo:checked').length < $('.check-veiculo').length) {
        $("#incluirTodosVeiculos").attr('checked', false);
    }else{
        $("#incluirTodosVeiculos").attr('checked', true);
    }


    var totalKG = parseFloat($('.ped-kg').html().replace(/[^0-9-.]/g, ''));


    // $('.ped-val').html().replace(/[^0-9-,]/g, '')
    var replCap = $('.veic-capacidade').html().replace(/[^0-9-,]/g, '');
    var replCub = $('.veic-cubagem').html().replace(/[^0-9-,]/g, '');
    var replEnt = $('.veic-max-entregas').html().replace(/[^0-9-,]/g, '');

    veiCap = parseFloat(replCap.replace(',', '.'));
    var veiCub = parseFloat(replCub.replace(',', '.'));
    var veiEnt = parseFloat(replEnt.replace(',', '.'));

    $('.veic-capacidade').html(Number(cap_v + veiCap).formatMoney(2, "", ".", ","));
    $('.veic-cubagem').html(Number(cub_v + veiCub).formatMoney(2, "", ".", ","));
    $('.veic-max-entregas').html(Number(maxe_v + veiEnt).formatMoney(2, "", ".", ","));
    totalCap = cap_v + veiCap;

    checkHasPedidoOrVeiculoChecked();
});

function checkHasPedidoOrVeiculoChecked(){
    if ($('.check-veiculo:checked').length > 0 && $('.check-pedido:checked').length > 0){
        // TODO Descomentar para continuar a funcionalidade
        $("#montarCargas").attr('disabled',false);
    }else{
        $("#montarCargas").attr('disabled',true);
    }
}

$('#montarCargas').click(function(e){
    e.preventDefault()

    var thad = $(this);

var totalkgatual = ($('.ped-kg').html().replace(/[^0-9-,]/g, '').replace(',', '.'));
var petCub = ($('.ped-cub').html().replace(/[^0-9-,]/g, '').replace(',', '.'));
var veiCub = ($('.veic-cubagem').html().replace(/[^0-9-,]/g, '').replace(',', '.'));

    if(parseFloat(totalCap) < parseFloat(totalkgatual) || parseFloat(veiCub) < parseFloat(petCub)){
        var p = '<p>Os veículos selecionados não suportam a capacidade total dos pedidos! Inclua mais veículos ou remova alguns pedidos</p>';
        $("#modalClean .modal-body").html(p);
        $("#modalClean .modal-footer").html('<button type="button" class="btn btn-default" data-dismiss="modal">Continuar</button>')
        $("#modalClean").modal("show");

    }else{
        $(this).parent().prepend('<div class="fa-spinner-tmp"><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Gravando</div>');
        $('.fa-spinner-tmp').css({'float': 'left', 'margin-right': '10px'});
        $(this).css('display', 'none');
        $('input').prop('readonly', true);
        $('#formCadastroParametrizacao').ajaxForm({

            type:'post',
            success: function(dados) {
                $(thad).show();
                $(".fa-spinner-tmp").remove();
                var arrayVei = arrayVeiculos;

                var pedidos = dados.result.pedidos
                if(typeof pedidos != 'object' && pedidos) {
                    window.location.href = ROOT+"/painel/roteirizador/acompanhamento";
                }
                var cargas = dados.result.cargas
                var carros = dados.veiculos
                var cli = $("#prProprietario").val();
                var trPonto = '';
                var opt = ''
                for(i in arrayVei){
                    opt += '<option data-lat="'+arrayVei[i].lat+'" data-lng="'+arrayVei[i].lng+'" value="'+arrayVei[i].id+'">'+arrayVei[i].descricao+'</option>'
                }
                var form = '<form method="POST" action="'+ROOT+'/painel/roteirizador/rota/manual/itens/rota" class="form-horizontal">'+
                                '<div class="col-sm-12">'+
                                    '<div class="block-pontos-inicio-fim col-sm-5">'+
                                        '<label>Selecione o ponto de início*</label>'+
                                        '<select name="laloInicio" class="form-control ponto-inicio">'+opt+'</select>'+
                                        '<input type="hidden" class="hidCli" name="cli" value="'+cli+'">'+
                                    '</div>'+
                                    '<div class="block-pontos-inicio-fim col-sm-5">'+
                                        '<label>Selecione o ponto de fim*</label>'+
                                        '<select name="laloFim" class="form-control ponto-fim">'+opt+'</select>'+
                                    '</div>'+
                                '</div>'+
                                '<div class="block-pedidos">'+
                                    '<h5>Lista de Pedidos</h5>'+
                                    '<table id="tableRListarPedidos" class="table datatable table-hover table-condensed table-striped">'+
                                        '<thead>'+
                                            '<tr>'+
                                                '<th class="mo-check-pedidos"><input type="checkbox" id="incluirTodos" name="itens[]"></th>'+
                                                '<th>Ponto</th>'+
                                                '<th>Pedido</th>'+
                                                '<th>Volumes</th>'+
                                                '<th>Cubagem</th>'+
                                                '<th>Quilos</th>'+
                                                '<th>Valor</th>'+
                                            '</tr>'+
                                        '</thead>'+
                                        '<tbody></tbody>'+
                                    '</table>'+
                                '</div>'+
                                '<div class="totais">'+
                                    '<th class="">'+
                                        '<span class="totais-mod">Volumes: <span class="ped-vol">0</span></span>'+
                                        '<span class="totais-mod">Cubagem: <span class="ped-cub">0</span></span>'+
                                        '<span class="totais-mod">Quilos:  <span class="ped-kg">0</span></span>'+
                                        '<span class="totais-mod">Valores: <span class="ped-val">0</span></span>'+
                                    '</tr>'+
                                '</div>'+
                                '<hr class="col-xs-12"></hr>'+
                                '<div class="block-pedidos">'+
                                    '<h5>Lista de Veículos</h5>'+
                                    '<table id="tableRListarVeiculos" class="table datatable table-hover table-condensed table-striped">'+
                                        '<thead>'+
                                            '<tr>'+
                                                '<th class="mo-check-veiculos">'+
                                                '</th>'+
                                                '<th>Placa</th>'+
                                                '<th>Prefixo</th>'+
                                                '<th>Disponível/Capacidade KG</th>'+
                                                '<th>Cubagem</th>'+
                                                '<th>Max. Entregas</th>'+
                                            '</tr>'+
                                        '</thead>'+
                                        '<tbody></tbody>'+
                                    '</table>'+
                                '</div>'+

                                '<div class="text-right">'+
                                    '<span class="text-danger msg-remonta-carga"></span>'+
                                    '<button id="remontarCargas" type="submit" value="save" class="btn btn-info"><span class="glyphicon glyphicon-ok"></span>Montar Cargas</button>'+
                                    '<a href="'+ROOT+'/painel/roteirizador/finalizacao/rota" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span>Ignorar</a>'+
                                '</div>'+
                        '</form>'
                $('#modalLarge .modal-body').html(form);
                var dataSet = []
                for(i in pedidos) {
                    var tr = ''
                    var data = []
                    tr += '<input type="checkbox" class="check-pedido" name="itens[]" value="'+pedidos[i].ircodigo+'">'
                    data.push(tr);
                    data.push(pedidos[i].irnome);
                    data.push(pedidos[i].irdocumento);
                    data.push(pedidos[i].irqtde);
                    data.push(pedidos[i].ircubagem);
                    data.push(pedidos[i].irpeso);
                    data.push(pedidos[i].irvalor);

                    dataSet.push(data)
                }
                if ($.fn.DataTable.isDataTable('#modalLarge .modal-body #tableRListarPedidos')) {
                    $('#modalLarge .modal-body #tableRListarPedidos').DataTable().destroy();
                }
                tableP =  $('#modalLarge .modal-body #tableRListarPedidos').DataTable({
                    paging: false,
                    retrieve: true,
                    scrollY:  "200px",
                    scrollCollapse: true,
                    language: traducao,
                    data: dataSet,
                });

                tratamentoErrosCarros(carros);
                changeCargas()

                $("#modalLarge").modal("show");
                $('#modalLarge .check-pedido').click(function(){
                    selectItens($(this), carros)
                })
                $('#modalLarge #incluirTodos').click(function(){
                    setTimeout(function(){
                        selectItens($(this), carros)
                        $(".mo-check-pedidos").trigger('click');
                    }, 500);
                })
                setTimeout(function(){
                    $(".mo-check-pedidos").trigger('click');
                    $(".mo-check-veiculos").trigger('click');
                }, 500);

                // $('#modalLarge #incluirTodos').trigger('click');

            }
        }).submit();
    }
})

function selectItens(thad, carros) {
    var itens = $('#modalLarge .check-pedido:checked');
    var itemCub = 0;
    var itemKg = 0;
    $(itens).each(function(i){
        item = $(itens[i]).parent().siblings();
        itemCub = parseFloat($(item[3]).html()) + itemCub;
        itemKg = parseFloat($(item[4]).html()) + itemKg;
    })

    tratamentoErrosCarros(carros, itemCub, itemKg);
    changeCargas()

}

function changeCargas(){
    var itens = $('#modalLarge .modal-body #tableRListarVeiculos .check-veiculos');
    $(itens).each(function(i){
        $(itens[i]).parents('tr').addClass($(itens[i]).data('class'));
    })
}

function tratamentoErrosCarros(carros, itemCub = 0, itemKg = 0){
    var trCarros
    var itemKg = parseFloat(itemKg);
    var itemCub = parseFloat(itemCub);
    var dataSet = []
    for(j in carros) {
        legendaCor = '';
        var centoCarga = (((parseFloat(carros[j].ropeso) + itemKg) * 100) / parseFloat(carros[j].vemaxpeso)).toFixed(2);
        var disp = (parseFloat(carros[j].ropeso) + itemKg).toFixed(2);
        var cubDisp = (parseFloat(carros[j].vecubagem) + itemCub).toFixed(2)
        var cubCarga = (((parseFloat(carros[j].vecubagem) + itemCub) * 100) / parseFloat(carros[j].vecubagem)).toFixed(2);
        var corCarga = '';
        if(centoCarga >= 90 && centoCarga <= 100 || cubCarga >= 90 && cubCarga <= 100) {
            corCarga = 'verde';
            legendaCor = 'Mais de 90% carregado.'
        }else if(centoCarga < 90 && centoCarga >= 70 || cubCarga < 90 && cubCarga >= 70) {
            corCarga = 'laranja';
            legendaCor = 'Mais de 70% carregado.'
        }else if(centoCarga > 100 || cubCarga > 100) {
            corCarga = 'vermelho';
            legendaCor = 'Excesso de carga.'
        }else {
            corCarga = 'cinza';
            legendaCor = 'Menos de 70% carregado.'
        }
        var tr = ''
        var data = []
        tr += '<input class="check-veiculos carro'+j+'" title="Porcentagem diponível" id="carro'+carros[j].veplaca+'" data-title="'+legendaCor+'" data-placa="#carro'+carros[j].veplaca+'" data-class="'+corCarga+'" name="veiculos" type="radio" value="'+carros[i].veplaca+'">'
        data.push(tr);
        data.push(carros[j].veplaca);
        data.push(carros[j].veprefixo);
        data.push(disp+'/'+carros[j].vemaxpeso);
        data.push(cubDisp+'/'+carros[j].vecubagem);
        data.push(carros[j].vemaxentregas);

        dataSet.push(data)
    }

    var checado = ""+$('#modalLarge .modal-body #tableRListarVeiculos .check-veiculos:checked').data('placa')+"";


    if ($.fn.DataTable.isDataTable('#modalLarge .modal-body #tableRListarVeiculos')) {
        $('#modalLarge .modal-body #tableRListarVeiculos').DataTable().destroy();
    }
    tableP =  $('#modalLarge .modal-body #tableRListarVeiculos').DataTable({
        paging: false,
        retrieve: true,
        scrollY:  "200px",
        scrollCollapse: true,
        language: traducao,
        data: dataSet,
    });

    $(checado).click();
    var veicul = $('.check-veiculos');
    $(veicul).each(function(i){
        console.log($(veicul[i]).data('title'))
        $($(veicul[i]).data('placa')).parents('tr').attr('title', $(veicul[i]).data('title'))
    })

}


$(document).on('click','.excluir-pedido',function(){
    var url = $(this).data('url');
    var id = $(this).data('id');
    var bts = '';
    bts += '<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>';
    bts += '<button type="button" value="'+url+'" data-dismiss="modal" class="btn btn-primary  bt-modal-desable">Salvar alterações</button>';

    $("#modalDelataDesativa .modal-body").html('Deseja deletar o pedido?')
    $("#modalDelataDesativa .modal-footer").html(bts)
    $("#modalDelataDesativa .modal-title").html("Deletar")

    $('.bt-modal-desable').click(function(e){
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: url,
            headers: { 'X-CSRF-TOKEN': $("#token").attr('value')},
            data: {
                id_item_rota:id,
            },
            dataType: "json",
            'success': function (dados) {
                if(dados.status){
                    $('#carregarParametrosRoterizacao').trigger('click');
                }else{
                    //TODO exibir q deu erro
                }
            }
        });

    })
});

$(document).on('click', '#incluirTodos', function(){
    if($(this).is(':checked')){
        $('.check-pedido').prop('checked', true);
        $('.ped-vol').html(Number(totalVol).formatMoney(2, "", ".", ","));
        $('.ped-cub').html(Number(totalCub).formatMoney(2, "", ".", ","));
        $('.ped-kg').html(Number(totalkg).formatMoney(2, "", ".", ","));
        $('.ped-val').html(totalval);

    }else{
        $('.check-pedido').prop('checked', false);
        $('.ped-vol').html('0,00');
        $('.ped-cub').html('0,00');
        $('.ped-kg').html('0,00');
        $('.ped-val').html('R$0,00');
    }
})

$(document).on('click', '#incluirTodosVeiculos', function(){
    if($(this).is(':checked')){
        $('.check-veiculo').prop('checked', true);
        $('.veic-capacidade').html(Number(totalCap).formatMoney(2, "", ".", ","));
        $('.veic-cubagem').html(Number(totalVCub).formatMoney(2, "", ".", ","));
        $('.veic-max-entregas').html(Number(totalMaxC).formatMoney(2, "", ".", ","));
    }else{
        $('.check-veiculo').prop('checked', false);
        $('.veic-capacidade').html('0.00');
        $('.veic-cubagem').html('0.00');
        $('.veic-max-entregas').html('0.00');
    }
})


// $(document).on('change','#prDataSaida, #dtSaidaEdited',function(){
//     moment.locale('pt-br');

//     var v1 = moment(moment($(this).val(),"DD/MM/YYYY").valueOf());
//     var v2 = moment(moment().format("DD/MM/YYYY"),"DD/MM/YYYY").valueOf();

//     if(v1.isSameOrBefore(v2)){
//         $(this).val(moment().format("DD/MM/YYYY"));
//     }

// });


$(document).on('click', '#remontarCargas', function(e){
    e.preventDefault();
    var Pfim = $(".ponto-inicio option:selected");
    var Pinicio = $(".ponto-fim option:selected");
    var laloFim = {'polatitude':$(Pfim).data('lat'), 'polongitude': $(Pfim).data('lng')};
    var laloInicio = {'polatitude':$(Pinicio).data('lat'), 'polongitude': $(Pinicio).data('lng')};
    var inicio = $(Pinicio).val();
    var fim = $(Pfim).val();

    var form = $(this).parents('form');
    var pedidos = $(this).parents('#modalLarge').find('.check-pedido:checked');
    var cli = $(".hidCli").val();

    var veiculos = $(".check-veiculos:checked");

    var itens= []
    if($(pedidos).length == 0) {
        $(".msg-remonta-carga").html('<strong>Selecione pelo menos um ponto para roteirizar!</strong>');
        return;
    }
    if(veiculos.length == 0) {
        $(".msg-remonta-carga").html('<strong>Selecione um veículo para roteirizar!</strong>');
        return;
    }
    $(pedidos).each(function(i){
        itens.push($(pedidos[i]).val());
    })

    $.post(ROOT+'/painel/roteirizador/rota/manual/itens/rota',
        {
            laloFim:laloFim,
            laloInicio:laloInicio,
            itens:itens,
            placa:$(veiculos).val(),
            cli:cli,
            fim:fim,
            inicio:inicio
        },
        function(data){
            $(pedidos).parents('tr').remove();
            var it = $('#modalLarge .check-pedido');
            if(it.length == 0) {
                window.location.href = ROOT+"/painel/roteirizador/acompanhamento";
            }
        }
    )
})
