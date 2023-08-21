var filtro = document.getElementById('filtro_centro');

var radio = $(filtro).find('[name=id_rol]');

var input = $(filtro).find('#filtro');

var table = $('.table tbody');

$(document).ready(function(){radio.on('click',get_datos);input.on('keyup',get_datos);});



function get_datos(){

    var data = new FormData(filtro);

    var sesion = data.get('rol_sesion');

    var type = 'GET';

    var json = '{';

    data.delete('rol_sesion');

    data.forEach(function (value, key){if(value!==''&&value!=='todos') json += '"'+key+'":"'+value+'",';});

    json = json.length>1?json.substr(0,json.length-1)+'}':json+'}';

    json = JSON.parse(json);

    $('.pagination').removeClass('hidden');

    if(Object.keys(json).length>0){type = 'POST';$('.pagination').addClass('hidden');}

    $.ajax({

        url:'src/web_services/centros.php',type:type,dataType:'json',data:json,

        success:function(data) {

            var html = '';

            for(var i = 0; i < data.length; i++) {

                html += '<tr>';

                html += '<td>'+data[i].id+'</td>';

                html += '<td>'+data[i].regiona+'</td>';

                html += '<td>'+data[i].centro+'</td>';

                html += '<td class="text-center">'

                if(sesion==='1') html += '<a onclick="cargar_contenido_modal(this.href)" class="btn-table" data-toggle="modal" data-target="#modal" href="src/usuario_formulario.php?id='+data[i].id+'" title="Actualizar datos"><span class="fa fa-edit"></span></a>';

                html += '<a href="?contenido=src/usuario_detalle.php&id='+data[i].id+'" class="btn-table" title="Ver más detalles"><span class="fa fa-eye"></span></a>';

                html += '</td>';

                html += '</tr>';

            } table.html(html)

        }

    });

}