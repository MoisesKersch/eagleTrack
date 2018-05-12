$(document).ready(function(){
    $(".btn-imprimir").click(function(){
        imprimirRelatorio("Relatório"," ");
    });
});

/*funcao para impressao*/
function imprimirRelatorio(titulo,cabecalho){
	var relatorio = $('.divImprimir').html(),
		tela_impressao = window.open('about:blank');//pega dados do relatorio
	html = "<html lang='en'>\
				<head>\
					<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
	html += "       <title>Eagle Track - "+titulo+"</title>";
	html += "       <style>\
						@media all{\
							 .hidden-print{\
								 display: none;\
							 }\
							 .success{\
								 background: #d0e9c6;\
							 }\
							 .danger{\
								 background: #ebcccc;\
							}\
							.warning{\
								 background: #faf2cc;\
							}\
							 .container {\
							   padding-right: 15px;\
							   padding-left: 15px;\
							   margin-right: auto;\
							   margin-left: auto;\
						   }\
						   .badge {\
						   			font-size: 11px;\
								    line-height: 20px;\
								    font-weight: 500;\
								    -moz-border-radius: 3px;\
								    -webkit-border-radius: 3px;\
								    border-radius: 3px;\
								    padding: 0px 8px;\
								} \
							 table {\
								 background-color: transparent;\
							 }\
							 th {\
								 text-align: left;\
							 }\
							 .table {\
							   width: 100%;\
							   max-width: 100%;\
							   margin-bottom: 20px;\
						   }\
						   .table > tbody > tr{\
								font-size: 10px;\
							}\
							 .table > thead > tr > th,\
							 .table > tbody > tr > th,\
							 .table > tfoot > tr > th,\
							 .table > thead > tr > td,\
							 .table > tbody > tr > td,\
							 .table > tfoot > tr > td {\
							   padding: 8px;\
							   line-height: 1.42857143;\
							   vertical-align: top;\
							   border-top: 1px solid #ddd;\
						   }\
							 .table > thead > tr > th {\
							   vertical-align: bottom;\
							   border-bottom: 2px solid #ddd;\
						   }\
							 .table > caption + thead > tr:first-child > th,\
							 .table > colgroup + thead > tr:first-child > th,\
							 .table > thead:first-child > tr:first-child > th,\
							 .table > caption + thead > tr:first-child > td,\
							 .table > colgroup + thead > tr:first-child > td,\
							 .table > thead:first-child > tr:first-child > td {\
							   border-top: 0;\
						   }\
							 .table > tbody + tbody {\
							   border-top: 2px solid #ddd;\
						   }\
							 .table .table {\
							   background-color: #fff;\
						   }\
							 .table-condensed > thead > tr > th,\
							 .table-condensed > tbody > tr > th,\
							 .table-condensed > tfoot > tr > th,\
							 .table-condensed > thead > tr > td,\
							 .table-condensed > tbody > tr > td,\
							 .table-condensed > tfoot > tr > td {\
							   padding: 5px;\
						   }\
							 .table-bordered {\
							   border: 1px solid #ddd;\
						   }\
							 .table-bordered > thead > tr > th,\
							 .table-bordered > tbody > tr > th,\
							 .table-bordered > tfoot > tr > th,\
							 .table-bordered > thead > tr > td,\
							 .table-bordered > tbody > tr > td,\
							 .table-bordered > tfoot > tr > td {\
							   border: 1px solid #ddd;\
						   }\
							 .table-bordered > thead > tr > th,\
							 .table-bordered > thead > tr > td {\
							   border-bottom-width: 2px;\
						   }\
							 .table-striped > tbody > tr:nth-of-type(odd) {\
							   background-color: #f9f9f9;\
						   }\
							 .table-hover > tbody > tr:hover {\
							   background-color: #f5f5f5;\
						   }\
							 table col[class*='col-'] {\
							   position: static;\
							   display: table-column;\
							   float: none;\
						   }\
							 table td[class*='col-'],\
							 table th[class*='col-'] {\
							   position: static;\
							   display: table-cell;\
							   float: none;\
						   }\
							 .table > thead > tr > td.active,\
							 .table > tbody > tr > td.active,\
							 .table > tfoot > tr > td.active,\
							 .table > thead > tr > th.active,\
							 .table > tbody > tr > th.active,\
							 .table > tfoot > tr > th.active,\
							 .table > thead > tr.active > td,\
							 .table > tbody > tr.active > td,\
							 .table > tfoot > tr.active > td,\
							 .table > thead > tr.active > th,\
							 .table > tbody > tr.active > th,\
							 .table > tfoot > tr.active > th {\
							   background-color: #f5f5f5;\
						   }\
							 .table-hover > tbody > tr > td.active:hover,\
							 .table-hover > tbody > tr > th.active:hover,\
							 .table-hover > tbody > tr.active:hover > td,\
							 .table-hover > tbody > tr:hover > .active,\
							 .table-hover > tbody > tr.active:hover > th {\
							   background-color: #e8e8e8;\
						   }\
							 .table > thead > tr > td.success,\
							 .table > tbody > tr > td.success,\
							 .table > tfoot > tr > td.success,\
							 .table > thead > tr > th.success,\
							 .table > tbody > tr > th.success,\
							 .table > tfoot > tr > th.success,\
							 .table > thead > tr.success > td,\
							 .table > tbody > tr.success > td,\
							 .table > tfoot > tr.success > td,\
							 .table > thead > tr.success > th,\
							 .table > tbody > tr.success > th,\
							 .table > tfoot > tr.success > th {\
							   background-color: #dff0d8;\
						   }\
							 .table-hover > tbody > tr > td.success:hover,\
							 .table-hover > tbody > tr > th.success:hover,\
							 .table-hover > tbody > tr.success:hover > td,\
							 .table-hover > tbody > tr:hover > .success,\
							 .table-hover > tbody > tr.success:hover > th {\
							   background-color: #d0e9c6;\
						   }\
							 .table > thead > tr > td.info,\
							 .table > tbody > tr > td.info,\
							 .table > tfoot > tr > td.info,\
							 .table > thead > tr > th.info,\
							 .table > tbody > tr > th.info,\
							 .table > tfoot > tr > th.info,\
							 .table > thead > tr.info > td,\
							 .table > tbody > tr.info > td,\
							 .table > tfoot > tr.info > td,\
							 .table > thead > tr.info > th,\
							 .table > tbody > tr.info > th,\
							 .table > tfoot > tr.info > th {\
							   background-color: #d9edf7;\
						   }\
							 .table-hover > tbody > tr > td.info:hover,\
							 .table-hover > tbody > tr > th.info:hover,\
							 .table-hover > tbody > tr.info:hover > td,\
							 .table-hover > tbody > tr:hover > .info,\
							 .table-hover > tbody > tr.info:hover > th {\
							   background-color: #c4e3f3;\
						   }\
							 .table > thead > tr > td.warning,\
							 .table > tbody > tr > td.warning,\
							 .table > tfoot > tr > td.warning,\
							 .table > thead > tr > th.warning,\
							 .table > tbody > tr > th.warning,\
							 .table > tfoot > tr > th.warning,\
							 .table > thead > tr.warning > td,\
							 .table > tbody > tr.warning > td,\
							 .table > tfoot > tr.warning > td,\
							 .table > thead > tr.warning > th,\
							 .table > tbody > tr.warning > th,\
							 .table > tfoot > tr.warning > th {\
							   background-color: #fcf8e3;\
						   }\
							 .table-hover > tbody > tr > td.warning:hover,\
							 .table-hover > tbody > tr > th.warning:hover,\
							 .table-hover > tbody > tr.warning:hover > td,\
							 .table-hover > tbody > tr:hover > .warning,\
							 .table-hover > tbody > tr.warning:hover > th {\
							   background-color: #faf2cc;\
						   }\
							 .table > thead > tr > td.danger,\
							 .table > tbody > tr > td.danger,\
							 .table > tfoot > tr > td.danger,\
							 .table > thead > tr > th.danger,\
							 .table > tbody > tr > th.danger,\
							 .table > tfoot > tr > th.danger,\
							 .table > thead > tr.danger > td,\
							 .table > tbody > tr.danger > td,\
							 .table > tfoot > tr.danger > td,\
							 .table > thead > tr.danger > th,\
							 .table > tbody > tr.danger > th,\
							 .table > tfoot > tr.danger > th {\
							   background-color: #f2dede;\
						   }\
							 .table-hover > tbody > tr > td.danger:hover,\
							 .table-hover > tbody > tr > th.danger:hover,\
							 .table-hover > tbody > tr.danger:hover > td,\
							 .table-hover > tbody > tr:hover > .danger,\
							 .table-hover > tbody > tr.danger:hover > th {\
							   background-color: #ebcccc;\
						   }\
							 .table-responsive {\
							   min-height: .01%;\
							   overflow-x: auto;\
						   }\
					   }\
					 </style>\
				</head>\
				<body>\
						<div class='container'>";
		html += "<div class='relatorioTitulo h3'>"+titulo+"</div>";
		html += "<div class='relatorioCabecalho'>"+cabecalho+"</div>";
		html +=	"<div class='relatorioCorpo'>"+relatorio+"</div>";
		html += "</div>"+
				"</body>"+
			"</html>";
	tela_impressao.document.write(html);
	tela_impressao.window.print();
	tela_impressao.window.close();
}

$(".data-data").mask('99/99/9999');

$(".data-data").datepicker({
	format: 'dd/mm/yyyy',
	language: 'pt-BR',
})

$(".data-data-min-today").datepicker({
	format: 'dd/mm/yyyy',
	language: 'pt-BR',
    startDate: "today",
    minDate: 0
});

$(".hora-inicio-pinfo").timepicker({
	minuteStep: 5,
	showSeconds: true,
	showMeridian: false,
	defaultTime: '00:00:01'
})
$(".hora-final-pinfo").timepicker({
	minuteStep: 5,
	showSeconds: true,
	showMeridian: false,
	defaultTime: '23:59:59'
})

$(".data-hora-inicio").timepicker({
	minuteStep: 5,
	showMeridian: false,
	defaultTime: '00:01'
})

$(".data-hora-fim").timepicker({
	minuteStep: 5,
	showMeridian: false,
	defaultTime: '23:59'
})

$('select').select2({
    "language": "pt-BR",
    allowClear: true
});

$('select.select2-noClear').select2({
    "language": "pt-BR",
    allowClear: false
});

$(".exportar").click(function(){
    var id = $(this).attr('data-id');
    var nCol = $(this).attr('data-col');
    var url = $(this).attr('data-url');
    table = document.getElementById(id);
    rows = table.getElementsByTagName("TR");
    var dados = []
    var excel = ''
    for (i = 1; i < (rows.length); i++) {
        cols = rows[i].getElementsByTagName("TD");
        dados[i] = []
        for(j = 0; j < (cols.length); j++) {
            if(j < nCol) {
                dados[i].push($(cols[j]).html())
                excel += $(cols[j]).html()+','
            }
        }
        excel += ';'
    }
    dados.shift()
    var thad = $(this)
    var type = $(this).attr('data-type')
    $(this).html('<span class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></span>')
        $.post(url,{
            type: type,
            dados: excel
        },
        function(data){
            if(type == 'pdf'){
                window.open(ROOT+'/'+data.dados+'.pdf');
                $(thad).html('PDF')
            }
        })
})


function dadosExport(table, column){
    table = document.getElementById(table);
    rows = table.getElementsByTagName("TR");
    var dados = ''
    for (i = 1; i < (rows.length); i++) {
		cols = rows[i].getElementsByTagName("TD");
		dados[i] = []
        for(j = 0; j < (cols.length); j++) {
            if(j < column) {
            	dados += $(cols[j]).html()+'*i&'
            }
        }
        dados += ';'
    }
    $('.exportar-dados').val(dados)
}
user = '';

$(".select-cliente").change(function(){
    var todos = $(this).val()
    var t = todos.indexOf("0")

    if(t == 0) {
        //$(".proximidade-clientes").children('.todos-preoximidade').remove()
        $(".select-cliente").children().attr('selected', true)
        $(".select-cliente").select2({
		    "language": "pt-BR"
		})
        var selected = 'selected'
    }
    var id = $(this).val();
    var url = $(this).attr('data-url');
    var sel = $(this).attr('data-id');

    $.post(url,
        {
            id:id,
        },
        function(data){
            var dados = data.dados
            var option = ''
			if(dados != ''){
				option = '<option value="0">Selecionar todos</option>';
			}
            for(i in dados){
				//Quando selecioan todas as empresas, apenas as empresas.
                option += '<option value="'+dados[i].vecodigo+'">'+dados[i].veplaca+' | '+dados[i].veprefixo+'</option>'
            }
            $('#'+sel).html(option)
        })
})


$(document).ready(function(){
    $(".select-cliente").trigger("change");
})


// Função para selecionar todos
$('.select-selecionar-todos-veiculos').change(function() {
    if ($(this).val()[0] == 'T' || $(this).val()[0] == 0) {
        $('select.select-selecionar-todos-veiculos option').prop("selected",true);
    }
    $(this).select2();
});


jQuery(function($) {
  $(document).on('keypress', 'input.only-number', function(e) {
    var $this = $(this);
    var key = (window.event)?event.keyCode:e.which;
    var dataAcceptDot = $this.data('accept-dot');
    var dataAcceptComma = $this.data('accept-comma');
    var acceptDot = (typeof dataAcceptDot !== 'undefined' && (dataAcceptDot == true || dataAcceptDot == 1)?true:false);
    var acceptComma = (typeof dataAcceptComma !== 'undefined' && (dataAcceptComma == true || dataAcceptComma == 1)?true:false);

		if((key > 47 && key < 58)
      || (key == 46 && acceptDot)
      || (key == 44 && acceptComma)) {
    	return true;
  	} else {
 			return (key == 8 || key == 0)?true:false;
 		}
  });
});
