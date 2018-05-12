const { mix } = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js([
    'resources/assets/js/cores.js',
    'resources/assets/js/app.js',
    './node_modules/jquery.maskedinput/src/jquery.maskedinput.js',
    './node_modules/jquery-maskmoney/src/jquery.maskMoney.js',
    './node_modules/jquery-form/dist/jquery.form.min.js',
    './node_modules/select2/dist/js/select2.min.js',
    './node_modules/select2/dist/js/i18n/pt-BR.js',
    'resources/assets/js/template/plugins/bootstrap/bootstrap-datepicker.js',
    'resources/assets/js/ui.datepicker-pt-BR.js',
    'resources/assets/js/template/plugins/daterangepicker/daterangepicker.js',
    'resources/assets/js/template/plugins/bootstrap/bootstrap-timepicker.min.js',
    './node_modules/leaflet/dist/leaflet.js',
    './node_modules/leaflet-ant-path/dist/leaflet-ant-path.js',
    './node_modules/leaflet.markercluster/dist/leaflet.markercluster.js',
    './node_modules/prunecluster/dist/PruneCluster.js',
    'resources/assets/js/carregaMapa.js',
  	'resources/assets/js/mapas.js',
    'resources/assets/js/verificaSessao.js',
    'node_modules/datatables/media/js/jquery.dataTables.min.js',
], 'public/js/layout/app.js')

mix.js([
    'resources/assets/js/app.js',
], 'public/js/layout/layout.js')


// mix.js([
//     'resources/assets/js/relatorios/tempoParado.js',
// ], 'public/js/relatorios/tempoParado.js')

mix.js([
    'resources/assets/js/cores.js',
    'resources/assets/js/eagle.js',
    'resources/assets/js/template/plugins/jquery/jquery-ui.min.js',
    'resources/assets/js/template/plugins/bootstrap/bootstrap-datepicker.js',
    './node_modules/jquery-form/dist/jquery.form.min.js',
    './node_modules/select2/dist/js/select2.min.js',
    './node_modules/select2/dist/js/i18n/pt-BR.js',
    'resources/assets/js/ui.datepicker-pt-BR.js',
    './node_modules/leaflet/dist/leaflet.js',
    './node_modules/leaflet-ant-path/dist/leaflet-ant-path.js',
    'resources/assets/js/carregaMapa.js',
    './node_modules/jquery.maskedinput/src/jquery.maskedinput.js',
    './node_modules/jquery-maskmoney/src/jquery.maskMoney.js',
    'resources/assets/js/template/plugins/icheck/icheck.min.js',
    'node_modules/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.js',
    'resources/assets/js/template/plugins/scrolltotop/scrolltopcontrol.js',
    './node_modules/rickshaw/vendor/d3.v2.js',
    './node_modules/rickshaw/rickshaw.min.js',
    'resources/assets/js/template/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js',
    'resources/assets/js/template/plugins/jvectormap/jquery-jvectormap-world-mill-en.js',
    'resources/assets/js/template/plugins/owl/owl.carousel.min.js',
    'resources/assets/js/template/plugins/daterangepicker/daterangepicker.js',
    'resources/assets/js/template/plugins/bootstrap/bootstrap-timepicker.min.js',
    'node_modules/datatables/media/js/jquery.dataTables.min.js',
    'resources/assets/js/template/plugins/tableexport/tableExport.js',
    'resources/assets/js/template/plugins/tableexport/jquery.base64.js',
    'resources/assets/js/template/plugins/tableexport/html2canvas.js',
    'resources/assets/js/template/plugins/tableexport/jspdf/libs/sprintf.js',
    'resources/assets/js/template/plugins/tableexport/jspdf/jspdf.js',
    'resources/assets/js/template/plugins/tableexport/jspdf/libs/base64.js',
    'resources/assets/js/template/settings.js',
    'resources/assets/js/template/plugins.js',
    'resources/assets/js/template/actions.js',
    'resources/assets/js/template/demo_dashboard.js',
    'resources/assets/js/helpers/strings.js',
    'resources/assets/js/cadastro.js',
    'resources/assets/js/usuario.js',
    'resources/assets/js/relatorios.js',
    'resources/assets/js/relatorios/kmspercorridos.js',
    'resources/assets/js/relatorios/tempoIgnicaoLigada.js',
    'resources/assets/js/relatorios/proximidade.js',
    'resources/assets/js/relatorios/historicoPosicoes.js',
    'resources/assets/js/relatorios/tempoParado.js',
    'resources/assets/js/relatorios/excessoVelocidade.js',
    'resources/assets/js/relatorios/controleHorarios.js',
    'resources/assets/js/relatorios/regioes.js',
    'resources/assets/js/relatorios/acionamentoPortas.js',
    'resources/assets/js/relatorios/tempoFuncionamento.js',
    'resources/assets/js/relatorios/comunicacao.js',
    'resources/assets/js/cadastros/motorista.js',
    'resources/assets/js/cadastros/modulos.js',
    'resources/assets/js/cadastros/veiculos.js',
    'resources/assets/js/cadastros/chips.js',
    'resources/assets/js/cadastros/jornadaTrabalho.js',
    'resources/assets/js/cadastros/gruposMotoristas.js',
    'resources/assets/js/cadastros/gruposVeiculos.js',
    'resources/assets/js/cadastros/tipoManutencao.js',
    'resources/assets/js/cadastros/manutencao.js',
    'resources/assets/js/cadastros/pontos.js',
    'resources/assets/js/cadastros/usuarios.js',
    'resources/assets/js/cadastros/perfilAcesso.js',
    'resources/assets/js/cadastros/clientes.js',
    'resources/assets/js/relatorios/jornadaTrabalho.js',
    'resources/assets/js/cadastros/importacaokml.js',
    'resources/assets/js/painel.js',
    'resources/assets/js/cadastros/regioes.js',
    'resources/assets/js/roteirizador/importarCargas.js',
    'resources/assets/js/roteirizador/cadastro.js',
    'resources/assets/js/roteirizador/rotaManual.js',
    'resources/assets/js/roteirizador/finalizacaoRotas.js',
    'resources/assets/js/verificaSessao.js',
    'resources/assets/js/roteirizador/acompanhamento.js',
    'resources/assets/js/cadastros/feriados.js',
    'resources/assets/js/coletivos/linhas.js'
], 'public/js/layout/eagle.js')

mix.styles([
    './node_modules/datatables/media/css/jquery.dataTables.min.css',
    './node_modules/select2/dist/css/select2.min.css',
    './node_modules/leaflet.markercluster/dist/MarkerCluster.css',
    './node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css',
    './node_modules/prunecluster/dist/LeafletStyleSheet.css',
    // './public/css/dropzone/dropzone.css',
], 'public/css/all.css')

mix.styles([
    './node_modules/leaflet/dist/leaflet.css',
], 'public/css/leaflet.css')

mix.styles([
     './public/css/bootstrap-datepicker.min.css',
    './public/css/bootstrap-datepicker.min.css',
], 'public/css/datepicker.css')

.sass('resources/assets/sass/app.scss', 'public/css')
.sass('resources/assets/sass/erro.scss', 'public/css/erro.css')
.sass('resources/assets/sass/layout.scss', 'public/css/layout.css')
.sass('resources/assets/sass/eagle.scss', 'public/css/eagle.css')
.version();//OBRIGA O BROWSER A NAO USAR CACHE QUANDO TEMOS ALTERACOES NO JAVASCRIPT
