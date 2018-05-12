carregaMapa = function (){
    if(typeof(CLLATITUDE) != 'undefined' && CLLATITUDE.length != 0) {
          position = [CLLATITUDE,CLLONGITUDE];
    }

    else
        position = [-27.099203, -52.626327];

    var attribution = '&copy;<a href="http://maps.google.com">Google Maps</a>';

    var googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {attribution: attribution, maxZoom: 20, subdomains:['mt0','mt1','mt2','mt3']}),
        satelliteGoogle = L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {attribution: attribution, maxZoom: 20, subdomains:['mt0','mt1','mt2','mt3']}),
        detalhado = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {attribution: attribution});

    var baseLayers = {
          "Padrão": googleStreets,
          "Satélite": satelliteGoogle,
          "Detalhado": detalhado
      };


    mapa = L.map('mapaPrincipal', {
          center: position,
          zoom: 13,
          layers: [googleStreets]
      });
    mapa.zoomControl.setPosition("bottomright");
    L.control.layers(baseLayers,null,{
        position: 'bottomright'
    }).addTo(mapa);
    //marker matriz
    var iconMatriz = L.icon({
        iconUrl: ROOT+'/img/matriz.png',
        iconSize: [50,50]
    });
    var matriz = L.marker(position,{
        title: 'Matriz',
        icon: iconMatriz
    });

    matriz.addTo(mapa);
}
