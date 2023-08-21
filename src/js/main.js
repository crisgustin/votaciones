    /*
    * En la variable map esta inicialiando el mapa para ellos usamos la constante L que instancia leaflet para el manejo de mapas.
    * el setView se usa para decirle a leaflet donde se centrar nuestro mapa y le pasamos lo valores de latitud y longitud, el valor de 10 indica el nivel de zoom por defecto.
    */
    let map = L.map('map-template').setView([1.208352,-77.280162], 17);
    //Aqui estoy declarando el TILE que usare, en este caso el de openstreetmap
    const tileUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    //Aqui declaramos la capa donde ira los creditos del Tile que usamos, puedes o no declararlo, cuestión de ética.
    L.tileLayer(
        tileUrl,
        {
            attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
            detectRetina: true
        }
        ).addTo(map);
    //Aqui configuro la personalización del marcador, usamos los iconos de fontawesome, 
    //Mayor informacion sobre extramarkers: https://www.npmjs.com/package/leaflet-extra-markers

    let marker = L.marker([1.208352,-77.280162]).addTo(map);


    var redMarker = {icon: L.ExtraMarkers.icon({
        icon: 'fa-exclamation-triangle',
        markerColor: 'red',
        shape: 'penta',
        prefix: 'fas'
    })} ;
    //Aqui creamos el marcador y le pasamos la personalizacion, con bindPopup le creamos una popup cuando hagamos clik sobre el marcador.
    // y con addTo lo agregamos al mapa que declaramos en la constante map.
    L.marker([-12.1374773,-77.0217879], redMarker).bindPopup('Tu estás aqui...').addTo(map);