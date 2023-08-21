var container = $('.filtro');
var sesion = container.find('#rol_sesion').val();
var filtro = container.find('#filtro');
var table = $('.table').find('tbody');
var original_html = table.html();
filtro.on('keyup',function(){
    var texto = $(this).val();
    if(texto!=='') {
        $('.pagination').addClass('hidden');
        $.ajax({
            url:'src/web_services/programas.php',
            type:'POST',dataType:'json',data:{texto:texto},
            success:function(data){
                var html = '';
                for(var objeto of data) {
                    html += '<tr>';
                    html += '<td>'+objeto.nivel+'</td>';
                    html += '<td>'+objeto.nombre+'</td>';
                    html += '<td>';
                    var fichas = objeto.fichas;
                    $.each(fichas,function(){
                        html += '<a target="_blank" style="margin-right: 5px;" class="text-italic text-info" href="?contenido=src/ficha_detalle.php&id='+this.id+'">'+this.ficha+'</a>';
                    })
                    html += '</td>';
                    var descripcion = objeto.descripcion;
                    descripcion=descripcion==null?'':descripcion;
                    html += '<td>'+descripcion+'</td>';
                    html += '<td class="text-center">';
                    html += '<a href="?contenido=src/programa_detalle.php&id='+objeto.id+'" class="btn-table" title="Ver más detalles" data-placement="left"><span class="fa fa-eye"></span></a>';
                    if(sesion==='1')html += '<a onclick="cargar_contenido_modal(this.href)" href="src/programa_formulario.php?id='+objeto.id+'" class="btn-table" title="Modificar" data-placement="left" data-toggle="modal" data-target="#modal"><span class="fa fa-edit"></span></a>';
                    html += '</td>';
                    html += '</tr>';
                } table.html(html);
            }
        })
    } else {
        $('.pagination').removeClass('hidden');
        table.html(original_html)
    }
})