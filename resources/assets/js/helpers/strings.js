// *************************** TRUNCAR TEXTO ***********************************
// *****************************************************************************
/* Função para truncar o texto sem quebrar as palavras.. */
truncarTexto =  function(texto,limite){
    if(texto.length>limite){
        limite--;
        last = texto.substr(limite-1,1);
        while(last!=' ' && limite > 0){
            limite--;
            last = texto.substr(limite-1,1);
        }
        last = texto.substr(limite-2,1);
        if(last == ',' || last == ';'  || last == ':'){
             texto = texto.substr(0,limite-2) + '...';
        } else if(last == '.' || last == '?' || last == '!'){
             texto = texto.substr(0,limite-1);
        } else {
             texto = texto.substr(0,limite-1) + '...';
        }
    }
    return texto;
}

String.prototype.truncar = function(limite){
    var orig = this.toString();
    if(orig.length > limite){
        var texto = orig.substring(0, limite) + "...";
    }else{
        texto = orig;
    }
    return texto;
}

// String.prototype.truncar = function(limite){
//     if(!limite)
//         throw "Limite não informado";
//     if(this.toString().length>limite){
//         limite--;
//         last = texto.substr(limite-1,1);
//         while(last!=" " && limite > 0){
//             limite--;
//             last = texto.substr(limite-1,1);
//         }
//         last = texto.substr(limite-2,1);
//         if(last == "," || last == ";"  || last == ":"){
//             texto = texto.substr(0,limite-2) + "...";
//         } else if(last == "." || last == "?" || last == "!"){
//             texto = texto.substr(0,limite-1);
//         } else {
//             texto = texto.substr(0,limite-1) + "...";
//         }
//     }
//     return texto;
// }
