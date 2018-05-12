$ajusteBtnSalvarImportacao = function() {
    $('#gravarImportacao').css({'display': 'block', 'float': 'left'});
    $('.fa-spinner-tmp').remove();
}
novoPonto = 0;
novoPontoRaio = 0;
var data = moment().format('LLL');


$('.telefone').mask("(99) 9999-9999?9");
$('.cpf').mask('999.999.999-99');
$('.cnpj').mask('99.999.999/9999-99');
$(".placa").mask('aaa-9999');
$('.iccid').mask('99999 99999 99999 99999');
$('.cnhnumero').mask('99999999999');
$('.inputData').mask("99/99/9999");
$('#mocodigo').mask('999999?9');
$('#inputIMEI').mask('999999999999999');
$('.input-time').mask('99:99');
$('.input-time-infinit').mask('99:99');

$(".data_hora").timepicker({
	minuteStep: 5,
	showMeridian: false,
	defaultTime: '08:00'
})

$('.input-time').keyup(function() {
    var horas = $(this).val().split('');
    var h1 = /[^0-2]/g;
    var h2 = /[^0-3]/g;
    var m1 = /[^0-5]/g;
    var m2 = /[^0-9]/g;

    if (horas[0].match(h1) != null) {
        $(this).val('');
        return;
    }
    var validaHora = parseInt(horas[0]) != 2 ? (horas[1].match(m2) != null) : (horas[1].match(h2) != null)
    if (validaHora) {
        $(this).val(horas[0]);
        return;
    }
    if (horas[3].match(m1) != null) {
        $(this).val(horas[0] + horas[1] + ':');
        return;
    }
    if (horas[4].match(m2) != null) {
        $(this).val(horas[0] + horas[1] + ':' + horas[3]);
        return;
    }
    $(this).val(horas[0] + horas[1] + ':' + horas[3] + horas[4]);
});

$('select').select2({
    "language": "pt-BR"
});

$(".data-hora").datepicker({
    dateFormat: 'dd/mm/yy',
    dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
    dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
    dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
    monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
    monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
    nextText: 'Próximo',
    prevText: 'Anterior'
});

var feDatepicker = function(){
	if($(".datepicker").length > 0){
		$(".datepicker").datepicker({format: 'yyyy-mm-dd'});
		$("#dp-2,#dp-3,#dp-4").datepicker(); // Sample
	}
}

var feTimepicker = function(){
	// Default timepicker
	if($(".timepicker").length > 0)
		$('.timepicker').timepicker();

	// 24 hours mode timepicker
	if($(".timepicker24").length > 0)
		$(".timepicker24").timepicker({minuteStep: 5,showSeconds: true,showMeridian: false});
}

// Extend the default Number object with a formatMoney() method:
// usage: someVar.formatMoney(decimalPlaces, symbol, thousandsSeparator, decimalSeparator)
// defaults: (2, "$", ",", ".")
//http://www.josscrowcroft.com/2011/code/format-unformat-money-currency-javascript/
Number.prototype.formatMoney = function(places, symbol, thousand, decimal) {
	places = !isNaN(places = Math.abs(places)) ? places : 2;
	symbol = symbol !== undefined ? symbol : "R$";
	thousand = thousand || ".";
	decimal = decimal || ",";
	var number = this,
	    negative = number < 0 ? "-" : "",
	    i = parseInt(number = Math.abs(+number || 0).toFixed(places), 10) + "",
	    j = (j = i.length) > 3 ? j % 3 : 0;
	return symbol + negative + (j ? i.substr(0, j) + thousand : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousand) + (places ? decimal + Math.abs(number - i).toFixed(places).slice(2) : "");
};


// Função para selecionar todos
$('.select-selecionar-todos').change(function() {
    if ($(this).val()[0] == 'T' || $(this).val()[0] == 0) {
        $('option',this).prop("selected",true);
    }
});

$(".money").maskMoney({symbol:'R$ ',
    showSymbol:true, thousands:'.', decimal:',', symbolStay: true});

$(document).on("click", ".add-campo", addCampo);

var click = 1
function addCampo(e) {
    e.preventDefault()
    click++
    var adicionar = $(this).attr('data-parent')
    var mask = $(this).attr('data-mask')
    var name = $(this).attr('data-campo')+'[]'
    var type = $(this).attr('data-type')
    var campos = '<div class="row">'+
                    '<div class="campos-add">'+
                        '<div class="col-xs-1 icon-campo-add">'+
                            '<a href="#" class="remove-campo" title="Remover">'+
                            '<span class="glyphicon glyphicon-minus"></span></a>'+
                        '</div>'+
                        '<div class="col-xs-11">'+
                            '<input type="'+type+'" name="'+name+'" class="form-control '+mask+'">'+
                        '</div>'+
                    '</div>'+
                '</div>'

    $('.'+adicionar).append(campos)
    $('.telefone').mask("(99) 9999-9999?9");
}

$(document).on("click", ".remove-campo", removeCampo);

function removeCampo() {
    $(this).parents('.campos-add').remove()
}

$(document).on("click", "#inputTipoPessoa", pessoaFisicaJuridica);

function pessoaFisicaJuridica() {
    var insEstadual = '<div class="col-sm-10">'+
                          '<div class="col-sm-12 form-group clinscricao-estadual">'+
                                '<label>Nome fantasia</label>'+
                                '<input type="text" name="clfantasia" class="form-control">'+
                            '</div>'
                        '</div>'

    if($('#inputTipoPessoa').is(":checked")) {
        $(".block-cpf label").html('CNPJ*')
        $(".block-rg label").html('Insc. estadual')
        // $(".dados-cliente").append(insEstadual)
        $(".cpf").addClass('cnpj').removeClass('cpf')
        $(".label-nome-cl").html('Razão Social*')
        $(".nome-fantasia").show()
    }else {
        $(".block-cpf label").html("CPF*")
        $(".block-rg label").html("RG")
        $('.cnpj').addClass('cpf').removeClass('cnpj')
        $(".clinscricao-estadual").remove()
        $(".label-nome-cl").html('Nome*')
        $(".nome-fantasia").hide()
    }
    $('.cpf').mask('999.999.999-99');
    $('.cnpj').mask('99.999.999/9999-99');
}

$(document).ready(function(){
    var cadCliente = $("#cadastroCliente");
    cadCliente.each(function(index, element){
        carregaMapa();
        markerEmpresa();
    })
    var cadDispon = $("#mapaPontos");
    cadDispon.each(function(index, element){
        carregaMapa();
        markerEmpresa();
    })
    var cadMot = $("#cadastroMotoristaAjudante");
    cadMot.each(function(index, element){
        carregaMapa();
        markerEmpresa();
    })
    var cadMot = $("#formCadastroModulos");
    cadMot.each(function(index, element){
        carregaMapa();
        markerEmpresa();
    })
})

$(document).on('click', '.ativar-cadastros', ativarCadastros);
$('.ativar-cadastros').on('click', ativarCadastros)

function ativarCadastros(e){
    e.preventDefault();
    var thad = $(this)
    var url = $(this).attr('data-url')
    var id = $(this).attr('data-id');
	$.post(url, {id:id}, function(data){
		$(thad).hide()
		$(thad).siblings('.desativar-cadastros').removeClass('hidden')
        $(".bt-filtros-update.btn-primary").trigger('click');
    })
}


function markerEmpresa() {
	var lat = $(".inputLatitude").val();
	var lng = $(".inputLongitude").val();
	var raio = $(".inputRaio").val();
    var tipo = $('#tipo').val();
    var iniciado = 1;
    var imgPontos = {
                'C': ROOT+'/img/coleta.png', //coleta
                'E': ROOT+'/img/entrega.png', //entrega
                'P': ROOT+'/img/referencia.png', //referencia
            };

    if(lat.length == 0 || lng.length == 0){
        lat = mapa.getCenter().lat;
        lng = mapa.getCenter().lng;
        iniciado = 0;
    }
    if(raio.length == 0)
        raio = 50;


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


        var html = "<span class='bloco'>Me arraste para o local desejado.</span>"+
                 "<span class='bloco'><span class='linha'><strong>Raio:</strong><input id='inputRangeRaio' value='"+raio+"' type='range' min='10' max='200'><span id='metrosRaio'>"+raio+" Mts</span></span></span>";
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
            $(".inputLatitude").val(novoPonto.getLatLng().lat);
            $(".inputLongitude").val(novoPonto.getLatLng().lng);

        });

    }else{
        var icone = new L.icon({
            iconUrl: (tipo ? imgPontos[tipo] : 'https://unpkg.com/leaflet@1.0.3/dist/images/marker-icon.png'),
            iconSize: [34, 34], //34, 34
            iconAnchor: [17, 32],
            popupAnchor: [-1, -30],
        });

        novoPonto = L.marker([lat,lng],{
                        draggable:true,
                        icon: icone
                    }).addTo(mapa);
        novoPontoRaio = L.circle([lat,lng],{
                            radius: raio
                            }
                        );
        mapa.flyTo([lat,lng]);
        var html = "<span class='bloco'>Me arraste para o local desejado.</span>"
                 + "<span class='bloco'><span class='linha'><strong>Raio:</strong><input id='inputRangeRaio' value='"+raio+"' type='range' min='10' max='200'><span id='metrosRaio'>"+raio+" Mts</span></span></span>";
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
            $(".inputLatitude").val(novoPonto.getLatLng().lat);
            $(".inputLongitude").val(novoPonto.getLatLng().lng);
            if($("#cadastroPontos").html() != 'undefined'){
                var pontos = {'0':{'lat':novoPonto.getLatLng().lat, 'log':novoPonto.getLatLng().lng}}
                var regiao = {'0':''};
                var cliente = $("#pontoVeproprietario").val();
                buscaRegiaoPonto(pontos, regiao, cliente)
            }
        });
    }
}

$(document).on('change', '#inputRangeRaio', setaRaio);

$(document).on('change', '#inputRangeRaio', function(){
    $(".inputRaio").val($("#inputRangeRaio").val());
});


function setaRaio(){
  var mtrs = $(this).val()
  novoPontoRaio.setRadius(mtrs)
  $('#metrosRaio').html(mtrs+ " Mts")
}
// $("#inputRangeRaio").change(function(){
// })

$(document).on('click', '.bt-finaliza-local', localEmpresa)

function localEmpresa(e){
    e.preventDefault()
    $(".inputLatitude").val(novoPonto.getLatLng().lat);
    $(".inputLongitude").val(novoPonto.getLatLng().lng);
    $(".inputRaio").val(novoPontoRaio.getRadius());
    $(this).addClass('fa fa-check-square-o')
}



$(document).on('click', '.buscados', setaBusca);

function setaBusca (e) {
    e.preventDefault()
    $(this).parents('.ul-busca').siblings('.resultado').val($(this).attr('data-id'))
    $(this).parents('.ul-busca').siblings('.buscas-campo').val($(this).html())
    $('.ul-busca').remove()
    //$('#clsigmento').focus()
}

$(document).on('click', '.remove-campo', removeCampo);
function removeCampo(e) {
    e.preventDefault()
    var url = $(this).attr('href')
    var thad = $(this);
    if(typeof $(this).attr('data-id') != 'undefined') {
        var id = $(this).attr('data-id')
        $.post(ROOT+'/painel/cadastros/'+url+'/excluir', {id:id}, function(data){})
    }
    $(thad).parents('.campos-add').remove()
}

dataTableCol6 = $(".datatable-col-6").dataTable({
	"pagingType": "full_numbers",
	"aoColumnDefs": [
		{ 'bSortable': false, 'aTargets': [ 5 ] }
	],
	dom: 'Bfrtip',
	lengthMenu: [
		[ 10, 25, 50, -1 ],
			[ '10 linhas', '25 linhas', '50 linhas', 'Mostrar todos' ]
		],
	buttons: [
			'pageLength',
	],
	"language": traducao,
});


dataTableCol5 = $(".datatable-col-5").dataTable({
	"pagingType": "full_numbers",
	"aoColumnDefs": [
		{ 'bSortable': false, 'aTargets': [ 4 ] }
	],
	dom: 'Bfrtip',
	lengthMenu: [
		[ 10, 25, 50, -1 ],
			[ '10 linhas', '25 linhas', '50 linhas', 'Mostrar todos' ]
		],
	buttons: [
			'pageLength',
	],
	"language": traducao,
});

$(".datatable-col-4").dataTable({
    paging: false,
    retrieve: true,
    'bRetrieve':true,
	"pagingType": "full_numbers",
	"aoColumnDefs": [
		{ 'bSortable': false, 'aTargets': [ 3 ] }
	],
	dom: 'Bfrtip',
	lengthMenu: [
		[ 10, 25, 50, -1 ],
			[ '10 linhas', '25 linhas', '50 linhas', 'Mostrar todos' ]
		],
	buttons: [
			'pageLength',
	],
	"language": traducao,
});

$(".datatable-col-3").dataTable({
    paging: false,
    retrieve: true,
	"pagingType": "full_numbers",
	"aoColumnDefs": [
		{ 'bSortable': false, 'aTargets': [ 2 ] }
	],
	dom: 'Bfrtip',
	lengthMenu: [
		[ 10, 25, 50, -1 ],
			[ '10 linhas', '25 linhas', '50 linhas', 'Mostrar todos' ]
		],
	buttons: [
			'pageLength',
	],
	"language": traducao,
});

$(".datatable-col-2").dataTable({
    paging: false,
    retrieve: true,
	"pagingType": "full_numbers",
	"aoColumnDefs": [
		{ 'bSortable': false, 'aTargets': [ 1 ] }
	],
	dom: 'Bfrtip',
	lengthMenu: [
		[ 10, 25, 50, -1 ],
			[ '10 linhas', '25 linhas', '50 linhas', 'Mostrar todos' ]
		],
	buttons: [
			'pageLength',
	],
	"language": traducao,
});

$(".salvar").one('click', function(e){ //errosalvar
    $(this).parent().prepend('<div class="fa-spinner-tmp"><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Gravando</div>');
    $('.fa-spinner-tmp').css({'float': 'left', 'margin-right': '10px'});
    $(this).css('display', 'none');
    $('input').prop('readonly', true);
});

// $(".salvar-witout-readonly").one('click', function(e){ //errosalvar Sem readOnli
//     $(this).parent().prepend('<div class="fa-spinner-tmp"><span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>Gravando</div>');
//     $('.fa-spinner-tmp').css({'float': 'left', 'margin-right': '10px'});
//     $(this).css('display', 'none');
// });

$(".bt-filtros-usuapp").click(function(){
	var thad = $(this);
	var url = $(this).attr('data-url')
	var val = $(this).attr('data-val')
    var id = $("#selUsuaApp").val()
	$(".exportar").val(val)
	$.post(url, {val:val, id:id}, function(data){
        $(thad).removeClass('btn-default').addClass('btn-primary')
        $(thad).siblings().addClass('btn-default').removeClass('btn-primary')
		populaTabelaUsuApp(data)
	})
})

$(".usuapp-perfil").on("change",function(){

    if($(this).val() == 'M'){
        $('.ususapp-modo').removeClass('hidden');
        // setar checkbox como false
        $('#usuAppModo').attr('checked', true);
        $('#usuAppModo').val('S');
    }else{
        $('#usuAppModo').val('N');
        $('.ususapp-modo').addClass('hidden');
        $('#usuAppModo').attr('checked', false);
    }
    // if($(this).val())
});

function populaTabelaUsuApp(data) {
    var data = data.status
    var dataSet = []

    var ppexcluir = $("#ppexcluir").data('permissao');
    var ppeditar = $("#ppeditar").data('permissao');

    for(i in data) {
            var nome = ''
            var tr = '';
            if(data[i].motorista != null) {
                nome = data[i].motorista.mtnome
            }else if(data[i].usuario != null) {
                nome = data[i].usuario.name
            }
            var perfil = data[i].usaperfil == 'M' ? 'Motorista/Ajutante' : 'Rastreamento';
            dados = [];
            dados.push(data[i].usacodigo);
            dados.push(nome)
            dados.push(perfil)

            if(ppeditar){
                tr += '<a title="Editar Usuário" class="btn btn-info" href="'+ROOT+'/painel/cadastros/usuarios/app/editar/'+data[i].usacodigo+'">';
                    tr += '<span class="fa fa-pencil"></span>';
                tr += '</a>';
            }
            if(ppexcluir){
                tr += '<a href="#" title="Remover usuário" class="btDelModal btn-tb btn btn-danger desativar-cadastros" data-toggle="modal" data-target="#modalDelataDesativa" data-delete-action="'+ROOT+'/painel/cadastros/usuarios/app/excluir/'+data[i].usacodigo+'">';
    //             tr += '<a href="#" title="Remover usuário" class="btDelModalUsuApp btn-tb btn btn-danger desativar-cadastros" data-toggle="modal" data-target="#modalDeleta" data-delete-action="'+ROOT+'/painel/cadastros/usuarios/app/destroy/'+data[i].usacodigo+'">';
                    tr += '<span class="glyphicon glyphicon-minus"></span>';
                tr += '</a>';
            }
            dados.push(tr)
            dataSet.push(dados);
    }

    $('#tableCadUsuApp').DataTable().destroy();
    table =  $('#tableCadUsuApp').DataTable({
        paging: false,
        retrieve: true,
        language: traducao,
        dom: 'Bfrtip',
        buttons:
            [{
               extend: 'pdf',
               className: 'btn-lg btn btn-default exportar',
               exportOptions: { columns: [0,1,2] },
               customize: function (doc) {
                    doc.defaultStyle.alignment = 'center';
                    doc.styles.tableHeader.alignment = 'center';
                    doc.content[1].table.widths =
                    Array(doc.content[1].table.body[0].length + 1).join('*').split('');
               }
            },{
               extend: 'excel',
               footer: false,
               className: 'btn-lg btn btn-default exportar',
               exportOptions: { columns: [0,1,2] }
           },{
               extend: 'csv',
               footer: false,
               className: 'btn-lg btn btn-default exportar',
               exportOptions: { columns: [0,1,2] }
           },{
               extend: 'print',
               text: 'Imprimir',
               footer: false,
               className: 'btn-lg btn btn-default exportar',
               exportOptions: { columns: [0,1,2] }
           }],
        data: dataSet,
        initComplete: function () {
            $('.dt-buttons').prepend('<span class="label-botoes-table">Exportar para: </span>');
            $('.exportar').removeClass("dt-button buttons-pdf buttons-csv buttons-excel buttons-html5");
            $(".exportar").prepend('<span class="fa fa-save"></span>')
        }
    });

}
$( document ).ready(function() {
	var ativo = $(".campo-status").val()
	if(ativo == 'I') {
		$(".desabilitar").prop("disabled", true);
	}
    $("#pontoVeproprietario").change(function(){
        var pontos = {'0':{'lat':$('#inputPontoLatitude').val(), 'log':("#inputPontoLongitude")}}
        var regiao = {'0':''};
        var cli = $(this).val();
        console.log(pontos, regiao, cli)
        buscaRegiaoPonto(pontos, regiao, cli)
    })
});

$(".campo-status").click(function(){
	if(!$('.campo-status').is(":checked")) {
		$(".desabilitar").prop("disabled", true);
	}else{
		$(".desabilitar").prop("disabled", false);

	}
})

$("#modulo").keyup(function(){
    var mod = $(this).val()
    if(mod.length > 2) {
        $.post(ROOT+'/painel/busca/modulos', {mod:mod}, function(data){
            var modulo = data.modulos
            var ul = '<ul class="ul-busca">';
            for(i in modulo) {
                ul += '<li><a href="#" class="buscados" value="'+modulo[i].mocodigo+'">'+modulo[i].mocodigo+'</a>';
            }
            ul += '</ul>';
            $('#modulo').parents('.busca').append(ul)
        })
    }else if(mod.length == 0){
        $('.ul-busca').remove()
    }
})


$(document).on('keyup', '.cidades', buscaCidades);

function buscaCidades() {
    var cidade = $(this).val()
    if(cidade.length > 2) {
        $('.ul-busca li').remove()
        $.post(ROOT+'/painel/busca/cidades', {cidade:cidade}, function(data){
            var cidades = data.cidades
            var ul = '<ul class="ul-busca">';
            for(i in cidades) {
                ul += '<li><a href="#" class="buscados" data-id="'+cidades[i].cicodigo+'">'+cidades[i].cinome+'</a>';
            }
            ul += '</ul>';
            $('.busca').append(ul)
        })
    }else if(cidade.length == 0){
        $('.ul-busca').remove()
    }
}

$('.multcliente').change(function(){
    var opt = $(this).find(':selected')
    //var html =  $('.multcliente option:selected').text()
    var option = ''
    if(opt.length == 1) {
        $('.usucliente').html('')
        option = '<option selected value="'+$(opt).val()+'">'+$(opt).text()+'</option>';
    }else{
        for (i in opt) {
            if(!isNaN(i)) {
                var valor = $(opt[i]).val()
                var html = $(opt[i]).html()
                var op = opt[i]
                option += '<option value="'+valor+'">'+html+'</option>';
            }
        }
    }
    $('.usucliente').html(option)
})


$(".usuapp-perfil").change(function(){
    var val = $(this).val();
    var cliente = $(".usacliente").val()
    if(val == 'M') {
        $(".usaassociado").attr('name', 'usamotorista')
    }else{
        $(".usaassociado").attr('name', 'usausuario')

    }
    $.post(ROOT+'/painel/cadastros/usuarios/app/associado',
        {
            val:val,
            cliente: cliente
        },
    function(dados){
        var dados = dados.dados
        var id = $(".usaassociado").attr('data-val')
        var opt = ''
        for(i in dados) {
            var sel = dados[i].codigo == id ? "selected" : ""
            opt += '<option '+sel+' value="'+dados[i].codigo+'">'+dados[i].nome+'</option>'
        }
        $(".usaassociado").html(opt)
    })
})

$(".usacliente").change(function(){
    $(".usuapp-perfil").trigger('change')
})

$(document).ready(function(){
    $('#selUsuaApp').trigger('change');
})

$("#selUsuaApp").change(function(){

    var id = [];
    id = $('#selUsuaApp').val()

    $.post(ROOT+'/painel/cadastros/usuarios/app/dados/cliente',{id:id},
    function(dados){
        populaTabelaUsuApp(dados)
    })
})

$(document).on('click', '.btDelModalUsuApp', desativaDeleta);
$(document).on('click', '.btDelModal', desativaDeleta);

function desativaDeleta() {
    var thad = $(this)
    var url = $(this).attr('data-delete-action')
    var data_class =  $(this).data('class');
    var data_id =  $(this).data('id');
    bts = '';
    bts += '<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>';
    bts += '<button type="button" value="'+url+'" data-id='+data_id+' class="btn btn-primary '+data_class+' bt-modal-desable">Salvar alterações</button>';

    $("#modalDelataDesativa .modal-footer").html(bts)
    $("#modalDelataDesativa .modal-title").html("Deletar")

    $('.bt-modal-desable').click(function(){
        var url = $(this).val()
        $.post(url, '', function(dados){
            $('#modalDelataDesativa').modal('hide');
            $('#selUsuaApp').trigger('change');
            $(".bt-filtros-update.btn-primary").trigger('click');
        })
    })
}

$(document).on('click', '.modalDesativa', modalDesativa);

function modalDesativa() {
    var thad = $(this);
    var url = $(this).attr('data-url');
    var data_class =  $(this).data('class');
    var head =  $(this).data('head');
    bts = '';
    bts += '<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>';
    bts += '<button type="button" value="'+url+'" class="btn btn-primary '+data_class+' bt-modal-desable">Salvar alterações</button>';
    $("#modalDesativa .modal-footer").html(bts);
    $("#modalDesativa .modal-title").html(head);

    $('.bt-modal-desable').click(function(){
        var url = $(this).val()
        $.post(url, '', function(dados){
            $('#modalDesativa').modal('hide');
            $(".bt-filtros-update.btn-primary").trigger('click');
        })
    })
}

$(document).ready(function(){
    $('.select-cliente-pontos').trigger('change')
    $('.select-cliente-motoristas').trigger('change')
    $(".usuapp-perfil").trigger('change')
});

$(document).ready(function(){
	$('.inteiro-positivo').on('change',function(){
		if($(this).val() < 0){
			$(this).val(0);
		}
	})
});



function buscaRegiaoPonto(pontos, regiao, cliente){
    $.post(ROOT+'/painel/cadastros/pontos/regiao',
        {
            pontos:pontos,
            regiao:regiao,
            cliente:cliente
        },
        function(dados){
            var regiao = dados.regiao
            if(typeof regiao != 'undefined') {
                $("#poregiao").val(regiao.recodigo);
                $('.regiao-nome').val(regiao.redescricao);
            }else{
                $("#poregiao").val('');
                $('.regiao-nome').val('');
            }
        }
    )
}
