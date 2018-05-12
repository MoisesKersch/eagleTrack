//*********VARIAVEIS****************************************
var novoPonto;
var novoPontoRaio;
var rotaTemp ;
var rotaCorrigidaTemp ;
var rotaDestacada;
var rotaInicio;
var veiculo = [];//veiculo somente lat, lng e direcao (faz o carro andar)
var todasPermissoes = []; //permissoes de acesso aos veiculos
var veiculoM = [];//veiculoM informações iniciais do veiculo e motorista
var markerPositionCorrigido = [];
var pontos = [];
var pontosRaios = [];
var requisicaoListaPosicoes = null;
var requisicaoRotas = null;
var requisicaoRastroCorrigido = null;
var requisicaoParadas = null;
var requisicaoExcessosVelocidades = null;
var requisicaoPortas = null;
var raioParada = null;
var tempoParadaGlobal = 300;
var excessoVelocidadeGlobal = 80;
var portaGlobal = 14;
var tempoRefresh;
var intervaloAtualizacao;
var position;
var coleta, referencia, entrega = false;
var $flagDadosVeiculosPC = 0;
var posicoes = '';
var motoristaSelecionado = '';
var campoMotoristaSelecionado = '';
var imputSlectMA; //booleano para motorista ou ajudante
var isMotoristaPainel; //booleano para motorista ou ajudante
var impOldSelectedMotorista; // 0,1 posicao do motorista original no select
var impOldSelectedAjudante; // 0,1 posicao do ajudante original no select
var thad;
var tablePainelControle;
var table_cont = 0;
var hidMarker = new Object();
var newMarVei = [];
var veiclClusters = [];
var oldVei = new Object();
var getPolilyne;
var rotasList = new Array();
var listaRotasMarcada = new Set();
var listaCheck = new Set()
var setRotasMarker = new Set();
var setRocodigo = new Set();
var mocodigo = 0;
var isRequest = true;
var isFirstAjax = true;
var dataSet = new Set();
var rotas,painel;
var firstVezes = true;
var firstData = true;
var veiculosAll = [];
var isRotas = false;
var isPainel = false;
var moduloIndex;
var agrupaMarkers = 1;

$('.select-empresa-painel').select2({
    language: "pt-BR",
    dropdownParent: $('#painelControleTabela')
});


// $('.data-data').datepicker({format:'dd/mm/yyyy',language: 'pt-BR'});

/*******************************************************************************
*****************FUNCAO INICIAL PARA INICIALIZACAO DO MAPA**********************
********************************************************************************/
$(document).ready(function() {
    var carregar = $("#pageHome");
    carregar.each(function(idx, element){
        carregaMapa();
    })
//veiculoMultEmpresa();
// carregaMarkersVeiculos();

$('.select-empresa-painel').trigger('change');
tempoRefresh = '20000';
refreshAtualizaVeiculos(tempoRefresh);

$(document).on('click', '.markerVeiculo', function() {
    var id = $(this).attr('id');
    fechaOpcoesVeiculo()
    mostraOpcoesVeiculo(id);
    removeRotas()
    limpaVariaveisRequisicao();
    tempoParadaGlobal = 300;
    excessoVelocidadeGlobal = 80;
    portaGlobal = 14;
    $('<div class="divIcon"></div>Velocidade').remove();
    $('.divIconParada').remove();
    $('.divIconPorta').remove();
    mapa.closePopup();
});


    // function carregaMarkersVeiculos(){
    //     $.ajax({
    //         // url: ROOT+'/veiculos/maps/carregarMarkers',
    //         url: ROOT+'/veiculos/maps/atualizarMarkers',
    //         type: 'post',
    //         dataType: 'json',
    //         data: {'modulo' : 0},
    //         success: function(retorno){
    //             veiculo = JSON.parse(JSON.stringify(retorno));
    //           for(var y in retorno){
    //               var icone = preparaIcon(retorno[y].tipo, retorno[y].direcao, retorno[y].prefixo,y,retorno[y].ignicao);
    //               veiculoM[y] = new L.marker([retorno[y].lat,retorno[y].lng],{
    //                                                       icon: icone
    //                                                   })
    //                                                   .addTo(mapa);

    //           }//fim for in
    //         }
    //     });//fim ajax
    // }


    /*******************************************************************************
    ******************FUNCTION FECHA OPCOES VEICULOS********************************
    *******************************************************************************/
    $(document).on('click', '#fechaOpcoesVeiculo', function() {
        //restarta refresh de atualizacao de posicao veiculos
        //refreshAtualizaVeiculos(tempoRefresh);
        //oculta div
        removeRotas()
        limpaVariaveisRequisicao();
        mapa.closePopup();
        $("#divOpcoesVeiculo").hide('slow');
        $('.divIconVelocidade').remove();
        $('.divIconParada').remove();
        $('.divIconPorta').remove();
    });

$('#checkboxVisualizaColeta').change(function(){
    if(this.checked){
        coleta = true;
        busca_pontos_inicial();
    }else{
        coleta = false;
        busca_pontos_inicial();
    }

});

$('#checkboxVisualizaEntrega').change(function(){
    if(this.checked){
        entrega = true;
        busca_pontos_inicial();
    }else{
        entrega = false;
        busca_pontos_inicial();
    }
});

$('#checkboxVisualizaReferencia').change(function(){
    if(this.checked) {
        referencia = true;
        busca_pontos_inicial();
    }else{
        referencia = false;
        busca_pontos_inicial();
    }
});

$('#checkboxVisualizaRegioes').change(function(){
    $('.regiaoNoMapaRemove').remove();
    if(this.checked) {
        buscaRegioes();
    }else{
        mapa.closePopup();
    }
});

painelControleMotoristas();

setInterval(function(){
    if ($flagDadosVeiculosPC != 1) {
        dadosVeiculosPC();
    } else {
        refreshPC();
    }
}, 22000);
});

/*******************************************************************************
************************ Painel de controle ************************************
*******************************************************************************/
function painelControleMotoristas(){
    var html = '';

// html +=
// '<table id="tabelaPainelControle" class="table table-striped">'+
//     '<thead>'+
//         '<tr id="tablehide">'+
//             '<th><input id="checkboxTbPc" type="checkbox" checked> Localizar</th>'+
//             '<th>Placa</th>'+
//             '<th>Prefixo</th>'+
//             '<th>Descrição</th>'+
//             '<th>Ignição</th>'+
//             '<th>Ult. Posição</th>'+
//             '<th>Próximo</th>'+
//             '<th>Alertas</th>'+
//             '<th class="text-center" >Motorista | Ajudante</th>'+
//         '</tr>'+
//     '</thead>'+
//     '<tbody>'+
//     '</tbody>'+
// '</table>';

// $('#painelControleTabela').html(html);

//set de tempo para carregar a variavel veiculos
setTimeout(function() {
    dadosVeiculosPC();
}, 1000);
};

function returnAJudanteMotorista(ajudante,motorista){
    var ajudMot = ''
    var ajudanteSize = ajudante.length
    var motoristaSize = motorista.length
    if(motoristaSize > 13 && motorista != "Sem Motorista"){
        ajudMot = motorista.substring(0,10)+'...'
    }else if(motorista == "Sem Motorista"){
        ajudMot = 'Sem Motorista'
    }else{
        ajudMot = motorista
    }

    if(ajudanteSize > 13 && ajudante != "Sem Ajudante"){
        ajudMot+=' | '+ajudante.substring(0,10)+'...'
    }else if(ajudante == "Sem Ajudante"){
        ajudMot+=' | Sem Ajudante'
    }else{
        ajudMot+= ajudante
    }

    return ajudMot;
}

function dadosVeiculosPC(arrayClientes, todasPermissoes) {
    var html = '';
    if ($.fn.DataTable.isDataTable('#tabelaPainelControle')){
        $('#tabelaPainelControle').DataTable().destroy();
    }
    $(document).ready(function(){


        if (veiculo != 0 && veiculo != undefined) {
            $flagDadosVeiculosPC = 1;
            for(var vm in veiculo){
                var bloqueio = '';
                var titulo = '';
                var panico = '';
                var movimentoIndevido = '';

                if (veiculo[vm].ultimobloqueio == 3) {
                    var bloqueio = 'fa fa-lock text-danger';
                    var titulo = 'Este veículo está bloqueado';
                }

                if (veiculo[vm].ultimopanico)
                    panico += '<span class="text-danger fa fa-podcast" title="Pânico ativado!"></span>';

                if (veiculo[vm].moultimomotivotransmissao == 70)
                    movimentoIndevido += '<span class="text-danger fa fa-share-square-o" title="Movimento indevido!"></span>';

                var mot = (veiculo[vm].motorista ? veiculo[vm].motorista : 'Sem Motorista')
                var aju = (veiculo[vm].ajudante ? veiculo[vm].ajudante : 'Sem Ajudante')
                var desc = mot+' | '+aju;
                var descCustom = returnAJudanteMotorista(aju,mot)
                var alteraMot = '';

                if(typeof todasPermissoes != 'undefined' && todasPermissoes.ppeditar) {
                    alteraMot = '<div title="Clique aqui para editar motorista e/ou ajudante" class="imput-mot-aju-painel fa fa-edit"  data-toggle="modal" \
                                data-target="#modalClean" data-cliente="' + veiculo[vm].clcodigo + '" \
                                data-mot="' + veiculo[vm].motoristaId + '" \
                                data-ajudante="'+ veiculo[vm].ajudante +'"\
                                data-ajudante-id="'+ veiculo[vm].ajudanteId +'"\
                                data-veiculo="' + veiculo[vm].veiculoId + '" \
                                data-vedescricao="' + veiculo[vm].descricao + '" \
                                data-mo = "'+ vm +'" \
                                data-mot-desc = "'+ veiculo[vm].motorista +'" \
                                data-modulo = "'+ veiculo[vm].modulo +'"  ></div>'
                            '</td>'
                }
                html +=
                '<tr class="ignicao'+veiculo[vm].ignicao+'">'+
                // '<td>'+
                //     // '<span><input data-modulo="'+veiculo[vm].modulo+'" class="checkboxTbPc" type="checkbox" checked></span>'+
                // '</td>'+
                '<td class="' + veiculo[vm].modulo + '" style="position: relative;">'+
                        '<span><input data-modulo="'+veiculo[vm].modulo+'" class="checkboxTbPc" type="checkbox" checked></span>'+
                        '<span id="crosshair'+veiculo[vm].modulo+'" style="cursor:crosshair;" class="glyphicon glyphicon-screenshot localizar-veiculo" onclick="mapa.flyTo([' + veiculo[vm].lat + ', ' + veiculo[vm].lng + '], 20);"></span>'+
                    '</td>'+
                    '<td title="Módulo: '+veiculo[vm].modulo+'">' + veiculo[vm].placa + '</td>'+
                    '<td>' + veiculo[vm].prefixo + '</td>'+
                    '<td title="'+veiculo[vm].descricao+'"> ' + (veiculo[vm].descricao ? veiculo[vm].descricao : '') + '</td>'+
                    '<td class="' + veiculo[vm].modulo + '">'+
                        '<span style="margin-left: 15px;"' + (veiculo[vm].ignicao == 0 ? 'title="Desligado" class="glyphicon glyphicon-remove-sign"' : 'title="Ligado" class="glyphicon glyphicon-ok-sign"') + '></span>' +
                    '</td>'+
                    '<td class="' + veiculo[vm].modulo + '">' + (veiculo[vm].dataEvento != null ? moment(moment(veiculo[vm].dataEvento, 'DD/MM HH:mm', 'pt-br')).format('DD/MM HH:mm') : '') + '</td>'+
                    '<td title="'+veiculo[vm].moultimareferencia+'">' +veiculo[vm].moultimareferencia + '</td>'+
                    '<td class="' + veiculo[vm].modulo+ '">'+
                        '<span style="margin-right: 7px;" title="'+titulo+'" class="'+bloqueio+'"></span>'+
                        panico+
                        movimentoIndevido+
                    '</td>'+
                        '<td class="text-center ' + veiculo[vm].modulo + '" title="'+desc+'">'+//aquimot
                        descCustom+
                        alteraMot+
                '</tr>';
            }

            $('#painelControleTabela').css('padding-top', '5px');
            $('#tabelaPainelControle thead').show();
        } else {
            html =
            '<tr>' +
            '<td colspan="9">' +
            '<div class="alert alert-warning" role="alert" id="alertaPainelControle">'+
            'Nenhum veículo encontrado!'+
            '</div>' +
            '</td>' +
            '</tr>';
            $('#tabelaPainelControle tbody').html(html);

            $('#painelControleTabela').css('padding-top', '35px');
            $('#tabelaPainelControle thead input').css('height', 'inherit');
            $('#tabelaPainelControle thead').show();
        }

        $('#tabelaPainelControle tbody').html(html);

        $('.filtroFerramentasPC').click(function() {
            if($(this).is(':checked')){
                $('#ipFerramentaArea').prop('checked', false)
                $('#ipFerramentaDistancia').prop('checked', false)
                $('#ipFerramentaRota').prop('checked', false)
                $(this).prop('checked', true);
            }
        // if ($('#ipFerramentaArea').prop('checked') &&
        //         $('#ipFerramentaDistancia').prop('checked') &&
        //         $('#ipFerramentaRota').prop('checked')) {
        //     $('.op0').show();
        //     $('.op1').show();
        //     $('.op2').show();

        //     $('#ipFerramentaArea').prop('checked', false)
        //     $('#ipFerramentaDistancia').prop('checked', false)
        //     $('#ipFerramentaRota').prop('checked', false)
        //     $(this).prop('checked', true);
        //     $(this).trigger('change');
        //     return;
        // }
});


//funcao filtro ignicao
$('.filtroIgnicaoPC').change(function() {
    if ($('#iptIgnicaoLig').prop('checked') && $('#iptIgnicaoDeslg').prop('checked')) {
        $('.ignicao0').show();
        $('.ignicao1').show();

        $('#iptIgnicaoLig').prop('checked', false)
        $('#iptIgnicaoDeslg').prop('checked', false)
        $(this).prop('checked', true);
        $(this).trigger('change');
        return;
    }
    if ($('#iptIgnicaoLig').prop('checked') && !$('#iptIgnicaoDeslg').prop('checked')) {
        $('.ignicao0').hide();
        $('.ignicao1').show();
        return;
    }
    if ($('#iptIgnicaoDeslg').prop('checked') && !$('#iptIgnicaoLig').prop('checked')) {
        $('.ignicao1').hide();
        $('.ignicao0').show();
        return;
    }
    if (!$('#iptIgnicaoLig').prop('checked') && !$('#iptIgnicaoDeslg').prop('checked')) {
        $('.ignicao0').show();
        $('.ignicao1').show();
        return;
    }
});

// if (typeof arrayClientes != 'undefined' && arrayClientes.length > 0) {
//     carregaMotoristas(arrayClientes);
// } else {
//     carregaMotoristas($('.select-empresa-painel').val());
// }

$('.motorista-painel-controle').select2({
    "language": "pt-BR",
    allowClear: false
});

//MODAL para gerenciar motoristas e ajudantes aos veículos
$('.imput-mot-aju-painel').on('click', function () {
    var bts = '';

//configuração do modal.. titulo e botoes
$("#modalClean .modal-title").html('<div>Alteração de Motorista | Ajudante do Veículo '+ $(this).data('vedescricao')+'</div>' );

// bts += '<button type="button" class="mot-invisible btn btn-success save-alteracao-mot-aju" data-dismiss="modal">Salvar <span class="glyphicon glyphicon-ok"></span></button>';
bts += '<button type="button" class="mot-invisible btn btn-success save-alteracao-mot-aju" >Salvar <span class="glyphicon glyphicon-ok"></span></button>';
bts += '<button type="button" class="mot-invisible btn btn-danger" data-dismiss="modal">Cancelar <span class="glyphicon glyphicon-remove"></span></button>';
$("#modalClean .modal-footer").html(bts);

thad = $(this);

$("#modalClean .modal-body").addClass('modal-select-motoristas');
var setMot = '<div class="col-md-6 td-motorista-painel-controle" data-cliente="' + thad.data('cliente') + '" data-mot="' + thad.data('mot') + '">'+
'Motorista:<select data-m="' + thad.data('mot') + '" data-veiculo="' + thad.data('veiculo') + '" data-mo="' + thad.data('mo') + ' "class="form-control select-mot-painel select-painel-motorista '+ thad.data('modulo') + '">';
setMot += '<option value=" ">Sem motorista</option>';
impOldSelectedMotorista = 0;

if (Number.isInteger(thad.data('mot'))) {
    impOldSelectedMotorista = 1;
    setMot += '<option selected value="' + thad.data('mot') + '">' + (thad.data('mot-desc') ? thad.data('mot-desc') : '') + '</option>';
}

setMot += '</select>'+
'</div>';
var setAju = '<div class=" col-md-6 td-motorista-painel-controle" data-cliente="' + thad.data('cliente') + '" data-mot="' + thad.data('mot') + '" data-aju="' + thad.data('ajudante-id') + '">'+
'Ajudante: <select data-m="' + thad.data('ajudante-id') + '" data-veiculo="' + thad.data('veiculo') + '" data-mo="' + thad.data('mo') + ' "class="form-control select-aju-painel select-painel-ajudante ' + thad.data('modulo') + '">';
setAju += '<option  value=" ">Sem Ajudante</option>';
impOldSelectedAjudante = 0;

if(Number.isInteger(thad.data('ajudante-id'))) {
    impOldSelectedAjudante = 1;
    setAju += '<option selected value="' + thad.data('ajudante-id') + '">' + (thad.data('ajudante') ? thad.data('ajudante') : '') + '</option>';
}

setAju += '</select>'+
'</div>';

$("#modalClean .modal-body").html('<div class="notification col-sm-12">'+setMot+setAju+'</div>');
$("#modalClean .modal-body .notification").append('<div class="col-sm-12 mot-notfication"></div>');
$("#modalClean .modal-body .notification").append('<div class="col-sm-12 aju-notfication"></div>');

//Replicado pois esses campos são adicionados depois de transformar os select em select2;
$('select').select2({
    "language": "pt-BR",
    allowClear: true
});

//carregar lista de motoristas e ajudantes;
if (typeof arrayClientes != 'undefined' && arrayClientes.length > 0) {
    carregaMotoristas(arrayClientes);
} else {
    carregaMotoristas($('.select-empresa-painel').val());
}

$('.select-painel-motorista').on('select2:select',function(){
// ver se está selecionado no outro imput

if($(this).val() != ' ' && $(this).val() == $('.select-painel-ajudante').val()){
// $('.select-painel-ajudante option').attr('selected',false);
$('.select-painel-ajudante option').removeAttr('selected');
$('.select-painel-ajudante option:first').attr('selected','selected');

$('select').select2({
    "language": "pt-BR",
    allowClear: true
});
}

$("#modalClean .modal-footer").html(bts);

//checkar se esta relacionado a outro veículo
$("#modalClean .modal-body .mot-notfication").html('');
if($(this)[0].selectedIndex != (impOldSelectedMotorista)){
    imputSlectMA = true;
    checkDisponibilidadeMA($(this).val(), $('.select-painel-motorista option:selected').text());
}
});

$('.select-painel-ajudante').on('select2:select',function(){
// ver se está selecionado no outro imput

if($(this).val() != ' ' && $(this).val() == $('.select-painel-motorista').val()){
// $('.select-painel-motorista option').attr('selected',false);
$('.select-painel-motorista option').removeAttr('selected');
$('.select-painel-motorista option:first').attr('selected','selected');

$('select').select2({
    "language": "pt-BR",
    allowClear: true
});
}

$("#modalClean .modal-footer").html(bts);

//checkar se esta relacionado a outro veículo
$("#modalClean .modal-body .aju-notfication").html('');
if($(this)[0].selectedIndex != (impOldSelectedAjudante)){
    imputSlectMA = false;
    checkDisponibilidadeMA($(this).val(), $('.select-painel-ajudante option:selected').text());
}


});
});

datatableForPainelControle();
$('.filtroIgnicaoPC').trigger('change');
});
}

function isChecked(element){
    if(element.prop('checked')){
        return true;
    }else{
        return false;
    }
}

$(document).on('click','.save-alteracao-mot-aju',function(){
    var m = $('.select-mot-painel').val();
    var a = $('.select-aju-painel').val();
    $(this).find('span').remove();
    $(this).append(`<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i><span class="sr-only"></span>`);
    $(this).prop('disabled', true);
// var v = veiculo[$(this).data('mo')].veiculoId;
var v = thad.data('veiculo');
$.ajax({
    url:ROOT+'/veiculos/maps/atualizarMotorista',
    type: 'post',
    dataType: 'json',
    data:{'veiculoId': v, 'motoristaId': m, 'ajudanteId': a},
    success: function(retorno){
        $('.select-empresa-painel').trigger('change');
        $('#modalClean').modal('toggle');
    }
});
});

//atualiza o localizar/ignicao/ult posicao e motorista no painel de controle
function refreshPC() {
    for (var v in veiculo) {
        var bloqueio = '';
        var panico = '';
        var movimentoIndevido = '';

        if(veiculo[v].ultimobloqueio == 3)
            bloqueio += '<span style="margin-right: 7px;" title="Este veículo está bloqueado" class="fa fa-lock text-danger"></span>';

        if (veiculo[v].ultimopanico)
            panico += '<span class="text-danger fa fa-podcast" title="Pânico ativado!"></span>';

        if (veiculo[v].moultimomotivotransmissao == 70){
            movimentoIndevido += '<span class="text-danger fa fa-share-square-o" title="Movimento indevido!"></span>';
        }

        $($('.' + v)[0]).parent().removeClass('ignicao1').removeClass('ignicao0').addClass((veiculo[v].ignicao ? 'ignicao1' : 'ignicao0'));

        $($('.' + v)[0]).find('.glyphicon-screenshot').attr("onclick","mapa.flyTo([" + veiculo[v].lat + "," + veiculo[v].lng + "], 20);");

        $($('.' + v)[1]).html('<span style="margin-left: 15px;"' + (veiculo[v].ignicao ? 'title="Ligado" class="glyphicon glyphicon-ok-sign"' : 'title="Desligado" class="glyphicon glyphicon-remove-sign"') + '></span>');

        $($('.' + v)[2]).html(veiculo[v].dataEvento ? moment(moment(veiculo[v].dataEvento, 'DD/MM HH:mm', 'pt-br')).format('DD/MM HH:mm') : '');

        $($('.' + v)[3]).html(bloqueio+panico+movimentoIndevido);

if ($($('.' + v)[4]).children().data('mot') != veiculo[v].motoristaId || $($('.' + v)[4]).children().data('ajudante-id') != veiculo[v].ajudanteId) {//aquimot
    var mot = (veiculo[v].motorista ? veiculo[v].motorista : 'Sem Motorista')
    var aju = (veiculo[v].ajudante ? veiculo[v].ajudante : 'Sem Ajudante')
    var desc = mot+' | '+aju;

    $($('.' + v)[4]).html(desc+
        '<div title="Clique aqui para editar motorista e/ou ajudante" class="imput-mot-aju-painel fa fa-edit"  data-toggle="modal" \
        data-target="#modalClean" data-cliente="' + veiculo[v].clcodigo + '" \
        data-mot="' + veiculo[v].motoristaId + '" \
        data-ajudante="'+ veiculo[v].ajudante +'"\
        data-ajudante-id="'+ veiculo[v].ajudanteId +'"\
        data-veiculo="' + veiculo[v].veiculoId + '" \
        data-vedescricao="' + veiculo[v].descricao + '" \
        data-mo = "'+ v +'" \
        data-mot-desc = "'+ veiculo[v].motorista +'" \
        data-modulo = "'+ veiculo[v].modulo +'"  >'+
        '</div>');

}

$('.checkboxTbPc').each(function(index, element) {
    if ($(element).data('modulo') == v && !$(element).prop('checked')) {
        $($('.' + v)[0]).prepend('<div class="bloqueiaLatLngGoTo"></div>');
    } else {
        $($('.' + v)[0]).find('bloqueiaLatLngGoTo').remove();
    }
});
}

$('.filtroIgnicaoPC').trigger('change');
}

function ajaxGetRotas(){
    var scrollPos = $(".dataTables_scrollBody").scrollTop()
        dataSet = new Array();
        listaCheck = new Set()
        $('.checkView').each(function(check){
            if($(this).is(':checked')){
                 listaCheck.add($(this).data('rocodigo'))
            }
        })
      //  console.log($('.rotas-empresas-select-painel').val())
        $.ajax({
            type : 'post',
            dataType : 'json',
            data : {empresas : $('.rotas-empresas-select-painel').val(), date : dateSelect},
            url : ROOT+'/rotas/getAllRotas',
            success : function(data){
                var rotas = data.response;
                rotasList = rotas;
                var cont = 0;
                console.log('resposta ',data);
                mocodigo = rotas
                var table = $('#tableRotas').DataTable();
                table.clear().draw();

                if ($.fn.DataTable.isDataTable('#tableRotas')){
                    $('#tableRotas').DataTable().destroy();
                }
                updateInfoTable(rotas)

            $('#tableRotas').DataTable({
                paging: false,
                responsive: true,
                dom: 'Bfrtip',
                data: dataSet,
                info: false,
                scrollY: 200,
                scrollCollapse: true,
                processing: true,
                stateSave: true,
                destroy: true,
                autoWidth: false,
                aoColumnDefs: [{
                        'bSortable': false,
                        'aTargets': [ 0, 4, 8]
                    },{
                        'targets' : [9]
                        ,'visible' : false
                    }],
                order: [[ 1, "asc" ]],
                language: {
                    search: "Buscar:",
                    searchPlaceholder: "Buscar na tabela",
                    sZeroRecords: "Nada encontrado"
                },
                createdRow: function(row, data, dataindex){
                    // if()
                        $(row).css('background', hexToRgbA(data[9],0.5));
                }
            });

            $('.checkView').click(function(){
                var thad = $(this);
                var rocodigo = $(this).data('rocodigo')
                var cor = $(this).data('rocor')
                if($(this).is(':checked')){
                    var lista = getItensRotas(rocodigo)
                    var latLong = new Array()
                    var ponto_saida = rotas[0]['ponto_saida'];
                    var ponto_retorno = rotas[0]['ponto_retorno'];
                    latLong.push({"polatitude":ponto_saida['polatitude'],"polongitude":ponto_saida['polongitude']})
                    for(i in lista){
                        var pontos = lista[i]['ponto']
                        latLong.push({"polatitude":pontos['polatitude'],"polongitude":pontos['polongitude']})
                    }
                    latLong.push({"polatitude":ponto_retorno['polatitude'],"polongitude":ponto_retorno['polongitude']})
                    $(this).prop('disabled',true);
                    createPoliLyne(latLong,rocodigo,cor,thad);
                    $(document).on('click','.markerRota',function(){
                    console.log($(this).data('id-marker'));
                    })
                    showVeiculosRota()
                }else{
                    var excluir = null;
                    listaRotasMarcada.forEach(function(value){
                        if(value.rocodigo == rocodigo){
                            mapa.removeLayer(value.polyline)
                            excluir = value
                        }
                    })
                    listaRotasMarcada.delete(excluir)
                    removeMarkerForRotas(rocodigo)
                   // hideVeiculosRota($(thad).data('modulo'))
                    removeClusterMarkers()
                }

                var veiculo = null;
                removeClusterMarkers()
                var id = $('.rotas-empresas-select-painel').val()
                var lista = new Array()

                $.post(ROOT+'/veiculos/maps/atualiza/painel',{id:id,getall: true}, function(dados){
                    if(Object.keys(veiclClusters).length > 0) {
                        removeClusterMarkers()
                    }
                    veiculo = dados.modulos
                    $('.checkView:checked').each(function(index,element){
                        var modulo = $(element).data('modulo')
                        for(i in veiculo){
                           if(veiculo[i].modulo == modulo){
                                lista.push(veiculo[i])
                           }
                        }
                    })

                    addVeiculosChangePainel(lista)
                    dadosVeiculosPC(id);
                    mapa.closePopup();
                })
            })

            $(".dataTables_scrollBody").scrollTop(scrollPos)

              $('.filto-status-rota-I').parents('tr').hide();
                if(isChecked($('#checkIniciado'))){
                    $('.filto-status-rota-I').parents('tr').show();
                }

                $('.filto-status-rota-P').parents('tr').hide();
                if(isChecked($('#checkPendente'))){
                    $('.filto-status-rota-P').parents('tr').show();
                }

                $('.filto-status-rota-F').parents('tr').hide();
                if(isChecked($('#checkFinalizado'))){
                    $('.filto-status-rota-F').parents('tr').show();
                }
            },
            error : function(data){}
        })
}

function getItensRotas(rocodigo){
    var cont = 0;
    var indice = 0;
    console.log('antes ',rotasList)
    for(i in rotasList){
        if(rotasList[i]['rocodigo'] == rocodigo){
            cont++
            if(cont == 1){
                indice = i;
                break;
            }
        }
    }
    return rotasList[indice]['itens_rota'];
}

function createMarkerRotas(idRota,cor,ordem,latLng,item_rota,isgreenorred){
    var circlehtml = ''
    var isColor = true;
    if(item_rota.irstatus == 'F' || item_rota.irstatus == 'P'){
        circlehtml = '<span class="circle circle-rotas-'+idRota+' circle-itensrota-'+item_rota.ircodigo+' markerRota data-id-marker="'+ordem+'">  <i class="fa fa-check rotascheckgreen-'+item_rota.ircodigo+'" /> </span>'
    }else{
        circlehtml = '<span class="circle circle-rotas-'+idRota+' circle-itensrota-'+item_rota.ircodigo+' markerRota"> '+ordem+' </span>'
    }

    var icon = L.divIcon({
        className: 'fa fa-map-marker markerRota rota-'+item_rota.ircodigo,
        html: circlehtml,
        iconAnchor:   [16, 30],
        popupAnchor: [0, -30]
    });

    var createHtmlPopup = ''
    if(item_rota.irstatus == 'F'){
        var data = '00:00'
        var datatempoparadao = '00:00:00'
        if(item_rota.irdata_hora_evento != null && item_rota != undefined){
            data = item_rota.irdata_hora_evento.substring(11,16);
        }

        if(item_rota.irtempoparado != null && item_rota.irtempoparado != undefined){
            datatempoparadao = moment(item_rota.irtempoparado).format('hh:mm:ss')
        }
        createHtmlPopup = `<div> <h5 class="titlerotas-marker">`+item_rota.irnome+`</h5>
        <div class="popup-rotas">
        Realizado: `+data+`</br>
        Tempo parado: `+datatempoparadao+`</br>
        Volumes: `+item_rota.irqtde+`</div> </div>`
    }else if(item_rota.irstatus == 'P'){
        createHtmlPopup = `<div> <h5 class="popup-rotas">`+item_rota.irnome+`</h5>
        <h6 class="popup-rotas-infoempty">O veículo não passou por esse ponto</h6> </div>`
    }else{
        //caso for diferente de pulada e finalizada,porém não foi implementado ainda
         createHtmlPopup = `<div> Sem informação </div>`
    }

    var marker = L.marker([latLng.polatitude, latLng.polongitude], {icon: icon})
    .bindPopup(createHtmlPopup)
    .addTo(mapa);

    if(isgreenorred){
       $('.rota-'+item_rota.ircodigo).css('color',''+cor+'');
       $('.circle-itensrota-'+item_rota.ircodigo).css('border','2px solid '+cor+'')
    }else{
       if(item_rota.irstatus == 'F'){
            $('.rotascheckgreen-'+item_rota.ircodigo).css('color','white')
            $('.rotascheckgreen-'+item_rota.ircodigo).parent().css('background','#009900')
            $('.rota-'+item_rota.ircodigo).css('color','#009900')
        }else if(item_rota.irstatus == 'P'){
            $('.rotascheckgreen-'+item_rota.ircodigo).css('color','white')
            $('.rotascheckgreen-'+item_rota.ircodigo).parent().css('background','red')
            $('.rota-'+item_rota.ircodigo).css('color','red')
        }
    }
    return marker;
}

//formata os dados exatamento como precisa
function updateInfoTable(rotas){
    for(i in rotas){
        var rota = rotas[i];
        var lista = new Array();
        var checkHtml = ''
        if(rota['rocor'] == undefined){
            rota['rocor'] = geraCor();
        }

        if(listaCheck.size > 0){
            console.log(rotas);
            // //checkbox
            var teste = false;
            listaCheck.forEach(function(data){
                if(!teste){
                    if(data == rota['rocodigo']){
                        checkHtml = `<input type="checkbox" class="checkView" data-rocor="`+rota['rocor']+`" data-rocodigo="`+rota['rocodigo']+`" data-modulo="`+mocodigo[i].mocodigo+`" style="height: 18px; width: 18px;" checked/>`
                       teste = true
                    }else{
                        checkHtml =`<input type="checkbox" class="checkView" data-rocor="`+rota['rocor']+`" data-rocodigo="`+rota['rocodigo']+`" data-modulo="`+mocodigo[i].mocodigo+`" style="height: 18px; width: 18px;"/>`
                    }
                }
            })
        }else{
            checkHtml = `<input type="checkbox" class="checkView" data-rocor="`+rota['rocor']+`" data-rocodigo="`+rota['rocodigo']+`" data-modulo="`+mocodigo[i].mocodigo+`" style="height: 18px; width: 18px;"/>`
        }
        lista.push(checkHtml)
        lista.push(rota['roplaca'])
        lista.push(rota['veprefixo'])

        //Data hora inicio
        if(rota['rostatus'] == 'F'){
            lista.push('Finalizada')
        }else if(rota['rostatus'] != 'I'){
            lista.push('Não iniciada')
        }else{
            if(rota['rodatahorainicio'] != null){
                var data =  rota['rodatahorainicio'].substring(8,10)+'/'+rota['rodatahorainicio'].substring(5,7)+
                ' '+rota['rodatahorainicio'].substr(11,5)
                lista.push(data)
            }else{
                lista.push('Sem informação')
            }
        }

        //Realizados
        var pontos = '';
        var pontos_array = new Array();
        pontos_array = rota['itens_rota'];
        var cont = 0;
        for(i in pontos_array){
            if(pontos_array[cont]['irstatus'] == 'F'){
                cont++;
            }
        }

        pontos = cont + ' | ' + pontos_array.length
        lista.push(pontos+`<div class="filto-status-rota-`+rota['rostatus']+`"> </div>` )
        $('.filto-status-rota-'+rota['rostatus']).parents('tr').css('background','red')

        //motorista/ajudante
        var mtnome = '';
        if(rota['mtmotorista'] == null){
            mtnome = 'Sem motorista'
        }else{
            mtnome = rota['mtmotorista'];
        }

        if(rota['mtajudante'] == null){
            mtnome += ' | '+'Sem ajudante'
        }else{
            mtnome+= ' | '+rota['mtajudante']
        }

        //
        var hodometro = '';
        if(rota['rohodometroinicio'] == null){
            hodometro = '0km'
        }else{
            hodometro = (parseFloat((rota['moultimohodometro'] - rota['rohodometroinicio'])/1000).toFixed(1))+'km'
        }

        if(rota['moultimohodometro'] == "" && rota['moultimohodometro']){
            hodometro += ' | 0km'
        }else{
            hodometro += ' | '+(parseFloat(rota['rokm']/1000).toFixed(1))+'km'
        }

        lista.push(hodometro)

        var tempo = ''
        if(rota['moultimohodometro'] != null && rota['rohodometroinicio'] != null){
            tempo = rota['moultimohodometro'] - rota['rohodometroinicio']; + 'h'
        }else{
            tempo = ''
        }

        //tempo
        var startTime=moment('00:00:00'),endTime =moment('00:00:00');
        var resto = ''
        if(rota['rodatahorainicio'] != null && rota['rotempo'] != null ){

            startTime = moment(rota['rodatahorainicio'].substring(10,19)+" pm", 'hh:mm:ss a');
            if(rota['rostatus'] == 'F'){
                if(rota['rodatahorafim'] != null){
                    endTime = moment(rota['rodatahorafim'].substring(10,19)+" pm", 'hh:mm:ss a');
                }
            }else{
                endTime = moment(rota['rotempo'].substring(0,7)+" pm", 'hh:mm:ss a');
            }

            var hours = endTime.diff(startTime, 'hours');
            var minutes = (endTime.diff(startTime, 'minutes') - (hours * 60))
            resto = hours+':'+minutes;
            var horatempo = '';

            if(rota['rotempo'] != null){
                horatempo = rota['rotempo'].substring(0,5)+'h'
            }else{
                horatempo = '0:h'
            }
            if(!isNaN(resto)){
                lista.push(resto+' | '+horatempo)
            }else{
                lista.push('Sem informação')
            }

        }else{
           lista.push('Sem informação')
        }
        lista.push(mtnome)
        if(rota['rostatus'] == 'P'){
            lista.push('<span class="fa fa-power-off"></span> <span class="fa fa-print"></span> <a data-status="'+rota['rostatus']+'"/>');
        }else{
            lista.push('<span class="fa fa-minus"></span> <span class="fa fa-print"></span> <a data-status="'+rota['rostatus']+'"/>');
        }
        lista.push(rota['rocor']);

        dataSet.push(lista);
        cont++;
    }
}

$('.filters').on('change',function(){
    $('.filto-status-rota-I').parents('tr').hide();
    if(isChecked($('#checkIniciado'))){
        $('.filto-status-rota-I').parents('tr').show();
    }

    $('.filto-status-rota-P').parents('tr').hide();
    if(isChecked($('#checkPendente'))){
        $('.filto-status-rota-P').parents('tr').show();
    }

    $('.filto-status-rota-F').parents('tr').hide();
    if(isChecked($('#checkFinalizado'))){
        $('.filto-status-rota-F').parents('tr').show();
    }
})

// function hideVeiculos(){
//     $('.checkboxTbPc').each(function(index, element) {
//         var modulo = $(element).data('modulo');
//         fechaOpcoesVeiculo(modulo);
//         $('#'+modulo).parent().hide();
//         $('#crosshair'+modulo).click(function() { return false; });
//         $($('.' + modulo)[0]).prepend('<div class="bloqueiaLatLngGoTo"></div>');
//     });
// }

//Checkbox para mostrar ou esconder veículos


$(document).on('click', '#checkboxTbPc', function() {
    removeClusterMarkers();
    if ($(this).prop('checked')) {
        $(this).prop('checked', true);
        $('.checkboxTbPc').prop('checked', true);
        $('.checkboxTbPc').each(function(index, element) {
            var modulo = $(element).data('modulo');
            veiculo[modulo] = oldVei[modulo];
            oldVei[modulo] = [];
            $($('.' + modulo)[0]).find('.bloqueiaLatLngGoTo').remove();
        })
    } else {
        $(this).prop('checked', false);
        $('.checkboxTbPc').prop('checked', false);
        $('.checkboxTbPc').each(function(index, element) {
            var modulo = $(element).data('modulo');
            fechaOpcoesVeiculo(modulo);
            oldVei[modulo] = veiculo[modulo];
            delete veiculo[modulo];
            $($('.' + modulo)[0]).prepend('<div class="bloqueiaLatLngGoTo"></div>');
        });
    }
    markerClusterVeiculo(veiculo);
});

$(document).on('change', '.checkboxTbPc', function(){
    var modulo = $(this).data('modulo');
    if ($(this).prop('checked')) {
        $(this).prop('checked', true);
        var mod = $(this).data('modulo');
        veiculo[mod] = oldVei[mod];
        $('.checkboxTbPc').each(function(index, element) {
            if ($(element).data('modulo') == modulo && !$(element).prop('checked')) {
                $($('.' + modulo)[0]).prepend('<div class="bloqueiaLatLngGoTo"></div>');
            } else {
                $($('.' + modulo)[0]).find('.bloqueiaLatLngGoTo').remove();
            }
        });
    } else {
        $(this).prop('checked', false);
        fechaOpcoesVeiculo(modulo)
        var mod = $(this).data('modulo');
        oldVei[mod] = veiculo[mod];
        delete veiculo[mod];
        $('.checkboxTbPc').each(function(index, element) {
            if ($(element).data('modulo') == modulo && !$(element).prop('checked')) {
                $($('.' + modulo)[0]).prepend('<div class="bloqueiaLatLngGoTo"></div>');
            } else {
                $($('.' + modulo)[0]).find('bloqueiaLatLngGoTo').remove();
            }
        });
    }
    if(Object.keys(veiclClusters).length > 0) {
        removeClusterMarkers();
    }
    markerClusterVeiculo(veiculo)

});

// function showHideVeiculos(){
    //função para mostrar e esconder os veículos
    $('#checkboxTbPc').click(function() {
        if ($(this).prop('checked')) {
            $(this).prop('checked', true);
            $('.checkboxTbPc').prop('checked', true);
            $('.checkboxTbPc').each(function(index, element) {
                var modulo = $(element).data('modulo');
                $('#'+modulo).parent().show();
                $($('.' + modulo)[0]).find('.bloqueiaLatLngGoTo').remove();
            })
        } else {
            $(this).prop('checked', false);
            $('.checkboxTbPc').prop('checked', false);
            $('.checkboxTbPc').each(function(index, element) {
                var modulo = $(element).data('modulo');
                fechaOpcoesVeiculo(modulo);
                $('#'+modulo).parent().hide();
                $('#crosshair'+modulo).click(function() { return false; });
                $($('.' + modulo)[0]).prepend('<div class="bloqueiaLatLngGoTo"></div>');
            });
        }
    });

//     //função para mostrar e esconder os veículos
//     $('.checkboxTbPc').change(function() {
//         var modulo = $(this).data('modulo');

//         if ($(this).prop('checked')) {
//             $(this).prop('checked', true);
//             $('#'+modulo).parent().show();
//             /*
//                 apaga essa parte que ficou ruim, nao continue esse codigo
//                 use uma classe
//             */
//             $('.checkboxTbPc').each(function(index, element) {
//                 if ($(element).data('modulo') == modulo && !$(element).prop('checked')) {
//                     $($('.' + modulo)[0]).prepend('<div class="bloqueiaLatLngGoTo"></div>');
//                 } else {
//                     $($('.' + modulo)[0]).find('.bloqueiaLatLngGoTo').remove();
//                 }
//             });
//         } else {
//             $(this).prop('checked', false);
//             fechaOpcoesVeiculo(modulo)
//             $('#'+modulo).parent().hide();
//             $('.checkboxTbPc').each(function(index, element) {
//                 if ($(element).data('modulo') == modulo && !$(element).prop('checked')) {
//                     $($('.' + modulo)[0]).prepend('<div class="bloqueiaLatLngGoTo"></div>');
//                 } else {
//                     $($('.' + modulo)[0]).find('bloqueiaLatLngGoTo').remove();
//                 }
//             });
//         }
//     });
// }

    //função para mostrar e esconder os veículos
    $('.checkboxTbPc').change(function() {
        var modulo = $(this).data('modulo');

        if ($(this).prop('checked')) {
            $(this).prop('checked', true);
            $('#'+modulo).parent().show();
            /*
                apaga essa parte que ficou ruim, nao continue esse codigo
                use uma classe
            */
            $('.checkboxTbPc').each(function(index, element) {
                if ($(element).data('modulo') == modulo && !$(element).prop('checked')) {
                    $($('.' + modulo)[0]).prepend('<div class="bloqueiaLatLngGoTo"></div>');
                } else {
                    $($('.' + modulo)[0]).find('.bloqueiaLatLngGoTo').remove();
                }
            });
        } else {
            $(this).prop('checked', false);
            fechaOpcoesVeiculo(modulo)
            $('#'+modulo).parent().hide();
            $('.checkboxTbPc').each(function(index, element) {
                if ($(element).data('modulo') == modulo && !$(element).prop('checked')) {
                    $($('.' + modulo)[0]).prepend('<div class="bloqueiaLatLngGoTo"></div>');
                } else {
                    $($('.' + modulo)[0]).find('bloqueiaLatLngGoTo').remove();
                }
            });
        }
    });



function selecionarPosicaoPonto(){
//esconde modal
$("#modalCadastro").modal("hide");

//recebe posicoes ja setadas
var lat = $("#inputPontoLatitude").val();
var lng = $("#inputPontoLongitude").val();
var raio = $("#inputPontoRaio").val();
var iniciado = 1;

if(lat.length == 0 || lng.length == 0){
    lat = mapa.getCenter().lat;
    lng = mapa.getCenter().lng;
    iniciado = 0;
}
if(raio.length == 0) raio = 50;

if(iniciado == 0){
//criar marker
novoPonto = L.marker([lat,lng],{
    draggable:true,
}).addTo(mapa);
novoPontoRaio = L.circle([lat,lng],{
    radius: raio
}
);
//centraliza
mapa.flyTo([lat,lng]);

var html = "<span class='bloco'>Me arraste para o local desejado.</span>"
+ "<span class='bloco'><span class='linha'><strong>Raio:</strong><input id='inputRangeRaio' value='"+raio+"' type='range' min='10' max='200' onchange='novoPontoRaio.setRadius(this.value);$(\"#metrosRaio\").html(this.value+\" Mts\")'><span id='metrosRaio'>"+raio+" Mts</span></span></span>"
+ "<button class='btn btn-warning btn-sm' onclick='finalizaPonto();'>É aqui mesmo!</button>";
novoPonto.bindPopup(html)
.openPopup();
novoPontoRaio.addTo(mapa);
novoPonto.on('dragstart',function(){
    novoPontoRaio.remove();
});
novoPonto.on('dragend',function(){
    novoPonto.openPopup();
    novoPontoRaio.setLatLng([novoPonto.getLatLng().lat,novoPonto.getLatLng().lng])
    .addTo(mapa);
});

}else{
    novoPonto.setLatLng([lat,lng]);
    $("#inputRangeRaio").val(raio);
    $("#metrosRaio").html(raio+" Mts");
    novoPonto.addTo(mapa);
    novoPontoRaio.setRadius(raio)
    .setLatLng([lat,lng])
    .addTo(mapa);
}
}

function finalizaPonto(){
    $("#modalCadastro").modal("show");
    $("#inputPontoLatitude").val(novoPonto.getLatLng().lat);
    $("#inputPontoLongitude").val(novoPonto.getLatLng().lng);
    $("#inputPontoRaio").val(novoPontoRaio.getRadius());
//remove marcador do mapa
novoPonto.remove();
novoPontoRaio.remove();
}

/*******************************************************************************
***********FUNCTION PARA CARREGAR MARKERS DOS VEICULOS MAPA*********************
*******************************************************************************/

//apenas move os ícones
function ajaxAtualizarMarkers(){
    var modulo = []
    for(var i in veiculo) {
        modulo.push(i);
    }
    $.ajax({
        url:ROOT+'/veiculos/maps/atualizarMarkers',
        type: 'post',
        dataType: 'json',
        data: {
            'modulo' : modulo,
        },
        success: function(retorno){
            if(retorno != null){
                veiculo = JSON.parse(JSON.stringify(retorno));
                if(Object.keys(veiculo).length == 0){
                    veiculo = [];
                }
                console.log('veic ',veiculo)
                var markers = veiclClusters.getLayers();
                // console.log('layer ',)
                for(var y in veiculo){
                    console.log('veiculos ',veiculo)
                    for(var m in markers) {
                        console.log('y ',y)
                        if(y == markers[m].options.info) {
                            try {
                                var mark = veiclClusters.getLayers()[m];
                                mark.setLatLng([veiculo[y].lat, veiculo[y].lng]);
                                var ico = preparaIcon(veiculo[y].tipo, veiculo[y].direcao, veiculo[y].prefixo,y,veiculo[y].ignicao);
                               // mark.setIcon(ico)
                            }catch(err) {
                                console.log("Error atualizaçao de markers!");
                            }
                        }
                    }
                }
            }
        }
    })
}


//compara
//apenas move os ícones/*
/*
function ajaxAtualizarMarkers(){
    var modulo = []
    for(var i in veiculo) {
        modulo.push(i);
    }
    $.ajax({
        url:ROOT+'/veiculos/maps/atualizarMarkers',
        type: 'post',
        dataType: 'json',
        data: {
            'modulo' : modulo,
        },
        success: function(retorno){
            if(retorno != null){
                veiculo = JSON.parse(JSON.stringify(retorno));
                if(Object.keys(veiculo).length == 0){
                    veiculo = [];
                }
                var markers = veiclClusters.GetMarkers();
                for(var y in veiculo){
                    for(var m in markers) {
                        if(y == markers[m].data.info) {
                            veiclClusters.GetMarkers()[m].Move(veiculo[y].lat, veiculo[y].lng);
                            var ico = preparaIcon(veiculo[y].tipo, veiculo[y].direcao, veiculo[y].prefixo,y,veiculo[y].ignicao);
                            veiclClusters.GetMarkers()[m].data.icon = ico;
                        }
                    }
                    var icone = preparaIcon(veiculo[y].tipo, veiculo[y].direcao, veiculo[y].prefixo,y,veiculo[y].ignicao);
                    try {
                        veiculoM[y].setIcon(icone);
                        veiculoM[y].addClass('hidden')
                        veiculoM[y].setLatLng([veiculo[y].lat,veiculo[y].lng]);
                    }

                    catch(err) {
                        console.log("Error atualizaçao de markers!");
                    }
                }
                veiclClusters.ProcessView();
                veiclClusters.RedrawIcons();
            }
        }
    })
}*/

function preparaIcon(tipo,direcao,prefixo,modulo,ignicao){
//prepara icon
var ignicao = ignicao == 1 ? "Ligada" : "Desligada";
if(tipo != "U"){
    var icon = new  L.divIcon({
        className: "divIcon divIcon" + direcao,
        html: "<div class='markerVeiculo' id='"+modulo+"' ><img src='./img/"+tipo+ignicao+direcao+".png'><span class='veprefixo'>"+prefixo+"</span></div>"
    });
}else{
    var icon = new  L.divIcon({
        className: "divIcon divIcon" + tipo,
        html: "<div class='markerVeiculo' id='"+modulo+"' ><img src='./img/"+tipo+ignicao+".png'><span class='veprefixo'>"+prefixo+"</span></div>"
    });
}
return icon;
}

//********FUNCAO PARA FICAR ATUALIZANDO POSICAO VEICULOS POR TEMPO PRE DETERMINADO*******/
// @Param tempo: tempo de intervalo entre cada chamada de funcao
function refreshAtualizaVeiculos(tempo){
    intervaloAtualizacao = setInterval(function(){
        ajaxAtualizarMarkers();
    },tempo);
}


/*******************************************************************************
************FUNCTION SARVAR POSICAO PONTO***************************************
*******************************************************************************/
function salvarNovoPonto(){

//recebe novas posicoes
var lat = novoPonto.getPosition().lat();
var lng = novoPonto.getPosition().lng();
//Atualiza inputs com novos valores
$("#inputPontoLatitude").val(lat);
$("#inputPontoLongitude").val(lng);

//mostra modal
$("#modalCadastro").modal('show');

//converte endereco
$.ajax({
    url:'./ajax/mapa/ajaxConverteEndereco.php',
    type:'post',
    data:{'tipo':'latlng','coordenadas': lat+','+lng},
    beforeSend: function(){
        $("#divStatusPontoEndereco").html("<img src='./img/mini-loader2.gif'>");
        $("#inputPontoEndereco").attr("disabled",true);
    },
    statusCode: {
        200: function(dados){
            $("#divStatusPontoEndereco").html("<span class='label label-success'>OK</span>");
            $("#inputPontoEndereco").val(dados);
        },
        404: function(){
            $("#divStatusPontoEndereco").html("<span class='label label-danger'>Erro!</span>");
        }
    }
});
}

function deletar_ponto_mapa(id, posicao, valor){
    $.post(ROOT+'/painel/cadastros/pontos/destroy_ponto_mapa/'+id,{password:valor}, function(dados){
        $('#divPasswordBlock').siblings().remove();
        $('#modalAlerta .modal-body').removeClass('text-danger');
        if (dados.erro) {
            $('#btnDeletaPonto').attr('disabled', false);
            $('#modalAlerta .modal-body').addClass('text-danger');
            $('#divPasswordBlock').css('color', '#636b6f')
            .parent().addClass("form-group has-error")
            .append('<div id="divErroBlock">'+dados.erro+'</div>');
            return;
        }

        var alert = '<div style="display:table; float: left;" class="alert alert-success col-md-12" role="alert">'
        +'<label class="text-success">'+dados.mensagem+'</label>'
        +'</div>';

        $('#divPasswordBlock').parent().removeClass("form-group has-error")
        .append(alert);
        setTimeout(function() {
            $('#modalAlerta').modal('hide');
        }, 1500);

        pontos[posicao].remove();
        pontosRaios[posicao].remove();
    })
}

$(document).on('click','.deletar_ponto_mapa',function(){
    var thad = $(this);
    $('#modalAlerta .modal-title').html('Esta ação requer confirmação de usuário');
    var body = '<div style="margin:auto; display: table">'
    +'<div><label>Informe sua senha:</label></div>'
    +'<div><input style="margin:auto; display: table;" autocomplete="false" type="password" id="divPasswordBlock"></div>'
    +'</div>';

    $('#modalAlerta .modal-body').html(body);
    var footer =
    '<div class="">'+
    '<button id="btnDeletaPonto" class="btn btn-success col-md-offset-2" type="button">'+
    '<span class="fa fa-check"></span> Confirmar'+
    '</button>'+
    '<button id="btnCancelarSenhaConfirm" class="btn btn-danger col-md-offset-2" type="button">'+
    '<span class="fa fa-times"></span> Cancelar'+
    '</button>'+
    '</div>';

    $('#modalAlerta .modal-footer').html(footer);

    $('#modalAlerta').modal('show');

    $("#btnDeletaPonto").click(function(){
        var valor = $(this).parents('#modalAlerta').find('#divPasswordBlock').val();
        deletar_ponto_mapa($(thad).attr('id'),$(thad).val(), valor);



    })
});


function busca_pontos_inicial(){

    //Remover todos os pontos
    if(pontos != null && pontos.length > 0){
        pontos.forEach(function(p, i){
            pontos[i].remove();
        });
    }
    pontos = []; //to a vingin

    //Remover todos os raios
    if(pontosRaios != null && pontosRaios.length > 0){
        pontosRaios.forEach(function(p, i){
            pontosRaios[i].remove();
        });
    }
    pontosRaios = []; //to a vingin

    $.ajax({
        url:ROOT+'/painel/cadastros/pontos/busca_inicial',
        type: 'post',
        dataType: 'json',
        data: {'coleta':coleta,'referencia':referencia,'entrega':entrega },
        success: function(retorno){
          if(retorno != 0){

            var markerClusters = L.markerClusterGroup({
               // disableClusteringAtZoom: agrupaMarkers,
                removeOutsideVisibleBounds: false,
            });

            $.each(retorno, function (i, objeto) {
                $.each(objeto, function(k, obj){
                    var imgPontos = {
                        'C': ROOT+'/img/coleta.png', //coleta
                        'E': ROOT+'/img/entrega.png', //entrega
                        'P': ROOT+'/img/referencia.png', //referencia
                    };


                    var imgPonto = 'https://unpkg.com/leaflet@1.0.3/dist/images/marker-icon.png';
                    if (typeof imgPontos[obj.potipo] !== 'undefined')
                        imgPonto = imgPontos[obj.potipo];

                    var icone = new L.icon({
                        iconUrl: imgPonto,
                        iconSize: [34, 34], //34, 34
                        iconAnchor: [17, 32],
                        popupAnchor: [-1, -30],
                    });

                    var link = '<span class="up-ponto-descricao ponto-descricao" data-k='+k+' data-id="'+obj.pocodigo+'" data-raio="'+obj.poraio+'" title="'+obj.podescricao+'">'+obj.podescricao.substr(0, 26)+'</span>';
                    link += '<span class="ponto-descricao ponto-raio">Raio:'+obj.poraio+'Mts</span><br />'
                    link += ' <a href="'+ROOT+'/painel/cadastros/pontos/show/'+obj.pocodigo+'"class="btn bts-acoes bt-edit-ponto btn-info btn-sm"><span class="fa fa-pencil-square-o"></span> Editar</a>';
                    link += ' <button id="'+obj.pocodigo+'" value="'+k+'" class="btn bts-acoes btn-danger btn-sm deletar_ponto_mapa"><span class="fa fa-trash-o"></span> Deletar</button>';

                    var m = L.marker( [obj.polatitude, obj.polongitude], {icon: icone, radius:obj.poraio} )
                                      .bindPopup( link );
                    // pontosRaios = L.circle([obj.polatitude, obj.polongitude],{radius: obj.poraio});
                    // pontosRaios[k].addTo(mapa);

                    markerClusters.addLayer( m );
                    pontos[k] = markerClusters;

                });
                mapa.addLayer( markerClusters );

                markerClusters.on('mouseover', function (e) {
                    pontosRaios = L.circle([e.latlng.lat, e.latlng.lng],{radius: e.layer.options.radius});
                    pontosRaios.addTo(mapa);
                });
                markerClusters.on('mouseout', function (e) {
                    mapa.removeLayer(pontosRaios)
                });
             });
           }
        }
    });
}

$(document).on('mouseover', '.marker-ponto img', function(){
// $(".markerPonto img").hover(function(){
    var lat = $(this).parent().data('lat');
    var lng = $(this).parent().data('lng');
    var raio = $(this).parent().data('raio');

    pontoRaio[$(this).parent().attr('id')] = L.circle([lat,lng],{
        radius: raio,
        className: 'iconCircle',
        }
    );
    mapa.addLayer(pontoRaio[$(this).parent().attr('id')])

})


$(document).on('mouseout', '.marker-ponto img', function(){
    for(var i in pontoRaio) {
        mapa.removeLayer(pontoRaio[i])
    }
    $(".iconCircle").remove();
})


$(document).on('click', '.bt-edit-ponto', function(e){

  e.preventDefault();
  var opt = '<option value="C">Ponto de Coleta</option>'
  opt += '<option value="E">Ponto de Entrega</option>'
  opt += '<option value="P">Referência</option>'


  var popUp = $(".leaflet-popup-content");
  var tamanho = $(popUp).width();
  var old =  $(popUp).html();
  var raio = $(".up-ponto-descricao").data('raio');
  var nome = $(".up-ponto-descricao").html();
  var id = $(".up-ponto-descricao").data('id');
  var k = $(".up-ponto-descricao").data('k');

  var novo = '<form action="'+ROOT+'/painel/cadastros/pontos/update/mapa">'
  novo += '<input type="hidden" name="pocodigo" value="'+id+'">'
  novo += '<span class="alinhar-label">Tipo:</span><select name="potipo" class="select-tipo-ponto form-control alinhar-input">'+opt+'</select>';
  novo += '<span class="alinhar-label">Nome:</span><input type="text" value="'+nome+'" name="podescricao" class="form-control ip-nome-ponto alinhar-input">';
  novo += '<span class="alinhar-label">Raio:</span><input type="range" name="poraio" data-k="'+k+'" min="10" max="200" value="'+raio+'" class="ip-nome-ponto ip-range alinhar-input">';
  novo += '<span id="metrosRaio">'+raio+'Mts</span>'
  novo += '<a href="#" class="btn bts-acoes btn-sm bt-salvar-pt"><span class="fa  fa-check"></span> Salvar</a>';
  novo += '<button type="submit" class="btn bts-acoes btn-sm bt-cancelar-update btn-danger"><span class="fa fa-times"></span> Cancelar</button>';
  novo += '</div>';
  novo += '</form>'

  $(popUp).html(novo);

  $('.bt-cancelar-update').click(function(e){
    e.preventDefault();
    $(popUp).html(old);
  })

  $('.bt-salvar-pt').click(function(){
    $(this).parents('form').ajaxForm({
      type:'post',
      success: function(dados) {
        var p = dados.ponto;

        mapa.removeLayer(pontosRaios[k]);
        mapa.removeLayer(pontos[k]);

        var imgPontos = {
            'C': ROOT+'/img/coleta.png', //coleta
            'E': ROOT+'/img/entrega.png', //entrega
            'P': ROOT+'/img/referencia.png', //referencia
        };
        var imgPonto = 'https://unpkg.com/leaflet@1.0.3/dist/images/marker-icon.png';
        if (typeof imgPontos[p.potipo] !== 'undefined')
            imgPonto = imgPontos[p.potipo];

        var icone = new L.icon({
            iconUrl: imgPonto,
            iconSize: [34, 34], //34, 34
            iconAnchor: [17, 32],
            popupAnchor: [-1, -30],
        });

        pontos[k] = new L.marker([p.polatitude,p.polongitude],{icon: icone});

        pontosRaios[k] = L.circle([p.polatitude,p.polongitude],{radius: p.poraio});
        pontosRaios[k].addTo(mapa);

        link = '<span class="up-ponto-descricao ponto-descricao" data-k='+k+' data-id="'+p.pocodigo+'" data-raio="'+p.poraio+'" title="'+p.podescricao+'">'+p.podescricao.substr(0, 26)+'</span>';
        link += '<span class="ponto-descricao ponto-raio">Raio:'+p.poraio+' Mts</span><br />'
        link += ' <a href="'+ROOT+'/painel/cadastros/pontos/show/'+p.pocodigo+'"class="btn bts-acoes bt-edit-ponto btn-info btn-sm"><span class="fa fa-pencil-square-o"></span> Editar</a>';
        link += ' <button id="'+p.pocodigo+'" value="'+k+'" class="btn bts-acoes btn-danger btn-sm deletar_ponto_mapa"><span class="fa fa-trash-o"></span> Deletar</button>';

        pontos[k].bindPopup(link).addTo(mapa);
      }
    }).submit();
  })

})

$(document).on('change', '.ip-range', function(){
  var mtrs = $(this).val()
  var k = $(this).data('k');
  pontosRaios[k].setRadius(mtrs)
  $('#metrosRaio').html(mtrs+ "Mts")
});


//função responsável por buscar as regiões de um cliente
function buscaRegioes() {
    $.ajax({
        url: ROOT+'/painel/cadastros/regioes/buscaRegioes',
        type: 'post',
        dataType: 'json',
        data: {
            'cliente': JSON.stringify($('.select-empresa-painel').val()),
            'multiempresa': ($('.select-empresa-painel').val().length > 1 ? 'true' : 'false')
        },
        success: function(retorno) {
            montaRegioes(retorno);
        }
    });
}

function montaRegioes(regioes) {
    for (var i in regioes) {
        var coordenadas = regioes[i].regioes_coordenadas;
        var coordenadasArray = [];
        for (var y in coordenadas) {
            coordenadasArray.push([coordenadas[y].rclatitude, coordenadas[y].rclongitude]);
        }

        var polygon = L.polygon(
            coordenadasArray,
            {
                color: regioes[i].recor.toString(),
                opacity: 0.3,
                fill: true,
                fillOpacity: 0.2,
                allowIntersection: false,
                className: 'regiaoNoMapaRemove'
            }
            ).addTo(mapa);

        var velocidade = regioes[i].revelocidade ? '<div> Velocidade: '+(regioes[i].revelocidade.split('.'))[0]+' km/h</div>' : '';
        var empresa = (regioes[i].clnome ? '<div>Empresa: '+(regioes[i].clnome.length > 25 ? regioes[i].clnome.substr(0,20)+'...' : regioes[i].clnome)+'</div>' : '');
        var cor = '<div>Cor: <span style="background-color: '+regioes[i].recor+';padding: 1px 8px; opacity: 0.2;margin-left:3px;"></span></div>';
        var popupRegiao =
        '<div style="display: table; min-width: 90px;">'
        +'<div><strong>'+regioes[i].redescricao+'</strong></div>'
        +empresa
        +velocidade
        +cor
        +'</div>';

        polygon.on('click', function(event) {
        }).bindPopup(popupRegiao);
    }

}


/*******************************************************************************
******************FUNCTION RESTAURA OPCOES VEICULOS*****************************
*******************************************************************************/
function restauraOpcoesVeiculo(){
// if(rotaTemp != undefined) mapa.removeLayer(rotaTemp);
// if(rotaDestacada != undefined) mapa.removeLayer(rotaDestacada);
// if(rotaInicio != undefined) rotaInicio.remove();
$('#checkboxRota').prop('checked',false);
$('#checkboxParadas').prop('checked',false);
$('#checkboxVelocidade').prop('checked',false);
$('#checkboxAcPortas').prop('checked',false);
removeRotas();
$('.divIconVelocidade').remove();
$('.divIconParada').remove();
$('.divIconPorta').remove();
mapa.closePopup();

$("#btnRestauraOpcoesVeiculo").addClass("hide");
$("#checkboxRota,#checkboxParadas,#checkboxVelocidade").attr("checked",false);
$("#divResultadoDentroOpcoesVeiculos").html("");
$("#divResultadoOpcoesVeiculosParametros").html("");
$("#btnVoltarOpcoes,#divResultadoOpcoesVeiculos").hide("slow");
$(".opcoes").css("width","auto");
$(".opcoes,#divInformacoesVeiculo").show("slow");
$("#divOpcoesVeiculo").animate({
    top:"40px",
// height:"460px"
},500);
}


function mostraOpcoesVeiculo(modulo){

    var html =
    "<div id='divCarregandoOpcoesPC' style='display: block'>"
    +"<div>"
    +"<span class='fa fa-spinner fa-spin fa-3x fa-fw'></span>"
    +"Carregando..."
    +"</div>";
    +"</div>";
    $("#divOpcoesVeiculo").html(html).show("slide");

    //centraliza mapa
    // var lat = veiculoM[0].position.lat;
    // var lng = veiculoM[0].position.lng;
    // mapa.flyTo([lat,lng]);

    $.ajax({
        url: ROOT+'/veiculos/maps/carregarMarkers',
        type: 'post',
        dataType: 'json',
        data: {'modulo' : modulo},
        success: function(x){
            var dataIni = moment(x[modulo].dataI, 'DD/MM/YYYY HH:mm:ss').format('DD/MM/YYYY')
            var dataFim = moment(x[modulo].dataF, 'DD/MM/YYYY HH:mm:ss').format('DD/MM/YYYY')
            var bloqueio = '';
            if (x[modulo].ultimobloqueio == 3) {
                var bloqueio = 'checked'
            }

        var motoristaPC = (x[modulo].motorista ? x[modulo].motorista : 'Sem motorista');
        var motoristaPCQuebrado = (motoristaPC.length > 14 ? motoristaPC.substr(0, 14).trim()+'...' : motoristaPC);
        var ajudantePC = (x[modulo].ajudante ? x[modulo].ajudante : 'Sem ajudante');
        var ajudantePCQuebrado = (ajudantePC.length > 14 ? ajudantePC.substr(0, 14).trim()+'...' : ajudantePC);
        var descricao = x[modulo].descricao;

        if (descricao) {
            var tratamentoDescricao = (descricao.length > 14 ? descricao.substr(0, 14).trim()+'...' : descricao)
        } else {
            var tratamentoDescricao = '&nbsp;';
        }
        var btAcoes = '';

        html = "<div class='bloco'>"
                    +"<div id='divInformacoesVeiculo' data-modulo='"+modulo+"'>"
                        +"<div class='infoVeiculoTitulo linha'>"
                            +"<div class='titleInfoV'><span class='fa fa-car'></span> Informações do Veículo</div>"
                            +"<button class='btn btn-xs btn-danger fechaOpcoesVeiculo' id='fechaOpcoesVeiculo'><span class='glyphicon glyphicon-remove'></span></button>"
                        +"</div>"
                        +"<div id='divInfoVeiculos'>"
                            +"<div class='divInfoVeiculos'>"
                                +"<div class='divInfoVeiculosLeft'>Placa:</div>"
                                +"<div class='divInfoVeiculosLeft'>Descrição:</div>"
                                +"<div class='divInfoVeiculosLeft'>Motorista:</div>"
                                +"<div class='divInfoVeiculosLeft'>Ajudante:</div>"
                                +"<div class='divInfoVeiculosLeft'>Percorridos Hoje:</div>"
                                +"<div class='divInfoVeiculosLeft'>Empresa:</div>"
                            +"</div>"
                            +"<div class='divInfoVeiculos'>"
                                +"<div class='divInfoVeiculosRight'>"+x[modulo].placa+"</div>"
                                +"<div class='divInfoVeiculosRight' title='"+descricao+"'>"+tratamentoDescricao+"</div>"
                                +"<div class='divInfoVeiculosRight' title='"+motoristaPC+"'>"+motoristaPCQuebrado+"</div>"
                                +"<div class='divInfoVeiculosRight' title='"+ajudantePC+"'>"+ajudantePCQuebrado+"</div>"
                                +"<div class='divInfoVeiculosRight'>"+x[modulo].totalKm+" Km</div>"
                                +"<div class='divInfoVeiculosRight' title='"+x[modulo].cliente+"'>"+(x[modulo].cliente.length > 18 ? x[modulo].cliente.substr(0, 16).trim()+'...' : x[modulo].cliente)+"</div>"
                            +"</div>"
                        +"</div>"
                    +"</div>"
                    +"<div class='bloco'>"
                        +"<div id='divAcoesVeiculo'>"
                            +"<span class='acoesVeiculoTitulo linha titleInfoV'><span class='fa fa-cogs'></span> Opções</span>"
                            +"<hr style='margin-top:1px;margin-bottom:1px;'>"
                            +"<div class='divBtnsAcoes'>"
                            +"<div class='left'>"
                                +"<input type='hidden' name='status'>"
                                +"<div style='float: right;'>"
                                    +"<span>Desbloqueado</span>"
                                +"</div>"
                                +"<div>"
                                    +"<span>Ativar pânico</span>"
                                +"</div>"
                            +"</div>"
                            +"<div class='left'>"
                                +"<div class='divCheckboxOpcoes'>"
                                    +"<label style='display:table; float:left;' class='col-xs-3 switch panicoOpcoes clickSolicitaSenhaContinuar'>"
                                        +"<input id='iptCheckboxBloqueio' disabled type='checkbox' data-bloqueio='"+x[modulo].ultimobloqueio+"' data-modelo='"+x[modulo].modelo+"' data-modulo='"+x[modulo].modulo+"' "+bloqueio+" class='bloqueio-veiculo' name='status'>"
                                        +"<div class='slider round'></div>"
                                    +"</label>"
                                +"</div>"
                                +"<div>"
                                    +"<label class='col-xs-3 switch panicoOpcoes'>"
                                        +"<input disabled type='checkbox' data-modelo='"+x[modulo].modelo+"' data-modulo='"+x[modulo].modulo+"' "+bloqueio+" class='bloqueio-veiculo' name='status'>"
                                        +"<div class='slider round'></div>"
                                    +"</label>"
                                +"</div>"
                            +"</div>"
                            +"<div class='left'>"
                                +"<div>"
                                    +"<span>Bloqueado</span>"
                                +"</div>"
                                +"<div>"
                                    +"<span>Inativar Pânico</span>"
                                +"</div>"
                            +"</div>"
                        +"</div>"
                            +"</div>"
                    +"</div>"
                +"</div>"
                +"<div class='bloco'>"
                    +"<div id='divCarregandoOpcoesPC'>"
                        +"<div>"
                            +"<span class='fa fa-spinner fa-spin fa-3x fa-fw'></span>"
                            +"Carregando..."
                        +"</div>"
                    +"</div>"
                    +"<div class='bloco'>"
                        +"<div class='form-group divParamDataInfoVeiculos'>"
                            +"<div class='paramDataInfoVeiculos'>"
                                +"<span>Data/Hora Inicial</span>"
                                +"<div class='col-sm-12 input-group date' >"
                                    +"<input class='data-inicio-pinfo data-data-pinfo' value='"+dataIni+"' type='text' name='data-inicio' style='width: 53%;'>"
                                    +"<input class='hora-inicio-pinfo'  type='text' name='data-inicio' style='width: 40%;'>"
                                +"</div>"
                            +"</div>"
                            +"<div class='paramDataInfoVeiculos'>"
                                +"<span>Data/Hora Final</span>"
                                +"<div class='col-xs-12 input-group date'>"
                                    +"<input class='data-data-pinfo data-fim-pinfo' value='"+dataFim+"' type='text' name='data-inicio'  style='width: 53%;'>"
                                    +"<input class='hora-final-pinfo' type='text' name='data-inicio'  style='width: 40%;'>"
                                +"</div>"
                            +"</div>"
                        +"</div>"
                    +"</div>"
                    +"<table id='tbPainelInfoVeiculos'>"
                        +"<tr>"
                            +"<td>"
                                +"<input id='checkboxRota' type='checkbox' class='heckboxRota' data-mod='"+modulo+"' data-placa='"+x[modulo].placa+"'>"
                            +"</td>"
                            +"<td><span>Rastro</span></td>"
                            +"<td style='width: 10px;'>"
                                +"<input id='checkboxTracoCorrigida' type='checkbox' class='checkboxTracoCorrigida'>"
                            +"</td>"
                            +"<td style='padding: 1px;' ><span title='Estamos testando esta funcionalidade para melhorar o rastro percorrido pelo veículo' >Rastro Corrigido (BETA)</span></td>"
                        +"</tr>"
                        +"<tr>"
                            +"<td>"
                                +"<input id='checkboxParadas' type='checkbox'>"
                            +"</td>"
                            +"<td><span>Paradas</span></td>"
                            +"<td colspan=2 >"
                                +"<input type='hidden' value='5' id='inputHParametroParadas'>"
                                +"<div id='divParametroParadas' class='btn-group btn-group-justified' role='group'>"

                                    +"<div class='btn-group' role='group'>"
                                        +"<button type='button' class='btn btn-xs btn-primary' data-parada='0'>*</button>"
                                    +"</div>"
                                    +"<div class='btn-group' role='group'>"
                                        +"<button type='button' class='btn btn-xs btn-primary' data-parada='60'>1</button>"
                                    +"</div>"
                                    +"<div class='btn-group' role='group'>"
                                        +"<button type='button' class='btn btn-xs btn-primary disabled' data-parada='300'>5</button>"
                                    +"</div>"
                                    +"<div class='btn-group' role='group'>"
                                        +"<button type='button' class='btn btn-xs btn-primary' data-parada='900'>15</button>"
                                    +"</div>"
                                    +"<div class='btn-group' role='group'>"
                                        +"<button type='button' class='btn btn-xs btn-primary' data-parada='1800'>30</button>"
                                    +"</div>"
                                    +"<div class='btn-group' role='group'>"
                                        +"<button type='button' class='btn btn-xs btn-primary' data-parada='3600'>60</button>"
                                    +"</div>"
                                +"</div>"
                            +"</td>"
                        +"</tr>"
                        +"<tr>"
                            +"<td>"
                                +"<input id='checkboxVelocidade' type='checkbox' data-modulo='" + modulo + "'>"
                            +"</td>"
                            +"<td><span>Velocidade</span></td>"
                            +"<td colspan=2 >"
                                +"<input type='hidden' id='inputHParametroVelocidade' value='80'>"
                                +"<div id='divParametroVelocidade' class='btn-group btn-group-justified' role='group'>"
                                    +"<div class='btn-group' role='group'>"
                                        +"<button type='button' class='btn btn-xs btn-primary' data-velocidade='50'>50</button>"
                                    +"</div>"
                                    +"<div class='btn-group' role='group'>"
                                        +"<button type='button' class='btn btn-xs btn-primary disabled' data-velocidade='80'>80</button>"
                                    +"</div>"
                                    +"<div class='btn-group' role='group'>"
                                        +"<button type='button' class='btn btn-xs btn-primary' data-velocidade='100'>100</button>"
                                    +"</div>"
                                    +"<div class='btn-group' role='group'>"
                                        +"<button type='button' class='btn btn-xs btn-primary' data-velocidade='120'>120</button>"
                                    +"</div>"
                                +"</div>"
                            +"</td>"
                        +"</tr>"
                        +"<tr>"
                            +"<td>"
                                +"<input id='checkboxAcPortas' type='checkbox' title='Acionamento das portas' data-modulo='" + modulo + "'>"
                            +"</td>"
                            +"<td><span>Ac. Portas</span></td>"
                            +"<td colspan=2 >"
                                +"<input type='hidden' id='inputHParametroAcPortas' value='1'>"
                                +"<div id='divParametroAcPortas' class='btn-group btn-group-justified' role='group'>"
                                    +"<div class='btn-group' role='group'>"
                                        +"<button type='button' class='btn btn-xs btn-primary disabled' data-porta='14'>1</button>"
                                    +"</div>"
                                    +"<div class='btn-group' role='group'>"
                                        +"<button type='button' class='btn btn-xs btn-primary' data-porta='16'>2</button>"
                                    +"</div>"
                                    +"<div class='btn-group' role='group'>"
                                        +"<button type='button' class='btn btn-xs btn-primary' data-porta='18'>3</button>"
                                    +"</div>"
                                    +"<div class='btn-group' role='group'>"
                                        +"<button type='button' class='btn btn-xs btn-primary' data-porta='20'>4</button>"
                                    +"</div>"
                                +"</div>"
                            +"</td>"
                        +"</tr>"
                    +"</table>"
                    +"<div>"
                        +"<button type='button' id='btnListaPosInfoVeiculos'>Lista de Posições</button>"
                    +"</div>"
                +"</div>";



//Mostra DIV
$("#divOpcoesVeiculo").html(html);

//solicita senha nas opções
$('.clickSolicitaSenhaContinuar').click(function() { //aquisolicita
    $('#modalAlerta .modal-title').html('Esta ação requer confirmação de usuário');

    var body = '<div style="margin:auto; display: table">'
    +'<div><label>Informe sua senha:</label></div>'
    +'<div><input style="margin:auto; display: table;" autocomplete="false" type="password" id="divPasswordBlock"></div>'
    +'</div>';

    $('#modalAlerta .modal-body').css({'display':'table', 'width':'100%'}).html(body);
    var footer =
    '<div class="">'+
    '<button id="btnConfirmarSenhaConfirm" class="btn btn-success col-md-offset-2" type="button">'+
    '<span class="fa fa-check"></span> Confirmar'+
    '</button>'+
    '<button id="btnCancelarSenhaConfirm" class="btn btn-danger col-md-offset-2" type="button">'+
    '<span class="fa fa-times"></span> Cancelar'+
    '</button>'+
    '</div>';

    $('#modalAlerta .modal-footer').html(footer);

    $('#modalAlerta').modal('show');

    $(document).on('click', '#modalAlerta #btnConfirmarSenhaConfirm', function() {
        $(this).attr('disabled', true);
        var password = $('#divPasswordBlock').val();
        var bloqueioData = $('#iptCheckboxBloqueio').data('bloqueio');
        var bloqueio = 0;
        if (bloqueioData == 0)
            bloqueio = 3;
        bloqueiaVeiculo(bloqueio, password);
    });

})

var htmlAguarde = '<span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Carregando...';//loading veiculos
// $(".reqListaLoading").html(htmlAguarde);

$(".data-data-pinfo").mask('99/99/9999');
//$(".hora-inicio-pinfo").mask('99:99:99')
//$(".hora-final-pinfo").mask('99:99:99')

$(".data-data-pinfo").datepicker({
    format: 'dd/mm/yyyy',
    language: 'pt-BR',
    showOn: "focus"
})

$(".hora-inicio-pinfo").timepicker({
    minuteStep: 5,
    showSeconds: true,
    showMeridian: false,
    defaultTime: '00:00:15',
    showOn: "focus"
})
$(".hora-final-pinfo").timepicker({
    minuteStep: 5,
    showSeconds: true,
    showMeridian: false,
    defaultTime: '23:59:59',
    showOn: "focus"
})
$('.heckboxRota').click(function(){
    if($(this).is(":checked")) {
        if($('.checkboxTracoCorrigida').is(':checked')){
            $('.checkboxTracoCorrigida').prop("checked", false);
        }
        removeRotas();
        montaDadosOpcoesInfoVeiculos();
    }else {
        removeRotas();
    }
})
$('#checkboxTracoCorrigida').click(function(){
    removeRotas();
    if($(this).is(":checked")) {
        $('.heckboxRota').prop("checked", false);
        montaDadosOpcoesInfoVeiculos();
    }
})
$("#fechaOpcoesVeiculo").click(function(){
    fechaOpcoesVeiculo()
    removeRotas();
})
$("#btnRestauraOpcoesVeiculo").click(function(){
    $('#fechaOpcoesVeiculo').css('left', '93.5%');
    var popupParada = $('.leaflet-popup-close-button');
    popupParada.length > 0 ? popupParada[0].click() : '';
    restauraOpcoesVeiculo();
})

function bloqueiaRequisicaoData() {
    $('#checkboxRota').prop('checked',false);
    $('#checkboxTracoCorrigida').prop('checked',false);
    $('#checkboxParadas').prop('checked',false);
    $('#checkboxVelocidade').prop('checked',false);
    $('#checkboxAcPortas').prop('checked',false);
    $('#divOpcoesVeiculoListaPosicoes').hide();
    removeRotas();
    $('.divIconVelocidade').remove();
    $('.divIconParada').remove();
    $('.divIconPorta').remove();
    mapa.closePopup();
    var tmpIni = $('.data-inicio-pinfo').val().split('/');
    var tmpFim = $('.data-fim-pinfo').val().split('/');
    var dataFim = moment([tmpFim[2], tmpFim[1], tmpFim[0]]);
    var dataIni = moment([tmpIni[2], tmpIni[1], tmpIni[0]]);

    if (dataFim.diff(dataIni, 'days') < 0) {
        $('.data-fim-pinfo').tooltip({
            animation: true,
            title: 'Data final deve ser maior que a inicial',
            placement: 'right',
            container: 'body'
        });
        $('.data-fim-pinfo').tooltip('show');
        return;
    }

    if (dataFim.diff(dataIni, 'days') > 7) {
        $('.data-fim-pinfo').tooltip({
            animation: true,
            title: 'O período informado é superior a 7 dias',
            placement: 'right',
            container: 'body'
        });
        $('.data-fim-pinfo').tooltip('show');
        return;
    }

    $('.data-fim-pinfo').tooltip('destroy');
    limpaVariaveisRequisicao();
// montaDadosOpcoesInfoVeiculos();
}

$('.data-inicio-pinfo').change(function() {
    bloqueiaRequisicaoData();
});
$('.data-fim-pinfo').change(function() {
    bloqueiaRequisicaoData();
});
$('.hora-inicio-pinfo').change(function() {
    bloqueiaRequisicaoData();
});
$('.hora-final-pinfo').change(function() {
    bloqueiaRequisicaoData();
});

var listaPosicaoAberta = 1;
$('#btnListaPosInfoVeiculos').click(function() {
    if (listaPosicaoAberta) {
        listaPosicaoAberta = 0;
        montaTabelaInfoVeiculos();
// $('#divOpcoesVeiculoListaPosicoes').css('display', 'block')
$('#divOpcoesVeiculoListaPosicoes').show()
}
else {
    listaPosicaoAberta = 1
    $('#divOpcoesVeiculoListaPosicoes').hide()
// $('#divOpcoesVeiculoListaPosicoes').css('display', 'none')
}
})

$('#fechaOpcoesVeiculoListaPos').click(function() {
    listaPosicaoAberta = 1
    $('#divOpcoesVeiculoListaPosicoes').hide()
});
}//fim success
});//fim ajax

//remove markers nao selecionados
// for(var a in veiculo){
//   if(a != modulo){
//     veiculoM[a].remove();
//   }
// }
}

$(document).on('click', '#btnCancelarSenhaConfirm', function() {
    $('#modalAlerta').modal('hide');
});

$(document).on('click', '#checkboxVelocidade', function(){
    if (!$(this).attr('checked')) {
        $(this).attr('checked', true);
        montaDadosOpcoesInfoVeiculos();
        return;
    }

    $(this).attr('checked', false);
    $('.divIconVelocidade').remove();
});

$(document).on('click', '#divParametroVelocidade button', function() {
    $('#divParametroVelocidade button').removeClass('disabled');
    excessoVelocidadeGlobal = $(this).data('velocidade');
    $(this).addClass('disabled');
    $('.divIconVelocidade').remove();
    if ($('#checkboxVelocidade').attr('checked')) {
        mostraExcessoVelocidade();
    }
});

function tratamentoValoresVaziosReq(valores) {
    if (valores != null && valores != '' && valores.length > 0)
        return 'ok';

    var html = "<div>Não há dados no período selecionado!<div>";
    $("#divCarregandoOpcoesPC").html(html).show("slide");

    setTimeout(function() {
        $("#divCarregandoOpcoesPC").hide("slide");
        var restauraHtml = "<span class='fa fa-spinner fa-spin fa-3x fa-fw'></span>"
        +"Carregando...";
        $("#divCarregandoOpcoesPC div").html(restauraHtml);

        $('#checkboxRota').prop('checked',false);
        $('#checkboxParadas').prop('checked',false);
        $('#checkboxVelocidade').prop('checked',false);
        $('#checkboxAcPortas').prop('checked',false);
        $("#checkboxTracoCorrigida").prop('checked',false);
    }, 3000);
}

function getExcessoVelocidade() {
    $('#divCarregandoOpcoesPC').show();
// $('.reqListaLoading').show();
$.ajax({
    url:ROOT+'/veiculos/maps/excessosVelocidades',
    type: 'post',
    data: {
        'placa': $('#checkboxRota').data('placa'),
        'dataIni': $('.data-inicio-pinfo').val()+' '+$('.hora-inicio-pinfo').val(),
        'dataFim': $('.data-fim-pinfo').val()+' '+$('.hora-final-pinfo').val()
    },
    dataType: 'json',
    success: function(retorno) {
        requisicaoExcessosVelocidades = retorno;
        $('#divCarregandoOpcoesPC').hide();
        mostraExcessoVelocidade();
    }
});
}

function mostraExcessoVelocidade() {
    if (!requisicaoExcessosVelocidades) {
        getExcessoVelocidade();
        return;
    }

// $('.reqListaLoading').hide();
var excessosVelocidade = requisicaoExcessosVelocidades.array;
tratamentoValoresVaziosReq(excessosVelocidade);
for (var r in excessosVelocidade) {
    if (excessosVelocidade[r].velocidade > excessoVelocidadeGlobal) {
        var html =
        '<div class="placa-excesso-velocidade">'+
        '<span>' + excessosVelocidade[r].velocidade + '</span>'+
        '</div>';
        var icone = new  L.divIcon({
            className: "divIconVelocidade",
            html: html
        });
        var latlog = excessosVelocidade[r].posicao.split(',');
        new L.marker([latlog[0], latlog[1]], {
            icon: icone
        }).addTo(mapa);
    }
}
}

function desenhaRota() {
// $('.reqListaLoading').show();
$('#divCarregandoOpcoesPC').show();
var placa = $('#checkboxRota').data('placa');
var dataIni = $('.data-inicio-pinfo').val()+' '+$('.hora-inicio-pinfo').val();
var dataFim = $('.data-fim-pinfo').val()+' '+$('.hora-final-pinfo').val();
//cancela refresh veiculos
clearInterval(intervaloAtualizacao);
//Efeitos

//ajax para carregar posicoes
$.post(ROOT+'/veiculos/maps/rotas',
{
    'placa':placa,
    'dataIni':dataIni,
    'dataFim':dataFim
},
function(data){
    requisicaoRotas = data;
    $('#divCarregandoOpcoesPC').hide();
    montaRota();
})
}
function desenhaRotaCorrigida() {
// $('.reqListaLoading').show();
$('#divCarregandoOpcoesPC').show();
var placa = $('#checkboxRota').data('placa');
var dataIni = $('.data-inicio-pinfo').val()+' '+$('.hora-inicio-pinfo').val();
var dataFim = $('.data-fim-pinfo').val()+' '+$('.hora-final-pinfo').val();
//cancela refresh veiculos
clearInterval(intervaloAtualizacao);
//Efeitos

//ajax para carregar posicoes
$.post(ROOT+'/veiculos/maps/rastro/corrigido',
{
    'placa':placa,
    'dataIni':dataIni,
    'dataFim':dataFim
},
function(data){
    requisicaoRastroCorrigido = data;
    $('#divCarregandoOpcoesPC').hide();
    montaRastroCorrigido();
})
}

function destacaTrecho(lat1,lng1,lat2,lng2){
    if(rotaDestacada != undefined)
        mapa.removeLayer(rotaDestacada);

    rotaDestacada = L.polyline.antPath([[lat1,lng1],[lat2,lng2]],{color:'red'}).addTo(mapa);
    mapa.flyTo([lat1,lng1], 18);
}

function removeRotas(){
    if(rotaTemp != undefined) mapa.removeLayer(rotaTemp);
    if(rotaCorrigidaTemp != undefined) mapa.removeLayer(rotaCorrigidaTemp);
    if(rotaDestacada != undefined) mapa.removeLayer(rotaDestacada);
    if(rotaInicio != undefined) rotaInicio.remove();
    removeMarkersRotaCorrigida();
}

function removeMarkersRotaCorrigida(){
    for(var m in markerPositionCorrigido){
        mapa.removeLayer(markerPositionCorrigido[m]);
    }
}

 refreshAtualizaVeiculos(7000);

function fechaOpcoesVeiculo(modulo){
//restarta refresh de atualizacao de posicao veiculos
//FOI COMENTADO HOJE DIA 03/04/2018 KKKK "refreshAtualizaVeiculos"
// refreshAtualizaVeiculos(tempoRefresh);
var moduloTmp = $('#divInformacoesVeiculo').data('modulo');
if (modulo && moduloTmp != modulo)
    return;

//oculta div
$("#divOpcoesVeiculo").hide('slow');
$("#divOpcoesVeiculoListaPosicoes").hide('slow');
//apaga rotas desenhadas, Paradas, Velocidade
if(rotaTemp != undefined){
    mapa.removeLayer(rotaTemp);
}
if(rotaCorrigidaTemp != undefined){
    mapa.removeLayer(rotaCorrigidaTemp);
}
if(rotaInicio != undefined){
    rotaInicio.remove();
}
//mostra todos os veiculos
// for(var a in veiculo){
//     console.log(veiculoM[a])
//     veiculoM[a].addTo(mapa);
// }
}

$(document).on('click', '#divParametroParadas button', function() {
    $('#divParametroParadas button').removeClass('disabled');
    tempoParadaGlobal = $(this).data('parada');
    $(this).addClass('disabled');
    $('.divIconParada').remove();
    if ($('#checkboxParadas').is(":checked")) {
        montaParadas();
    }
});

$(document).on('click', '#checkboxParadas', function() {
    if ($(this).is(":checked")) {
        $(this).attr('checked', true);
        montaDadosOpcoesInfoVeiculos();
        return;
    }else{
        $(".leaflet-popup").hide()
    }

    $(this).attr('checked', false);
    $('.divIconParada').remove();
});

function getParadas() {
    $('#divCarregandoOpcoesPC').show();
// $('.reqListaLoading').show();
$.ajax({
    url:ROOT+'/veiculos/maps/paradas',
    type: 'post',
    data: {
        'placa': $('#checkboxRota').data('placa'),
        'dataIni': $('.data-inicio-pinfo').val()+' '+$('.hora-inicio-pinfo').val(),
        'dataFim': $('.data-fim-pinfo').val()+' '+$('.hora-final-pinfo').val()
    },
    dataType: 'json',
    success: function(retorno) {
        requisicaoParadas = retorno;
        $('#divCarregandoOpcoesPC').hide();
        montaParadas();
    }
});
}

function montaParadas() {
    if (!requisicaoParadas) {
        getParadas();
        return;
    }

    tratamentoValoresVaziosReq(requisicaoParadas);

    var paradas = requisicaoParadas;
    for (var i in paradas) {
      var html = '';
        if (parseInt(paradas[i].segundos) >= parseInt(tempoParadaGlobal)) {
          var placa = paradas[i].placa;
          var parada = paradas[i]
          var clas = '';
          var bt = '';
          $.post(ROOT+'/clientes/pontos',{placa:placa}, function(dados){
            var pontos = dados.pontos
            if(pontos.length > 0){
              $('.associar-a-ponto').hide();
            }else{
              $('.cadastrar-novo-ponto').addClass('sozinho');
            }
          })
          html = '<div class="placa-parada">'+
                  '<span>'+ parada.resumido +'</span>'+
              '</div>';
          var icone = new  L.divIcon({
                          className: "divIconParada",
                          html: html
                      });

          var data = parada.data.split(' ');

          var htmlPopup =
              '<div>' +
                  '<div style="color: #ff0000"><strong>Informações da Parada</strong></div>' +
                  '<div><strong>Parou as:</strong> ' + data[0].substr(0, 5) + ' ' + data[1].substr(0, 5) + '</div>' +
                  '<div><strong>Ficou parado:</strong> ' + parada.diferenca +'</div>' +
                  '<div><strong>Em:</strong> ' + parada.endereco + '</div>' +
                  '<div class="bt-acoes">'+
                  '<a data-id="'+parada.placa+'" data-lat="'+parada.lat+'" data-lng="'+parada.lng+'" href="#" class="associar-a-ponto">Associar a um ponto</a>'+
                  '<a href="#" data-id="'+parada.placa+'" data-lat="'+parada.lat+'" data-lng="'+parada.lng+'" class="'+clas+' cadastrar-novo-ponto">Cadastrar novo ponto</div>'+
              '</div>';

          new L.marker([parada.lat, parada.lng], {
              icon: icone
          }).bindPopup(htmlPopup).addTo(mapa);


        }
    }
}


function getPosicoesLista() {
    var loading = "<div id='divCarregandoOpcoesPC' style='display: block'>"
    +"<div>"
    +"<span class='fa fa-spinner fa-spin fa-3x fa-fw'></span>"
    +"Carregando..."
    +"</div>"
    +"</div>";
    $('#divResultadoDentroOpcoesVeiculos').html(loading);
// $('.reqListaLoading').show();
// $('#divCarregandoOpcoesPC').show();
$.ajax({
    url:ROOT+'/veiculos/maps/listaPosicoes',
    type: 'post',
    data: {
        'placa': $('#checkboxRota').data('placa'),
        'dataIni': $('.data-inicio-pinfo').val()+' '+$('.hora-inicio-pinfo').val(),
        'dataFim': $('.data-fim-pinfo').val()+' '+$('.hora-final-pinfo').val()
    },
    dataType: 'json',
    success: function(retorno) {
        requisicaoListaPosicoes = retorno;
// $('#divCarregandoOpcoesPC').hide();
montaTabelaInfoVeiculos();
}
});
}

function montaDadosOpcoesInfoVeiculos() {
// montaTabelaInfoVeiculos();

if ($('.heckboxRota').is(":checked")) {
    if($('.data-inicio-pinfo').val() != '' && $('.hora-inicio-pinfo').val() != '' && $('.data-fim-pinfo').val() != '' && $('.hora-fim-pinfo').val() != ''){
        montaRota();
    }else{
        $('.heckboxRota').prop('checked',false);
    }
}

if ($('.checkboxTracoCorrigida').is(":checked")) {
    if($('.data-inicio-pinfo').val() != '' && $('.hora-inicio-pinfo').val() != '' && $('.data-fim-pinfo').val() != '' && $('.hora-fim-pinfo').val() != ''){
        montaRastroCorrigido();
    }else{
        $('.checkboxTracoCorrigida').prop('checked',false);
    }
}

if ($('#checkboxParadas').is(":checked")) {
    montaParadas();
}

if ($('#checkboxVelocidade').is(":checked")) {
    mostraExcessoVelocidade();
}

if ($('#checkboxAcPortas').is(":checked")) {
    montaPortas();
}
}

function limpaVariaveisRequisicao() {
    requisicaoListaPosicoes = null;
    requisicaoRotas = null;
    requisicaoRastroCorrigido = null;
    requisicaoParadas = null;
    requisicaoExcessosVelocidades = null;
    requisicaoPortas = null;
}

function montaTabelaInfoVeiculos() {
    if (!requisicaoListaPosicoes) {
        getPosicoesLista();
        return;
    }
//limpa lista rotas
$("#divResultadoDentroOpcoesVeiculos").html("");

var dados = requisicaoListaPosicoes.array;
$('#divOpcoesVeiculo .bg-warning').remove();
if (!dados || dados.length == 0) {
    listaPosicaoAberta = 1;
    $('#divResultadoDentroOpcoesVeiculos')
    .html('<div class="alert alert-warning alert-dismissible" style="width: 100%; margin-top: 5px; text-align: center;" role="alert">Nenhum dado encontrado!<div>')
    return;
}

if (!$(document).hasClass('table-infoVeiculo')) {
    var x = 0;
    var y = 0;
    var kmAcumulado = 0;
    var table = "<table class='table table-infoVeiculo table-hover'>"
    + "   <thead>"
    + "       <th>Data/Hora</th>"
    + "       <th>Endereço</th>"
    + "       <th>Velocidade</th>"
    + "       <th title='Quilometros acumulados'>Kms</th>"
    + "   </thead>"
    + "   </tbody>";

var legenda = "<div class='legenda-resultado-veiculo'>"//margin-left:5px;
+"<span style='background-color:#dff0d8; border: 1px solid #5CB85C;'></span>"
+"<div>Em movimento</div>"
+"</div>"
+"<div class='legenda-resultado-veiculo'>"
+"<span style='background-color:#ebcccc; border: 1px solid #D9534F;'></span>"
+"<div>Paradas</div>"
+"</div>"
+"<div class='legenda-resultado-veiculo'>"
+"<span style='background-color:#faf2cc; border: 1px solid #F0AD4E;'></span>"
+"<div>Excessos de Velocidade</div>"
+"</div>";
//monta lista de posicoes e prepara dados para montar rota
$("#divResultadoOpcoesVeiculos").show('slow');
$("#legenda-resultado-veiculo").html(legenda);
// $("#inputPaginacaoOpcoesVeiculoMax").val(parseInt(dados.length/10));//seta valor maximo de paginacao

var dadosLength = dados.length;

while (x < dadosLength) {
//eixo de rota destacada
y = (y == (dadosLength - 1) ? x-1 : x+1);

//calcula kms
kmAcumulado += dados[y].hodometro - dados[x].hodometro;

table += "<tr class='" + (dados[x].velocidade > excessoVelocidadeGlobal ? 'warning' : (dados[x].movimento == 0 ? 'danger' : 'success')) + "'>"
+ "  <td>" + dados[x].data + "</td>"
+ "  <td>" + dados[x].endereco + "</td>"
+ "  <td>" + dados[x].velocidade + " Kms/h</td>"
+ "  <td>" + (kmAcumulado ? kmAcumulado + ' Kms' : '') + "</td>"
+ "  <td class='" + (dados[x].movimento == 0 || dados[x].velocidade > excessoVelocidadeGlobal ? 'parada-excesso-vel' : 'destaca-trecho') + "' data-trecho='" + dados[x].posicao + "," + dados[y].posicao + "'>"
+ "      <span class='glyphicon glyphicon-screenshot'></span>"
+ "  </td>"
+ "</tr>";
x++;
}//fim while

table += "  </tbody>"
+ "</table>";
//insere dados tabela
$("#divResultadoDentroOpcoesVeiculos").html(table);

$('.destaca-trecho').click(function(){
    var latlog = $(this).attr('data-trecho').split(',')
    destacaTrecho(latlog[0], latlog[1], latlog[2], latlog[3]);
});

$('.parada-excesso-vel').click(function(){
    var latlog = $(this).data('trecho').split(',');
    mapa.flyTo({lat: parseFloat(latlog[0]), lon: parseFloat(latlog[1])}, 18);
});
// $('.reqListaLoading').hide();
}
}

function montaRota() {
    if (!requisicaoRotas) {
        desenhaRota();
        return;
    }
    removeRotas();
    var dados = requisicaoRotas.array;

    tratamentoValoresVaziosReq(dados);

    posicoes = "[";
    var posicoesTmp = '';

    for (var cr in dados) {
        posicoesTmp += "[" + dados[cr].posicao + "],";
    }

    posicoes += posicoesTmp.substring(0,(posicoesTmp.length - 1));
    posicoes += "]";

    rotaTemp = L.polyline.antPath(JSON.parse(posicoes),{
        delay: 1500
    }).addTo(mapa);
    mapa.flyToBounds(rotaTemp.getBounds());

    var iconInicio = L.icon({
        iconUrl: './img/novoPonto.png',
        className: 'iconRotaInicio'
    });
    var posicaoInicio = "["+dados[0].posicao+"]";
    rotaInicio = L.marker(
        JSON.parse(posicaoInicio),{
            icon: iconInicio
        }).addTo(mapa);
}

function montaRastroCorrigido() {
    if (!requisicaoRastroCorrigido) {
        desenhaRotaCorrigida();
        return;
    }
    removeRotas();
    var dados = requisicaoRastroCorrigido.posicoes;

    tratamentoValoresVaziosReq(dados);

    posicoes = "[";
    var posicoesTmp = '';

    for (var cr in dados) {
        posicoesTmp += "[" + dados[cr].posicao + "],";
    }

    posicoes += posicoesTmp.substring(0,(posicoesTmp.length - 1));
    posicoes += "]";


    rotaCorrigidaTemp = L.polyline.antPath(requisicaoRastroCorrigido.posicoes_corrigidas,{
        delay: 1500
    }).addTo(mapa);


    mapa.flyToBounds(rotaCorrigidaTemp.getBounds());

    mapa.on("zoomend", function (e) {
        if(requisicaoRastroCorrigido != null){
            setmarkersRotaCorrigida(requisicaoRastroCorrigido.posicoes);
        }
    });
    mapa.on("moveend", function (e) {
        if(requisicaoRastroCorrigido != null){
            setmarkersRotaCorrigida(requisicaoRastroCorrigido.posicoes);
        }
    });

//mostra marker inicio rota
var iconInicio = L.icon({
    iconUrl: './img/novoPonto.png',
    className: 'iconRotaInicio'
});
var posicaoInicio = "["+dados[0].posicao+"]";

rotaInicio = L.marker(
    JSON.parse(posicaoInicio),{
        icon: iconInicio
    }).addTo(mapa);
}

function setmarkersRotaCorrigida(dados){
    if(!$('.heckboxRota').is(":checked")){
        if(mapa.getZoom() <= 18){
            removeMarkersRotaCorrigida();
        }else{
            var micon = L.icon({
                iconUrl: './img/marker_rastro_corrigido.png',
                className: 'divIconRastroCorrigido',
                iconAnchor: [10, 10],
            });
            dados.forEach(function(dado) {
                var position = dado.posicao.split(',');
                if(mapa.getBounds().contains(L.latLng(position[0],position[1]))){
                    var data = new moment(dado.data);
                    markerPositionCorrigido.push(L.marker([position[0],position[1]],{icon: micon}).addTo(mapa).bindPopup('<strong>'+data.format('DD/MM/YYYY h:mm:ss')+'</strong>'));
                }
            });
        }
    }
}

$('.select-empresa-painel').change(function(){

    if ($(this).val()[0] == 0) {
        $(this).find('option').prop("selected",true);
        $(this).find('.option-todos').prop("selected",false);
        $(this).select2({
            "language": "pt-BR"
        });
    }
    var id = $(this).val();
    if(id.length < 0){
        id = ['0'];
    }
    $.post(ROOT+'/veiculos/maps/atualiza/painel',{id:id}, function(dados){
        setTimeout(function(){
            if(Object.keys(veiclClusters).length > 0) {
                removeClusterMarkers()
            }
            veiculo = dados.modulos
            todasPermissoes = dados.permissoes
            addVeiculosChangePainel(veiculo)
            dadosVeiculosPC(id, todasPermissoes);
            mapa.closePopup();
            $('#checkboxVisualizaRegioes').trigger('change');
        }, 600);
    })
})

function removeClusterMarkers() {
    try{
        for(var i in veiclClusters) {
            mapa.removeLayer(veiclClusters)
        }
    }catch(err){}
    return false;
}

$(".btn-cluster").click(function(){
    if($(".btn-cluster").is(':checked')) {
        agrupaMarkers = 20
    }else{
        agrupaMarkers = 1
    }
    $(".select-empresa-painel").trigger('change');
    busca_pontos_inicial();
})

function addVeiculosChangePainel(veicl) {
    if(typeof veicl != 'undefined') {
        markerClusterVeiculo(veicl)
    }
}

function markerClusterVeiculo(veicl){
    veiclClusters = L.markerClusterGroup({
        disableClusteringAtZoom: agrupaMarkers,
        removeOutsideVisibleBounds: true,
        iconCreateFunction: function(cluster) {
            if(cluster.getChildCount() < 10) {
                var colore = "colore-verde"
            }else if(cluster.population < 100){
                var colore = "colore-amarelo"
            }else{
                var colore = "colore-vermelho"
            }
            return L.divIcon({ html: '<div id="'+y+'" class="custom-cluster '+colore+'"><span class="fa fa-asterisk trucc '+colore+'"></span><span class="qtd-car">' + cluster.getChildCount() + '</span></div>' });
        }
    });
    var marker = [];
    for(var y in veicl){
        try {

            marker = L.marker( [veicl[y].lat,veicl[y].lng], {
                icon: preparaIcon(veicl[y].tipo, veicl[y].direcao, veicl[y].prefixo,veicl[y].modulo,veicl[y].ignicao),
                info: y,
            } )

        }catch(err) {
            console.log("Error atualizaçao de markers!");
        }
        veiclClusters.addLayer( marker );
    }
    mapa.addLayer( veiclClusters );
}


//Função para carregar os motoristas do cliente na alteraçao de motorista e/ou ajudante!
function carregaMotoristas(cliente) {
    if(cliente.length > 0)
    {
        $.ajax({
            url:ROOT+'/veiculos/maps/listarMotoristas',
            type: 'get',
            dataType: 'json',
            data: {
                'cliente': cliente
            },
            success: function(retorno){
                var clienteMotoristas = [];
                $('.td-motorista-painel-controle').each(function(index, element) {
                    var motoristasOptions = '';
                    var ajudantesOptions = '';
                    var codigoCliente = retorno[$(element).data('cliente')];
                    for (var i in codigoCliente) {
                        if(codigoCliente[i].mtperfil.indexOf("M") != -1){
                            if (codigoCliente[i].mtcodigo != $(element).data('mot')){
                                motoristasOptions += '<option  value="' + codigoCliente[i].mtcodigo + '">' + codigoCliente[i].mtnome + '</option>';
                            }
                        }
                        if(codigoCliente[i].mtperfil.indexOf("A") != -1){
                            if (codigoCliente[i].mtcodigo != $(element).data('aju')){
                                ajudantesOptions += '<option  value="' + codigoCliente[i].mtcodigo + '">' + codigoCliente[i].mtnome + '</option>';
                            }
                        }
                    }
                    $(element).find('.select-mot-painel').append(motoristasOptions);
                    $(element).find('.select-aju-painel').append(ajudantesOptions);
                });
            }
        });
    }
}

function checkDisponibilidadeMA(cod, nome) {
    if(cod > 0){
        $.ajax({
            url:ROOT+'/veiculos/maps/checkDisponibilidadeMA',
            type: 'get',
            dataType: 'json',
            data: {
                'cod': cod
            },
            success: function(retorno){
                var veiculo = retorno.veiculo;
                var cod = retorno.cod;
                var string = "";

                if(veiculo != null  ){
//saber se é motorista ou ajudanto no veículo em que está associado
if(veiculo.vemotorista != null && veiculo.vemotorista == cod){
    isMotoristaPainel = true;
    string = "motorista";
    $("#modalClean .modal-body .mot-notfication").html('<div class="alert-warning col-sm-12"> '+nome+' está associada ao veículo '+ veiculo.veprefixo +' como '+string+'</div>' );
}else{
    isMotoristaPainel = false;
    string = "ajudante";
    $("#modalClean .modal-body .aju-notfication").html('<div class="alert-warning col-sm-12"> '+nome+' está associada ao veículo '+ veiculo.veprefixo +' como '+string+'</div>' );
}

$('.mot-invisible').addClass('invisible');

bts = '';
bts += '<button data-veiculo="'+veiculo.vecodigo+'" type="button"\
class=" save-alteracao-mot-aju confl-invisible  btn btn-warning"\
title="Esta ação vai desassociar o motorista/ajudante do veículo atual!">Desassociar e Salvar  \
</button>';
bts += '<button type="button" class="confl-invisible  btn btn-danger" data-dismiss="modal">Cancelar <span class="glyphicon glyphicon-remove"></button>';
$("#modalClean .modal-footer").html(bts);
}

datatableForPainelControle();
}
});
    }
}

function getAcionamentoPortas() {
// $('.reqListaLoading').show();
$.ajax({
    url:ROOT+'/veiculos/maps/acionamentoPortas',
    type: 'post',
    data: {
        'placa': $('#checkboxRota').data('placa'),
        'dataIni': $('.data-inicio-pinfo').val()+' '+$('.hora-inicio-pinfo').val(),
        'dataFim': $('.data-fim-pinfo').val()+' '+$('.hora-final-pinfo').val()
    },
    dataType: 'json',
    success: function(retorno) {
        requisicaoPortas = retorno;
        $('#divCarregandoOpcoesPC').hide();
        montaPortas();
    }
});
}

function montaPortas() {
    var portas = {
        14: 1,
        16: 2,
        18: 3,
        20: 4
    };
    if (!requisicaoPortas) {
        getAcionamentoPortas();
        return;
    }
// $('.reqListaLoading').hide();
var acPortas = requisicaoPortas.array;
tratamentoValoresVaziosReq(acPortas);

for (var i in acPortas) {
    if (acPortas[i].bimotivotransmissao == portaGlobal) {
        var html =
        '<div>'+
        '<span class="fa fa-unlock"></span>'+
        '</div>';
        var icone = new  L.divIcon({
            className: "divIconPorta",
            html: html
        });
        var latlog = acPortas[i].posicao.split(',');

        var htmlPopup =
        '<div>' +
        '<div style="color: #ff0000"><strong>Informações sobre acionamento de porta</strong></div>' +
        '<div><strong>Porta:</strong> ' + portas[portaGlobal] + '</div>' +
        '<div><strong>Abriu as:</strong> ' +acPortas[i].data+'</div>' +
        '<div><strong>Em:</strong> ' + acPortas[i].endereco + '</div>' +
        '</div>';

        new L.marker([latlog[0], latlog[1]], {
            icon: icone
        }).bindPopup(htmlPopup).addTo(mapa);
    }
}
}

$(document).on('click', '#divParametroAcPortas button', function() {
    $('#divParametroAcPortas button').removeClass('disabled');
    portaGlobal = $(this).data('porta');
    $(this).addClass('disabled');
    $('.divIconPorta').remove();
    mapa.closePopup();
    if ($('#checkboxAcPortas').is(":checked")) {
        montaPortas();
    }
});

$(document).on('click', '#checkboxAcPortas', function() {
    if ($(this).is(":checked")) {
        $(this).attr('checked', true);
        montaDadosOpcoesInfoVeiculos();
        return;
    }

    $(this).attr('checked', false);
    $('.divIconPorta').remove();
    mapa.closePopup();
});


$(document).on('click', '.associar-a-ponto', function(e){
  e.preventDefault();
  var pop = $(this).parents('.leaflet-popup-content');
  var old = $(pop).html();
  var placa = $(this).data('id');
  var lat = $(this).data('lat');
  var lng = $(this).data('lng');

  $.post(ROOT+'/clientes/pontos',
    {
        placa:placa,
        lat:lat,
        lng:lng,
    }, function(dados){
    var pontos = dados.pontos;
    var opt = '';
    for(var i in pontos){
      opt += '<option value="'+pontos[i].pocodigo+'">'+pontos[i].podescricao+' ('+parseInt(pontos[i].distancia*1000)+'m)</option>'
    }
    var select = '<form action="'+ROOT+'/painel/cadastros/pontos/reassociar">'
    select += '<label>Selecione um ponto já existente:</label>'
    select += '<select class="form-control select-ponto" name="pocodigo">'+opt+'</select>';
    select += '<input type="hidden" value="'+lat+'" name="polatitude">'
    select += '<input type="hidden" value="'+lng+'" name="polongitude">'
    select += '<div class="block-bts">';
    select += '<button href="#" class="associar-ponto btn btn-sm">Salvar</button>';
    select += '<button href="#" class="cancelar-associacao btn btn-sm">Cancelar</button>';
    select += '</div>';
    select += '<form>';

    $(pop).html(select);

    $('.select-ponto').select2({
      language: "pt-BR",
      // dropdownParent: $('#painelControleTabela'),
      allowClear: true,
    });
    $('.cancelar-associacao').click(function(e){
      e.preventDefault();
      $(pop).html(old);
    })
  })

})


$(document).on('click', '.cadastrar-novo-ponto', function(e){
  e.preventDefault();
  var pop = $(this).parents('.leaflet-popup-content');
  var old = $(pop).html();
  var lat = $(this).data('lat');
  var lng = $(this).data('lng');
  var placa = $(this).data('id');
  $.post(ROOT+'/clientes/parada',{placa:placa}, function(dados){
    var clientes = dados.clientes;
    var opt = '';
    for(i in clientes){
      opt += '<option value="'+clientes[i].clcodigo+'">'+clientes[i].clfantasia+'</option>'
    }
    var option = '<option value="C">Ponto de Coleta</option>';
    option += '<option value="E">Ponto de Entrega</option>';
    option += '<option value="P">Referência</option>';

    var formCad = '<form action="'+ROOT+'/painel/cadastros/pontos/save">'
    formCad += '<select class="form-control select-ponto" name="veproprietario">'+opt+'</select>';
    formCad += '<input type="text" name="descricao" placeholder="Descrição*" class="descricao-novo-ponto form-control">'
    formCad += '<input type="hidden" value="'+lat+'" name="cllatitude">'
    formCad += '<input type="hidden" value="'+lng+'" name="cllongitude">'
    formCad += '<input type="hidden" value="" name="pocodigo">'
    formCad += '<select class="form-control" name="tipo">'+option+'</select>';
    formCad += '<input id="raioPonto" type="range" data-lat="'+lat+'" data-lng="'+lng+'" name="clraio" class="raio-pontos" value="50" min="10" max="200">';
    formCad += '<span class="mtsRaio">50 Mts</span>';
    formCad += '<span class="text-danger hidden campo-obrigatorio">O campo descrição é obrigatório</span>'
    formCad += '<div class="block-bts">';
    formCad += '<button href="#" class="hidden associar-ponto btn btn-sm">Salvar</button>';
    formCad += '<button href="#" class="cancelar-associacao btn btn-sm">Cancelar</button>';
    formCad += '</div>';

    $(pop).html(formCad);

    $('.select-ponto').select2({
      language: "pt-BR",
      allowClear: true,
    });
    $('.cancelar-associacao').click(function(e){
      e.preventDefault();
      $(pop).html(old);
      if(raioParada != null) mapa.removeLayer(raioParada);
    })
    $(".raio-pontos").change(function(e){
        e.preventDefault()
        var raio = $(this).val();
        $('.mtsRaio').html(raio+' Mts')
        if(raioParada != null) mapa.removeLayer(raioParada);
        raioParada = L.circle([$(this).data('lat'),$(this).data('lng')],{
                radius: raio,
                className: 'iconCircle',
            }
        );
        mapa.addLayer(raioParada);
    })
    $(".raio-pontos").trigger('change');
  })
})

$(document).ready(function(){
    mapa.on('popupclose', function (e) {
        if(raioParada != null) mapa.removeLayer(raioParada);
    });
})

$(document).on('click', '.associar-ponto', function(e){
  e.preventDefault();
  var thad = $(this);
  var form = $(thad).parents('form');
  $(form).ajaxForm({
    type:'post',
    success: function(dados) {
        var pop = $(thad).parents('.leaflet-popup-content');
        $(pop).html('<span class="msg-success">Ponto salvo com sucesso!</span>')
        if(raioParada != null) mapa.removeLayer(raioParada);
    },
    beforeSend: function() {
        $(thad).append(`<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i><span class="sr-only"></span>`);
        $(thad).attr('disabled',true);

    },
  }).submit();
})

$(document).on('keyup', '.descricao-novo-ponto', function(){
    var valor = $(this).val();
    if(valor.length > 0){
        $('.associar-ponto').removeClass('hidden');
    }else{
        $('.associar-ponto').addClass('hidden');
    }
})

// console.log($(this).val())
// $(document).on('click', '.bloqueio-veiculo', bloqueiaVeiculo)

function bloqueiaVeiculo(val, password) {
// if ($('.bloqueio-veiculo').is(':checked')) {
//     var val = 3
// } else {
//     var val = 0
// }
var modelo = $('.bloqueio-veiculo').data('modelo');
var modulo = $('.bloqueio-veiculo').data('modulo');
$.post(ROOT+'/veiculos/maps/bloqueio',
{
    val:val,
    modelo: modelo,
    modulo: modulo,
    password: password
},
function(dados){
    $('#divPasswordBlock').siblings().remove();
    $('#modalAlerta .modal-body').removeClass('text-danger');
    if (dados.erro) {
        $('#btnConfirmarSenhaConfirm').attr('disabled', false);
        $('#modalAlerta .modal-body').addClass('text-danger');
        $('#divPasswordBlock').css('color', '#636b6f')
        .parent().addClass("form-group has-error")
        .append('<div id="divErroBlock">'+dados.erro+'</div>');
        return;
    }

    if (val == 3) {
        $('.clickSolicitaSenhaContinuar input').prop('checked', true);
    } else {
        $('.clickSolicitaSenhaContinuar input').prop('checked', false);
    }

    $('#iptCheckboxBloqueio').data('bloqueio', val);

    var alert = '<div style="display:table; float: left;" class="alert alert-success col-md-12" role="alert">'
    +'<label class="text-success">'+dados.mensagem+'</label>'
    +'</div>';

    $('#divPasswordBlock').parent().removeClass("form-group has-error")
    .append(alert);
    setTimeout(function() {
        $('#modalAlerta').modal('hide');
    }, 1500);
})
}

function datatableForPainelControle() {
    var rowCount = $('#tabelaPainelControle tbody tr').length;
    if(rowCount > 1){
        if ($.fn.DataTable.isDataTable('#tabelaPainelControle')){
            $('#tabelaPainelControle').DataTable().destroy();
        }

        $('#tabelaPainelControle').DataTable({
            paging: false,
            responsive: true,
            info: false,
            scrollY: 200,
            scrollCollapse: true,
            processing: true,
            stateSave: true,
            destroy: true,
            autoWidth: false,
            aoColumnDefs: [{
                'bSortable': false,
                'aTargets': [ 0, 4, 7 ]
            },],
            order: [[ 1, "asc" ]],
            language: {
                search: "Buscar:",
                searchPlaceholder: "Buscar na tabela",
                sZeroRecords: "Nada encontrado"
            }, columns: [
            { "width": "10%"},
            { "width": "5%"},
            { "width": "5%"},
            { "width": "15%"},
            { "width": "5%"},
            { "width": "10%"},
            { "width": "20%"},
            { "width": "5%"},
            { "width": "20%"}
            ]
        });
    }
}

var dateSelect = getDate
var $myGroup = $('#painelControle');
$myGroup.on('show','.collapse', function() {
    $myGroup.find('.collapse.in').collapse('hide');
});

$('.rotas-empresas-select-painel').on('select2:select select2:unselect',function(){
    if ($(this).val()[0] == 0) {
        $(this).find('option').prop("selected",true);
        $(this).find('.option-todos').prop("selected",false);
        $(this).select2({
            "language": "pt-BR"
        });
    }
    var id = $(this).val();
    if(id.length < 0){
      id = ['0'];
    }

     ajaxGetRotas()
})

$("#rotasPC").click(function(){
    if($(".paramRotas").hasClass("mostra")){
        $(".paramRotas").removeClass("mostra");
    }else{
        $(".paramRotas").addClass("mostra");
    }

    var today = new Date().toISOString().slice(0, 10);
    var years = today.substring(0,4)
    var month = today.substring(5,7)
    var day = today.substring(8,10)
    var date = day+'/'+month+'/'+years
    var veiculo = null;
});

$('#botaoPainelControle, #rotasPC').on('click',function(){

    if(firstVezes){
        rotas = $('.rotas-empresas-select-painel').val();
        painel = $('.select-empresa-painel').val();
        firstVezes = false;
    }

    if(isFirstAjax){
        isFirstAjax = false;
    }
    if($(this).find('.fa').hasClass('fa-chevron-right')){
        $(this).find('.fa').removeClass('fa-chevron-right');
        $(this).find('.fa').addClass('fa-chevron-down');
    }else{
        $(this).find('.fa').removeClass('fa-chevron-down');
        $(this).find('.fa').addClass('fa-chevron-right');
    }

            // #A9A9A9

    if($(this).attr('aria-expanded') === "false"){
        // $(this).css('background-color', '#A9A9A9');
        if($(this).attr("id") == 'botaoPainelControle'){
            if($('#rotasPainelCollapse').attr('aria-expanded') === "true"){
                $('#rotasPainelCollapse').collapse('toggle');
            }
        }else if( $(this).attr("id") == 'rotasPC'){
            if($('#painelControleCollapse').attr('aria-expanded') === "true"){
                $('#painelControleCollapse').collapse('toggle');
           }
        }
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        var yyyy = today.getFullYear();
    }

    if($(this).attr('aria-expanded') === "false" && table_cont <= 0) {
        table_cont++;
        dadosVeiculosPC();
    }

    if($(this).attr("id") == 'botaoPainelControle'){
        $(document).ready(function(){
            isRequest = false
            if($('#painelControleCollapse').attr('aria-expanded') === "true"){
                $('#botaoPainelControle').removeClass('btn-dark')
                $('#botaoPainelControle').addClass('btn-primary')
                $('#rotasPC').removeClass('btn-primary')
            }else{
                $('#botaoPainelControle').removeClass('btn-primary')
                $('#rotasPC').removeClass('btn-primary')
                $('#botaoPainelControle').addClass('btn-dark')
                $('#rotasPC').addClass('btn-dark')
            }
            clearAllRotasMapa()
        })
         if(!isPainel){
            $('#checkboxTbPc').trigger('change').prop('checked',true);
                $(".select-empresa-painel").val(rotas).trigger('change');
                $('.checkboxTbPc:not(:checked)').each(function(index, element) {
                $(element).trigger('click')
                removeClusterMarkers()
                var table = $('#tableRotas').DataTable();
                table.clear().draw();
            });
            isPainel = true;
        }
        isRotas = false;
    }

    if($(this).attr("id") == 'rotasPC'){
        //seleciona todos os veiculos novamente
        $(document).ready(function(){
            if(!isRequest){
                $('.checkView').prop('checked',false)
                isRequest = true
                isFirstAjax = false;
            }
            if($('#rotasPC').attr('aria-expanded') === "true"){
                $('#rotasPC').removeClass('btn-dark')
                $('#rotasPC').addClass('btn-primary')
                $('#botaoPainelControle').removeClass('btn-primary')
            }else{
                $('#rotasPC').removeClass('btn-primary')
                $('#botaoPainelControle').removeClass('btn-primary')
                $('#rotasPC').addClass('btn-dark')
                $('#botaoPainelControle').addClass('btn-dark')
            }
        })
         if(!isRotas){
            $(".rotas-empresas-select-painel").val(painel).trigger('change');
            $('#endDate').datepicker('setDate',getDate())
            $('#endDate').datepicker('hide');
            $('#endDate').trigger('click');
            $('.checkView').attr('checked',false)
            dateSelect = getDate();
            isRotas = true;
            removeClusterMarkers()
         }
        isPainel = false;
    }
})


//FUNÇÃO QUE RETORNA A DATA
function getDate(){
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //January is 0!
    var yyyy = today.getFullYear();

    if(dd<10) {
        dd = '0'+dd
    }

    if(mm<10) {
        mm = '0'+mm
    }

    today = dd + '/' + mm + '/' + yyyy;
    return today;
}

// function showVeiculos(){
//     console.log('entrou')
//     $('.checkboxTbPc').prop('checked', true);
//     $('.checkboxTbPc').each(function(index, element) {
//         var modulo = $(element).data('modulo');
//         $('#'+modulo).parent().show();
//         console.log(modulo)
//         $($('.' + modulo)[0]).find('.bloqueiaLatLngGoTo').remove();
//     })
// }

$('.rotas-empresas-select-painel').select2({width: '10%'})

var isfirst = true;
$('#endDate').click(function(){
    isfirst = true;
})

$('.rotas-empresas-select-painel').select2({width: '10%'})

var isfirst = true;
$('#endDate').click(function(){
    isfirst = true;
})

var i = 0;
setInterval(ajaxGetRotas,500000);

$('#endDate').datepicker({
    format: "dd/mm/yyyy",
    autoclose: true,
    language: 'pt-BR'
}).change(function(){
    if(isfirst){
        var date = $(this).val();
        var years = date.substring(6,10)
        var month = date.substring(3,5)
        var day = date.substring(0,2)
        var mydate = years+'-'+month+'-'+day
        if($('#endDate').val() != ""){
            clearAllRotasMapa()
            //hideVeiculosRotaAll()
            dateSelect = mydate;
            $('.checkView').prop('checked',true);
            ajaxGetRotas()
            listaCheck = new Set();
            $('.checkView').prop('disabled',true);
        }
        isfirst = false;
    }
})


function clearAllRotasMapa(){
    var excluir = null;
    setRocodigo.forEach(function(data){
          listaRotasMarcada.forEach(function(value){
        if(value.rocodigo == data){
            mapa.removeLayer(value.polyline)
                excluir = value
            }
        })
        removeMarkerForRotas(data)
    })

    listaRotasMarcada.delete(excluir)
}

function showVeiculosRota(){
    $('.checkView').each(function(index, element) {
        var modulo = $(element).data('modulo');
        if($(element).is(':checked')){
            $('#'+modulo).parent().show();
            $($('.' + modulo)[0]).find('.bloqueiaLatLngGoTo').remove();
        }
    })
}



function createPoliLyne(lngLong,codigo,cor,thad){
//format "var latlngs = [[45.51, -122.68]];"
    $.ajax({
        type: 'post',
        url: ROOT+'/defineRota',
        data: {latLong : lngLong},
        success : function(data){
            $(thad).prop('disabled',false);
            if(data.response[0].code != undefined){
                var polyString = data.response[0]['routes'][0].geometry;
                console.log('poly ',polyString)
                if(cor == null){
                    cor = geraCor();
                }

                setRocodigo.add(codigo)
                var decode = decodePoly.decode(polyString);
                var polyline = L.polyline.antPath(decode,{
                    delay: 1500,
                    weight: 12,
                    color : cor
                });
                polyline.addTo(mapa)
                mapa.flyToBounds(polyline.getBounds())
                listaRotasMarcada.add({"rocodigo":codigo,"polyline":polyline})
                var i = 0;
                var setItensRotasInMarker = new Set()
                getItensRotas(codigo).forEach(function(response){
                if(response.irstatus != 'P' && response.irstatus != 'F'){
                    var marker = createMarkerRotas(response.ircodigo,cor,response.irordem,lngLong[i],response,true);
                }else{
                    var marker = createMarkerRotas(response.ircodigo,cor,response.irordem,lngLong[i],response,false);
                }
                getAllLatLng(getItensRotas(codigo));
                setItensRotasInMarker.add(marker)
                i++;
                })
                console.log(codigo)
                setRotasMarker.add({"rocodigo" : codigo,"marker" : setItensRotasInMarker})
            }
        },
        error : function(data){
            $(thad).prop('disabled',false);
        }
    })
}

function removeMarkerForRotas(idRota){

    var marker;
    var i = 0;
    setRotasMarker.forEach(function(data){
        if(idRota == data.rocodigo){
            data.marker.forEach(function(dato){
                console.log(dato)
                mapa.removeLayer(dato)
            })
        }
    })
}

function getAllLatLng(itensRotas){
    var setLatLng = new Set();
    itensRotas.forEach(function(data){
        setLatLng.add();
    })
    return setLatLng;
}

function hexToRgbA(hex,opacity){
    var c;
    if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
        c= hex.substring(1).split('');
        if(c.length== 3){
            c= [c[0], c[0], c[1], c[1], c[2], c[2]];
        }
        c= '0x'+c.join('');
        return 'rgba('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+','+opacity+')';
    }
    throw new Error('Bad Hex');
}
