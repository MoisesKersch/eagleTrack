<?php
namespace App\Helpers;
use Excel;

class ExportaHelper
{
    public function exportaCSV($titulo,$arrayDados){
        $pasta = public_path("/csv");
        $nome = md5(\Auth::user()->id.\Auth::user()->name.$titulo);
        $arquivo = Excel::create($nome, function($csv) use($titulo,$arrayDados){
            $csv->sheet($titulo, function($sheet) use($arrayDados){
                $sheet->fromArray($arrayDados);
            });
        })->store('csv',$pasta);
        return "csv/$nome.csv";
    }

    public function exportaExcel($titulo,$arrayDados){
        $pasta = public_path("/excel");
        $nome = md5(\Auth::user()->id.\Auth::user()->name.$titulo);
        $arquivo = Excel::create($nome, function($excel) use($titulo,$arrayDados){
            $excel->sheet($titulo, function($sheet) use($arrayDados){
                $sheet->fromArray($arrayDados);
            });
        })->store('xls',$pasta);
        return "excel/$nome.xls";
    }

    public function converteHtmlPdf($titulo,$tabela){
        $html = "<!DOCTYPE html>
            <html lang=\"pt-BR\">
                <head>
                    <title>'.$titulo.'</title>
                    <meta charset=\"UTF-8\">
                    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
                    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
					<link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css\" integrity=\"sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u\" crossorigin=\"anonymous\">
					<link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css\" integrity=\"sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp\" crossorigin=\"anonymous\">
                </head>
                <body>
                    <div class=\"container\">
                        <h1>$titulo</h1>
                        <div class=\"float-left folha-cabecalho\">
                            <span>Relatório Simplificado: Relatório de <span style='text-transform:lowercase;'>$titulo</span></span>
                            <span>Emitido por: ".\Auth::user()->name."| Emissão: ".date('d/m/Y')."  </span>
                        </div>";
        $html .= $tabela;
        $html .= "</div></body></html>";
        //gravar arquivo
        $nome = md5(\Auth::user()->id.\Auth::user()->name.$titulo);
        $nomeArquivo = public_path("relatorios.html");
        $nomePdf = public_path("pdf/".$nome.".pdf");
        $arquivo = fopen($nomeArquivo,'w');
        fwrite($arquivo, $html);
        fclose($arquivo);
        exec("xvfb-run -a wkhtmltopdf $nomeArquivo $nomePdf",$shell);
        return "pdf/$nome.pdf";
    }
}
