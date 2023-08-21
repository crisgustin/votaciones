$('#form').on('submit',function(e){
    $('#submit').attr({'disabled':'disabled'});
    var data = new FormData(e.target);
    var json = '{';
    for (var key of data.keys()) {
        json += '"'+key+'":"'+data.get(key)+'",';
    }json = json.substring(0,(json.length-1))+'}';
    json = JSON.parse(json);
    $.ajax({
        url:'src/identidad.php',type:'POST',
        data:json,
        success:function(data) {
            var html = '';
            if(data!='')html = 'Hemos reenviado las credenciales de acceso al correo electrónico '+data;
            else html = 'No se pudo identificar a ningún usuario con los datos que ingresaste...';
            $('.nota').removeClass('hidden').text(html);
        }
    })
    e.preventDefault()
})