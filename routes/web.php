<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index');


Auth::routes();

Route::get('/home', 'HomeController@index');

Route::get('/verificasessao', 'HomeController@verificaSessao')->middleware('auth');

Route::post('/defineRota','Roteirizador\RoteirizadorController@requestRotasPolilyne');

Route::group(['prefix' => 'painel', 'middleware' => ['auth', 'att_session']], function(){
    Route::get('/', 'HomeController@painel');
    Route::get('/previsao/tempo','HomeController@previsaoTempo');
    Route::get('/ranking/kms','HomeController@rankingKms');
    Route::get('/alertas/manutencao','HomeController@alertasManutencao');
    Route::get('/alertas/cnhvencida','HomeController@alertasCnhVencida');
    Route::get('/regiao/veiculos','HomeController@veregiao');

    Route::group(['prefix' => 'cadastros'], function () {

        Route::group(['prefix' => 'chips', 'middleware' => ['auth_master', 'acl']], function () {
            Route::get('/', 'Cadastros\ChipController@index')->middleware('acl');
            Route::get('/cadastrar', 'Cadastros\ChipController@cadastro')->middleware('acl');
            Route::post('/listar', 'Cadastros\ChipController@listar')->middleware('acl');
            Route::post('/cadastrar', 'Cadastros\ChipController@save')->middleware('acl');
            Route::post('/editar', 'Cadastros\ChipController@save')->middleware('acl');
            Route::post('/status', 'Cadastros\ChipController@alterarStatus');
            // Route::get('/destroy/{id}', 'Cadastros\ChipController@destroy');
            Route::get('/editar/{id}', 'Cadastros\ChipController@show')->middleware('acl');
            Route::get('/exportar/pdf', 'Cadastros\ChipController@pdf');
            Route::get('/exportar/{type}', 'Cadastros\ChipController@excel');
        });

        Route::group(['prefix' => 'motoristas'], function () {
            Route::get('/', 'Cadastros\MotoristaAjudanteController@index')->middleware('acl');
            Route::post('/listar', 'Cadastros\MotoristaAjudanteController@listar')->middleware('acl');
            Route::get('/cadastrar', 'Cadastros\MotoristaAjudanteController@cadastro')->middleware('acl');
            Route::get('/buscapontoscliente', 'Cadastros\MotoristaAjudanteController@buscarPontosCliente');
            Route::post('/dados/cadastro', 'Cadastros\MotoristaAjudanteController@dadosCadastro');
            Route::post('/cadastrar', 'Cadastros\MotoristaAjudanteController@save')->middleware('acl');
            Route::post('/editar', 'Cadastros\MotoristaAjudanteController@save')->middleware('acl');
            Route::post('/status', 'Cadastros\MotoristaAjudanteController@alterarStatus');
            Route::post('/cliente', 'Cadastros\MotoristaAjudanteController@cliente');
            Route::get('/editar/{id}', 'Cadastros\MotoristaAjudanteController@show')->middleware('acl');
            Route::get('/exportar', 'Cadastros\MotoristaAjudanteController@exportar');
            Route::post('/jornada', 'Cadastros\MotoristaAjudanteController@jornada');
            Route::post('/mais/licenca', 'Cadastros\MotoristaAjudanteController@maisLicenca');
            Route::post('/desassociar/licenca', 'Cadastros\MotoristaAjudanteController@desassociarLicenca');
        });

        Route::group(['prefix' => 'pontos'], function () {
            Route::get('/', 'Cadastros\PontosController@index')->middleware('acl');
            Route::get('/cadastrar', 'Cadastros\PontosController@cadastro')->middleware('acl');
            Route::post('/cadastrar', 'Cadastros\PontosController@save')->middleware('acl');
            Route::post('/editar', 'Cadastros\PontosController@save')->middleware('acl');
            Route::get('/excluir/{id}', 'Cadastros\PontosController@destroy')->middleware('acl');
            Route::post('/destroy_ponto_mapa/{id}', 'Cadastros\PontosController@destroyPontoMapa')->middleware('verify_password');
            Route::post('/destroy_conflito', 'Cadastros\PontosController@destroyConflito');
            Route::post('/salvar_conflito', 'Cadastros\PontosController@salvarConflito');
            Route::post('/listar/reload', 'Cadastros\PontosController@reload');
            Route::get('/editar/{id}', 'Cadastros\PontosController@show')->middleware('acl');
            Route::post('/cliente', 'Cadastros\PontosController@cliente')->middleware('acl');
            Route::post('/busca_inicial', 'Cadastros\PontosController@buscaInicial');
            Route::get('/importar', 'Cadastros\PontosController@importar')->middleware('acl');
            Route::post('/importar', 'Cadastros\PontosController@salvarImportacao')->middleware('acl');
            Route::post('/disponibilidade', 'Cadastros\PontosController@disponibilidade');
            Route::post('/regiao', 'Cadastros\PontosController@regiao');
            Route::post('/pesquisaPonto', 'Cadastros\PontosController@pesquisaPontoEndereco');
            Route::post('/update/mapa', 'Cadastros\PontosController@updateMapa');
        });

        Route::group(['prefix' => 'clientes'], function() {
            Route::get('/', 'Cadastros\ClientesController@listar');
            Route::get('/cadastrar', 'Cadastros\ClientesController@criar')->middleware('acl');
            Route::post('/cadastrar', 'Cadastros\ClientesController@salvar')->middleware('acl');
            Route::get('/editar/{id}', 'Cadastros\ClientesController@editar')->middleware('acl');
            Route::post('/editar/{id}', 'Cadastros\ClientesController@atualizar')->middleware('acl');
            Route::get('/desativar/{id}', 'Cadastros\ClientesController@desativar');
            Route::post('desativar/{id}', 'Cadastros\ClientesController@desable');
            Route::post('/filtros', 'Cadastros\ClientesController@filtros');
            Route::get('/exportar/pdf', 'Cadastros\ClientesController@pdf');
            Route::get('/exportar/{type}', 'Cadastros\ClientesController@excel');
            Route::post('/ativar', 'Cadastros\ClientesController@ativar');
            Route::post('/tipo', 'Cadastros\ClientesController@tipo');
            Route::post('/key', 'Cadastros\ClientesController@key');
            Route::post('/key/remove/{id}', 'Cadastros\ClientesController@keyRemove');
        });
        Route::group(['prefix' => 'veiculos', 'middleware' => 'aclVei'], function(){
            Route::get('/', 'Cadastros\VeiculosController@listar')->middleware('acl');
            Route::get('/cadastrar', 'Cadastros\VeiculosController@criar')->middleware('acl');
            Route::post('/cadastrar', 'Cadastros\VeiculosController@salvar')->middleware('acl');
            Route::get('/editar/{id}', 'Cadastros\VeiculosController@editar')->middleware('acl');
            Route::post('/editar/{id}', 'Cadastros\VeiculosController@atualizar')->middleware('acl');
            Route::post('/status', 'Cadastros\VeiculosController@alterarStatus');
            Route::post('/buscar', 'Cadastros\VeiculosController@buscar');
            Route::post('/modulo_usado', 'Cadastros\VeiculosController@VerificaModuloUsado');
            Route::post('/desvincular_modulo_usado', 'Cadastros\VeiculosController@DesvincularModuloUsado');
            Route::post('/cliente', 'Cadastros\VeiculosController@cliente');
            Route::post('/regioes_cliente', 'Cadastros\VeiculosController@regioesCliente');
            Route::post('/veiculo', 'Cadastros\VeiculosController@veiculo');
            Route::post('/last_bilhete', 'Cadastros\VeiculosController@last_bilhete');
            Route::post('/hodometro_horimetro', 'Cadastros\VeiculosController@getHodometroHorimetro');


        });
        Route::group(['prefix' => 'usuarios/app'], function (){
            Route::get('/', 'Cadastros\UsuarioAppController@listar')->middleware('acl');
            Route::get('/cadastrar', 'Cadastros\UsuarioAppController@criar')->middleware('acl');
            Route::post('/associado', 'Cadastros\UsuarioAppController@associado');
            Route::post('/cadastrar', 'Cadastros\UsuarioAppController@cadastro')->middleware('acl');
            Route::get('/editar/{id}', 'Cadastros\UsuarioAppController@editar')->middleware('acl');
            Route::post('/editar/{id}', 'Cadastros\UsuarioAppController@atualizar')->middleware('acl');
            Route::get('/desativar/{id}', 'Cadastros\UsuarioAppController@desativar');
            Route::post('/desativar/{id}', 'Cadastros\UsuarioAppController@desable');
            Route::post('/excluir/{id}', 'Cadastros\UsuarioAppController@destroy')->middleware('acl');
            Route::post('/ativar/{id}', 'Cadastros\UsuarioAppController@ativar');
            Route::post('/status', 'Cadastros\UsuarioAppController@status');
            Route::post('/dados/cliente', 'Cadastros\UsuarioAppController@dadosCliente');
        });
        Route::group(['prefix' => 'perfil/acesso'], function (){
            Route::get('/', 'Cadastros\PerfilAcessoController@index')->middleware('acl');
            Route::post('/listar', 'Cadastros\PerfilAcessoController@listar');
            Route::post('/cadastrar', 'Cadastros\PerfilAcessoController@criar')->middleware('acl');
            Route::get('/cadastrar', 'Cadastros\PerfilAcessoController@criar')->middleware('acl');
            Route::get('/editar/{id}', 'Cadastros\PerfilAcessoController@editar')->middleware('acl');
            Route::post('/cadastrar', 'Cadastros\PerfilAcessoController@save')->middleware('acl');
            Route::post('/editar', 'Cadastros\PerfilAcessoController@update')->middleware('acl');
            Route::post('/ativar/{id}', 'Cadastros\PerfilAcessoController@ativar');
            Route::post('/desativar/{id}', 'Cadastros\PerfilAcessoController@desativar');
            Route::post('/perfilItens', 'Cadastros\PerfilAcessoController@perfilItens');
            Route::post('/grupos/veiculos', 'Cadastros\PerfilAcessoController@gruposVeiculos');
            Route::post('/veiculos/grupo', 'Cadastros\PerfilAcessoController@veiculosGrupo');
            Route::post('/check/desc', 'Cadastros\PerfilAcessoController@checDescricao');
        });
        Route::post('/mail/excluir', 'Cadastros\ClientesController@emailExcluir');

        Route::group(['prefix' => 'telefones'], function () {
            Route::post('/excluir', 'Cadastros\TelefonesController@excluir');
        });

        Route::group(['prefix' => 'usuarios'], function() {
            Route::get('/', 'Cadastros\UsuariosController@listar')->middleware('acl');
            Route::get('/cadastrar', 'Cadastros\UsuariosController@criar')->middleware('acl');
            Route::post('/cadastrar', 'Cadastros\UsuariosController@salvar')->middleware('acl');
            Route::get('/editar/{id}', 'Cadastros\UsuariosController@editar')->middleware('acl');
            Route::post('/editar/{id}', 'Cadastros\UsuariosController@atualizar')->middleware('acl');
            Route::post('/buscar/{name}', 'Cadastros\UsuariosController@buscarUsuario');
            Route::post('/desativar/{data}', 'Cadastros\UsuariosController@alterarStatus');
            Route::post('/ativar/{data}', 'Cadastros\UsuariosController@alterarStatus');
            Route::get('/master/{data}', 'Cadastros\UsuariosController@alterarMaster');
            Route::post('/clientes', 'Cadastros\UsuariosController@clientes');
            Route::post('/perfis', 'Cadastros\UsuariosController@perfis');
        });

        Route::group(['prefix' => 'modulos'], function() {
          Route::get('/', 'Cadastros\ModulosController@listar')->middleware('acl');
            Route::get('/cadastrar', 'Cadastros\ModulosController@criar')->middleware('acl');
            Route::post('/cadastrar', 'Cadastros\ModulosController@salvar')->middleware('acl');
            Route::post('/buscarModelo', 'Cadastros\ModulosController@buscarModelo');
            Route::post('/buscarSIM', 'Cadastros\ModulosController@buscarSIM');
            Route::get('/editar/{id}', 'Cadastros\ModulosController@editar')->middleware('acl');
            Route::post('/listar/reload', 'Cadastros\ModulosController@reload');
            Route::post('/editar/{id}', 'Cadastros\ModulosController@atualizar')->middleware('acl');
            Route::post('/status', 'Cadastros\ModulosController@alterarStatus');
            Route::post('/filtros', 'Cadastros\ModulosController@filtros');
            Route::get('/exportar', 'Cadastros\ModulosController@exportar');
            Route::post('/monito', 'Cadastros\ModulosController@monitor');
        });

        Route::group(['prefix' => 'gruposMotoristas'], function() {
          Route::get('/', 'Cadastros\GruposMotoristasController@listar')->middleware('acl');
            Route::get('/cadastrar', 'Cadastros\GruposMotoristasController@criar')->middleware('acl');
            Route::post('/cadastrar', 'Cadastros\GruposMotoristasController@salvar')->middleware('acl');
            Route::get('/editar/{id}', 'Cadastros\GruposMotoristasController@editar')->middleware('acl');
            Route::post('/editar/{id}', 'Cadastros\GruposMotoristasController@atualizar')->middleware('acl');
            Route::get('desativar/{id}', 'Cadastros\GruposMotoristasController@desativar');
            Route::post('desativar/{id}', 'Cadastros\GruposMotoristasController@disable');
            Route::post('/ativar', 'Cadastros\GruposMotoristasController@ativar');
            Route::post('/listarTable', 'Cadastros\GruposMotoristasController@listarTable');
        });

        Route::group(['prefix' => 'gruposVeiculos'], function() {
            Route::get('/', 'Cadastros\GruposVeiculosController@listar')->middleware('acl');
            Route::get('/cadastrar', 'Cadastros\GruposVeiculosController@criar')->middleware('acl');
            Route::post('/cadastrar', 'Cadastros\GruposVeiculosController@salvar')->middleware('acl');
            Route::get('/editar/{id}', 'Cadastros\GruposVeiculosController@editar')->middleware('acl');
            Route::post('/editar/{id}', 'Cadastros\GruposVeiculosController@atualizar')->middleware('acl');
            Route::get('disable/{id}', 'Cadastros\GruposVeiculosController@disable');
            Route::post('desativar/{id}', 'Cadastros\GruposVeiculosController@disable');
            Route::post('/excluir/{id}', 'Cadastros\GruposVeiculosController@delete')->middleware('acl');
            Route::post('/ativar', 'Cadastros\GruposVeiculosController@ativar');
            Route::post('/listarTable', 'Cadastros\GruposVeiculosController@listarTable');
            Route::post('veiculos', 'Cadastros\GruposVeiculosController@getVeiculos');
            Route::post('checkVeiculo', 'Cadastros\GruposVeiculosController@checkVeiculo');
            Route::post('desassociarVeiculoGrupo', 'Cadastros\GruposVeiculosController@desassociarVeiculoGrupo');
        });

        Route::group(['prefix' => 'jornadaTrabalho'], function() {
          Route::get('/', 'Cadastros\JornadaTrabalhoController@listar')->middleware('acl');
            Route::get('/cadastrar', 'Cadastros\JornadaTrabalhoController@criar')->middleware('acl');
            Route::post('/cadastrar', 'Cadastros\JornadaTrabalhoController@salvar')->middleware('acl');
            Route::get('/editar/{id}', 'Cadastros\JornadaTrabalhoController@editar')->middleware('acl');
            Route::post('/editar/{id}', 'Cadastros\JornadaTrabalhoController@atualizar')->middleware('acl');
            Route::get('desativar/{id}', 'Cadastros\JornadaTrabalhoController@desativar');
            Route::post('desativar/{id}', 'Cadastros\JornadaTrabalhoController@disable');
            Route::post('/ativar', 'Cadastros\JornadaTrabalhoController@ativar');
            Route::post('/listarTable', 'Cadastros\JornadaTrabalhoController@listarTable');
        });

        Route::group(['prefix' => 'regioes'], function() {
            Route::get('/', 'Cadastros\RegioesController@listar')->middleware('acl');
            Route::post('/cadastrar', 'Cadastros\RegioesController@salvar')->middleware('acl');
            // Route::post('/atualizar/{id}', 'Cadastros\JornadaTrabalhoController@atualizar');
            Route::post('/buscaRegioes', 'Cadastros\RegioesController@buscaRegioes');
            Route::post('/excluir', 'Cadastros\RegioesController@excluirRegiao');
        });

        Route::group(['prefix' => 'feriados'], function(){
            Route::get('/', 'Cadastros\FeriadosController@index')->middleware('acl');
            Route::get('/cadastrar', 'Cadastros\FeriadosController@cadastro')->middleware('acl');
            Route::post('/cadastrar', 'Cadastros\FeriadosController@salvar')->middleware('acl');
            Route::post('/listagem', 'Cadastros\FeriadosController@listagem');
            Route::get('/editar/{id}', 'Cadastros\FeriadosController@editar')->middleware('acl');
            Route::post('/editar/{id}', 'Cadastros\FeriadosController@update')->middleware('acl');
            Route::get('/excluir/{id}', 'Cadastros\FeriadosController@excluir')->middleware('acl');
            Route::post('/duplicados', 'Cadastros\FeriadosController@duplicados');
        });

        Route::group(['prefix' => 'justificativas'], function(){
            Route::post('/', 'Cadastros\JustificativasController@cadastro');
        });

    });

    Route::group(['prefix' => 'manutencao'], function () {
        Route::group(['prefix' => 'tipo_manutencao'],function(){
            Route::get('/','Cadastros\TipoManutencaoController@index');
            Route::post('/listar','Cadastros\TipoManutencaoController@listar');
            Route::post('/save','Cadastros\TipoManutencaoController@salvar');
            Route::get('/show/{id}', 'Cadastros\TipoManutencaoController@show');
            Route::get('/cadastrar','Cadastros\TipoManutencaoController@cadastrar');
            Route::post('/excluir','Cadastros\TipoManutencaoController@destroy');
        });

        Route::group(['prefix' => 'manutencao'], function(){
            Route::get('/','Cadastros\ManutencaoController@index');
            Route::get('/cadastrar','Cadastros\ManutencaoController@cadastrar');
            Route::post('/save','Cadastros\ManutencaoController@salvar');
            Route::post('/save_new','Cadastros\ManutencaoController@salvarNova');
            Route::post('/tipos_manutencoes','Cadastros\ManutencaoController@tiposManutencaoUsuario');
            Route::post('/listar','Cadastros\ManutencaoController@listar');
            Route::post('/realiza_manutencao','Cadastros\ManutencaoController@realizaManutencao');
            Route::post('/edit_manutencao','Cadastros\ManutencaoController@editManutencao');
            Route::post('/excluir','Cadastros\ManutencaoController@destroy');
        });

    });

    Route::group(['prefix' => 'coletivos'], function(){
        Route::group(['prefix' => 'cadastros'], function(){
            Route::group(['prefix' => 'linhas'], function(){
                Route::get('/cadastrar', 'Coletivos\LinhasController@cadastro');
                Route::post('/cadastrar', 'Coletivos\LinhasController@salvar');
                Route::post('/listagem', 'Coletivos\LinhasController@listagemDados');
                Route::get('/listagem', 'Coletivos\LinhasController@listagem');
                Route::get('/editar/{id}', 'Coletivos\LinhasController@editar');
                Route::post('/editar/{id}', 'Coletivos\LinhasController@update');
                Route::get('/excluir/{id}', 'Coletivos\LinhasController@excluir');
                Route::post('/pontos', 'Coletivos\LinhasController@listaPontos');
                Route::post('/regioes', 'Coletivos\LinhasController@listaRegioes');
                Route::post('/pontos/filtro', 'Coletivos\LinhasController@filtroPontos');
                Route::post('/pontos/todos', 'Coletivos\LinhasController@todosPontos');
                Route::post('/rota', 'Coletivos\LinhasController@rota');
                Route::post('/dados', 'Coletivos\LinhasController@dadosEdicao');
                Route::post('/check/descricao', 'Coletivos\LinhasController@checkDescricao');
            });
        });
    });


    Route::group(['prefix' => 'relatorios'], function(){
        Route::group(['prefix' => 'rotas'], function(){
            Route::post('/relatorio', 'Relatorios\RotaController@relatorio');
        });
        Route::group(['prefix' => 'tempo/parado'], function(){
            Route::get('/', 'Relatorios\TempoParadoController@listar');
            Route::post('/', 'Relatorios\TempoParadoController@relatorio');
            Route::post('/exportar', 'Relatorios\TempoParadoController@exportar');
            Route::post('/todos', 'Relatorios\TempoParadoController@todos');
        });
        Route::group(['prefix' => 'tempo/ignicao/ligada'], function(){
            Route::get('/', 'Relatorios\TempoIgnicaoController@listar');
            Route::post('/', 'Relatorios\TempoIgnicaoController@listar');
            Route::post('/gerar', 'Relatorios\TempoIgnicaoController@gerar');
            Route::post('/exportar', 'Relatorios\TempoIgnicaoController@exportar');
        });
        
        Route::group(['prefix' => 'comunicacao'], function(){
            Route::get('/', 'Relatorios\ComunicacaoController@listar');
            Route::post('/gerar', 'Relatorios\ComunicacaoController@gerar');
            //Route::get('/exportar', 'Relatorios\ExcessoVelocidadeController@exportar');
            Route::post('/exportar', 'Relatorios\ComunicacaoController@exportar');
            Route::post('/clientes', 'Relatorios\ComunicacaoController@clientes');
            Route::post('/carrega/veiculos','Relatorios\ComunicacaoController@carregaVeiculos');
            Route::post('/todos', 'Relatorios\ComunicacaoController@todos');
        });

        //TODO não apagar
        // Route::group(['prefix' => 'proximidade'], function() {
        //     Route::get('/', 'Relatorios\ProximidadeController@listar');
        //     Route::post('/buscar', 'Relatorios\ProximidadeController@relatorio');
        //     Route::get('/placas_grupo_motorista', 'Relatorios\ProximidadeController@buscarPlacasGrupoMotorista');
        //     Route::get('/exportar', 'Relatorios\ProximidadeController@exportar');
        //     Route::post('/todos', 'Relatorios\TempoIgnicaoController@todos');
        // });
        Route::group(['prefix' => 'tempo/funcionamento'], function(){
            Route::get('/', 'Relatorios\TempoFuncionamentoController@listar');
            Route::post('/', 'Relatorios\TempoFuncionamentoController@relatorio');
            Route::post('/exportar', 'Relatorios\TempoFuncionamentoController@exportar');
        });
        Route::group(['prefix' => 'jornada/trabalho'], function(){
            Route::get('/', 'Relatorios\JornadaTrabalhoController@listar');
            Route::post('/', 'Relatorios\JornadaTrabalhoController@relatorio');
            Route::post('/exportar', 'Relatorios\JornadaTrabalhoController@exportar');
            Route::post('/clientes', 'Relatorios\JornadaTrabalhoController@cliente');
            Route::post('/todos', 'Relatorios\JornadaTrabalhoController@todos');
        });
        Route::group(['prefix' => 'controle/horario'], function(){
            Route::get('/', 'Relatorios\ControleDeHorario@listar');
            Route::post('/cliente', 'Relatorios\ControleDeHorario@placaMorotista');
            Route::post('/relatorio', 'Relatorios\ControleDeHorario@relatorio');
            Route::post('/exportar', 'Relatorios\ControleDeHorario@exportar');
            Route::post('/dados_filtros', 'Relatorios\ControleDeHorario@dadosFiltros');
        });
        Route::group(['prefix' => 'excesso/velocidade'], function(){
            Route::get('/', 'Relatorios\ExcessoVelocidadeController@listar');
            Route::post('/', 'Relatorios\ExcessoVelocidadeController@relatorio');
            //Route::get('/exportar', 'Relatorios\ExcessoVelocidadeController@exportar');
            Route::post('/exportar', 'Relatorios\ExcessoVelocidadeController@exportar');
            Route::post('/clientes', 'Relatorios\ExcessoVelocidadeController@clientes');
            Route::post('/grupoMotorista', 'Relatorios\ExcessoVelocidadeController@grupoMotorista');
            Route::post('/todos', 'Relatorios\ExcessoVelocidadeController@todos');
        });

        Route::group(['prefix' => 'kmspercorridos'], function(){
            Route::get('/', 'Relatorios\KmPercorridoController@listar');
            Route::post('/buscar', 'Relatorios\KmPercorridoController@relatorio');
            Route::post('/exportar', 'Relatorios\KmPercorridoController@exportar');
            Route::get('/placas_grupo_motorista', 'Relatorios\KmPercorridoController@buscarPlacasGrupoMotorista');
            Route::get('/get_veiculos_cliente', 'Relatorios\KmPercorridoController@getVeiculosCliente');
        });

        Route::group(['prefix' => 'historico/posicoes'], function(){
            Route::get('/', 'Relatorios\HistoricoPosicoesController@listar');
            Route::post('/carrega/veiculos','Relatorios\HistoricoPosicoesController@carregaVeiculos');
            Route::post('/carrega/grupos/motoristas','Relatorios\HistoricoPosicoesController@carregaGrpMotoristas');
            Route::post('/gerar','Relatorios\HistoricoPosicoesController@gerar');
            Route::post('/exportar','Relatorios\HistoricoPosicoesController@exportar');
        });

        Route::group(['prefix' => 'regiao'],function(){
            Route::get('/', 'Relatorios\RegiaoController@listar');
            Route::post('/dados_filtros', 'Relatorios\RegiaoController@dadosFiltros');
            Route::post('/relatorio', 'Relatorios\RegiaoController@relatorio');
            Route::post('/exportar', 'Relatorios\RegiaoController@exportar');
        });

        Route::group(['prefix' => 'acionamentoPortas'],function(){
            Route::get('/', 'Relatorios\AcionamentoPortasController@listar');
            Route::post('/dados_filtros', 'Relatorios\AcionamentoPortasController@dadosFiltros');
            Route::post('/relatorio', 'Relatorios\AcionamentoPortasController@relatorio');
            Route::post('/exportar', 'Relatorios\AcionamentoPortasController@exportar');
        });
    });

    Route::group(['prefix' => 'roteirizador'], function(){

        Route::group(['prefix' => 'importar'], function() {
            Route::get('/cargas', 'Roteirizador\RoteirizadorController@importar');
            Route::post('/cargas', 'Roteirizador\RoteirizadorController@salvarImportacao');
            Route::post('/cargas/confirmar', 'Roteirizador\RoteirizadorController@confirmar');
        });
        Route::get('/criar', 'Roteirizador\RoteirizadorController@criar');
        Route::post('/dados/parametrizacao', 'Roteirizador\RoteirizadorController@dadosParametrizacao');
        Route::post('/carregar/parametros', 'Roteirizador\RoteirizadorController@carregarParametros');
        Route::post('/destroy', 'Roteirizador\RoteirizadorController@destroy');
        Route::post('/edit', 'Roteirizador\RoteirizadorController@edit');

        Route::group(['prefix' => '/rota/automatica'], function(){
            Route::post('/', 'Roteirizador\RoteirizadorController@rotaAutomatica');
            Route::post('remonta/cargas', 'Roteirizador\RoteirizadorController@remontaCargas');
        });
        Route::group(['prefix' => 'rota/manual'], function(){
            Route::get('/', 'Roteirizador\RoteirizadorController@rotaManual');
            Route::post('/regioes', 'Roteirizador\RoteirizadorController@regioes');
            Route::post('/itens', 'Roteirizador\RoteirizadorController@itens');
            Route::post('/editar/itens', 'Roteirizador\RoteirizadorController@editarItens');
            Route::post('/rotas', 'Roteirizador\RoteirizadorController@rotaManualRotas');
            Route::post('/itens/rota', 'Roteirizador\RoteirizadorController@itensRota');
            Route::post('/mais/pedido', 'Roteirizador\RoteirizadorController@maisPedido');
            Route::post('/novo/pedido', 'Roteirizador\RoteirizadorController@novoPadido');
            Route::post('/desassociar/item', 'Roteirizador\RoteirizadorController@desassociarItem');
            Route::post('/ja/roteirizados', 'Roteirizador\RoteirizadorController@jaRoteirizados');
            Route::post('/remover/rota', 'Roteirizador\RoteirizadorController@removeRota');
            Route::post('/remover/item', 'Roteirizador\RoteirizadorController@removeItem');
            Route::post('/update/statusRota','Roteirizador\RoteirizadorController@updateStatusRota');
        });

        Route::group(['prefix' => 'finalizacao/rota'], function(){
            Route::get('/','Roteirizador\FinalizadorRotaController@criar');
            Route::post('/regioes','Roteirizador\FinalizadorRotaController@regioes');
            Route::post('/mesclagemVeiculosCapacitados', 'Roteirizador\FinalizadorRotaController@getVeiculosCapacitados');
            Route::post('/alterar/cor','Roteirizador\FinalizadorRotaController@alterarCorRota');
            Route::post('/mesclarRota','Roteirizador\FinalizadorRotaController@mesclarRota');
        });

        Route::group(['prefix' => 'acompanhamento'], function(){
            Route::get('/','Roteirizador\AcompanhamentoController@acompanhar');
            Route::get('/buscar/rotas','Roteirizador\AcompanhamentoController@buscarRotas');
            Route::get('/getJustificativa/{id}', 'Roteirizador\AcompanhamentoController@getJustificativa');
            Route::get('/ItensRotaNaoFinalizada/{id}', 'Roteirizador\AcompanhamentoController@itensRotaNaoFinalizada');
            Route::post('/updateItensRota','Roteirizador\AcompanhamentoController@updateItensRota');
        });

    });
    Route::post('busca/cliente', 'Cadastros\ClientesController@buscar');
    Route::post('busca/chip', 'Cadastros\ChipController@buscar');
    Route::post('busca/cidades', 'Cadastros\CidadesController@buscar');
    Route::post('busca/modulos', 'Cadastros\ModulosController@buscar');

    //rotas para testes de funções
    Route::group(['prefix' => 'testes'], function(){
        Route::get('defineRota', 'TestesController@testeDefineRota');
        Route::get('calculaDistanciaTempo', 'TestesController@testeCalculaDistanciaTempo');
        Route::get('buscaPontoMaisProximo', 'TestesController@testeBuscaPontoMaisProximo');
        Route::get('montaCargas', 'TestesController@testeMontaCargas');
        Route::post('montaCargas', 'TestesController@testeMontaCargas');
        Route::get('jornadaLivre', 'TestesController@testeJornadaLivre');
    });
});


Route::get('painel/relatorios/jornada/trabalho/script', 'Relatorios\JornadaTrabalhoController@script');

Route::group(['prefix' => 'veiculos'], function () {
    Route::group(['prefix' => 'maps'], function () {
        Route::post('carregarMarkers', 'VeiculosController@carregaMarkersVeiculos');
        Route::post('atualizarMarkers', 'VeiculosController@ajaxRefreshVeiculos');
        Route::post('/atualiza/painel', 'VeiculosController@atualizaPainel');
        Route::get('listarMotoristas', 'Cadastros\MotoristaAjudanteController@listarMotoristas');
        Route::get('checkDisponibilidadeMA', 'Cadastros\MotoristaAjudanteController@checkDisponibilidadeMA');
        Route::get('desassociarMA', 'Cadastros\MotoristaAjudanteController@desassociarMA');
        Route::post('atualizarMotorista', 'Cadastros\VeiculosController@atualizarMotorista');
        Route::post('/listaPosicoes', 'VeiculosController@listaPosicoes');
        Route::post('/rotas', 'VeiculosController@rotasVeiculos');
        Route::post('/rastro/corrigido', 'VeiculosController@rastroCorrigidoVeiculo');
        Route::post('/paradas', 'VeiculosController@paradas');
        Route::post('/excessosVelocidades', 'VeiculosController@excessosVelocidades');
        Route::post('/acionamentoPortas', 'VeiculosController@acionamentoPortas');
        Route::post('/bloqueio', 'VeiculosController@bloqueio')->middleware('verify_password');
    });
});

Route::group(['prefix' => 'rotas'],function(){
        Route::post('/getAllRotas','Relatorios\RotaController@getAllRotas');
});

Route::post('busca/chip', 'Cadastros\ChipController@buscar');
Route::post('busca/cidades', 'Cadastros\CidadesController@buscar');
Route::post('clientes/pontos', 'Cadastros\PontosController@buscarPontosProximos');
Route::post('clientes/parada', 'Cadastros\PontosController@index');
Route::post('/painel/cadastros/pontos/reassociar', 'Cadastros\PontosController@reassociar');
Route::post('/painel/cadastros/pontos/save', 'Cadastros\PontosController@save');

Route::group(['prefix' => 'api'], function (){
    Route::post('/historico/posicoes', 'Api\BilhetesAppController@getHistoricoPosicoes');
    Route::post('/rotas/cadastro', 'Api\RotasController@cadastrar');
// });

// // Route::post('api/historico/posicoes', 'Api\BilhetesAppController@getHistoricoPosicoes');
// Route::group(array('prefix' => 'api'), function()
// {
  Route::post('pontosreferencia/all', 'Api\PontosReferenciaController@buscar')->middleware('check_token_api');
  Route::post('pontos', 'Api\PontosReferenciaController@buscarAll')->middleware('check_token_api');
  Route::patch('updateAlStatus', 'Api\AlertasController@updateAlStatus')->middleware('check_token_api');
  Route::put('updateAlStatusPut', 'Api\AlertasController@updateAlStatusPut')->middleware('check_token_api');
  Route::post('auth/usuacodigo', 'Api\UsuarioAppController@findByUsuaCod')->middleware('check_token_api');
  Route::post('veiculos/find', 'Api\VeiculosController@findByCodCliente')->middleware('check_token_cod_cliente_api');
  Route::post('veiculos/find_all_last_position', 'Api\VeiculosController@findAllVeiculosLastPosition')->middleware('check_token_cod_cliente_api');
  Route::post('modulos/find', 'Api\ModulosController@getUltimaPosicaoVeiculo')->middleware('check_token_modulo_id');
  Route::post('alertas/getalertas', 'Api\AlertasController@getAlertas')->middleware('check_token_api');
  Route::post('alertas/getall_alertas', 'Api\AlertasController@getAllAlertas')->middleware('check_token_api');
  Route::post('alertas/get_count_alertas', 'Api\AlertasController@getCountAlertas')->middleware('check_token_api');
  Route::post('veiculos/getposicoes', 'Api\VeiculosController@getPosicoesVeiculo')->middleware('check_token_modulo_id');
  Route::post('veiculos/find_modulo', 'Api\VeiculosController@findByModulo')->middleware('check_token_cod_cliente_api');
  Route::post('veiculos/relaciona_veiculo_motorista', 'Api\VeiculosController@relacionaVeiculoMotorista')->middleware('check_token_cod_cliente_api');
  Route::post('veiculos/find_all_from_user_app', 'Api\VeiculosController@findAllByUserApp')->middleware('check_token_api');
  Route::post('veiculos/find_all_modulos_from_user_app', 'Api\JornadasController@getDadosJornada')->middleware('check_token_api');
  Route::post('modulos/find_by_id', 'Api\ModulosController@findByIdModulo')->middleware('check_token_cod_cliente_api');
  Route::post('eventos/inserir_eventos_app', 'Api\EventosAppController@inserirEventosApp')->middleware('check_token_api');
  Route::put('bilhetes/inserir_bilhetes_app', 'Api\BilhetesAppController@inserirBilhetesApp')->middleware('check_token_api');

  Route::put('bilhetes/updateBilhetes', 'Api\BilhetesAppController@updateBilhetes')->middleware('check_token_api');
  Route::put('bilhetes/updateIgnicaoVeiculo', 'Api\BilhetesAppController@updateIgnicaoVeiculo')->middleware('check_token_api');
  Route::post('justificativas/getAll','Api\JustificativasController@getAllJustificativasForUser')->middleware('check_token_api');

  Route::group(['prefix' => 'veiculo', 'middleware' => ['check_token_api']], function () {
      Route::post('rota', 'Api\VeiculosController@rotaVeiculo');
      Route::post('velocidade', 'Api\VeiculosController@excessosVelocidades');
      Route::post('paradas', 'Api\VeiculosController@paradas');
      Route::post('acionamento/portas', 'Api\VeiculosController@acionamentoPortas');
  });

  Route::post('pontos/cadastro', 'Api\PontosController@cadastroJson');

  // Rota referente ao app de teste de Módulos
  Route::group(array('prefix' => 'test/modulo' ), function(){
      Route::get('bilhetes/{bimocodigo}', 'Api\BilhetesAppController@getBilhetesByModulo');
  });

});

Route::get('500', function()
{
    abort(500);
});
