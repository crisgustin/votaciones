$(document).ready(function(){setTimeout(get_results,1500);});
var filtro = $('.filtro');
var fecha = filtro.find('.fecha');
var nivel = filtro.find('#nivel');
var programa = filtro.find('#programa');
var ficha = filtro.find('#ficha');
var coord = filtro.find('#coord');
var clear = filtro.find('#clear');
var btn_export = $('#export_pdf');
var search = filtro.find('#search');
filtro.children().removeAttr('title');
programa.selectpicker({'liveSearch':true});
coord.selectpicker();
fecha.datetimepicker({viewMode:'years',format:'YYYY-MM-DD',locale:'es'});
nivel.on('change',function(){
    coord.val('');
    programa.val('');
    ficha.val('');
    $('select').selectpicker('refresh');
    var parametros = {'nivel':this.value,'tipo':'programas'};
    cargar_options(parametros,programa);
});
programa.on('change',function(){
    coord.val('');
    ficha.val('');
    $('select').selectpicker();
});
coord.on('change',function(){
    var id_programa = programa.val();
    if(id_programa!=='') {
        var parametros = {'coord': this.value, 'id_programa': id_programa, 'tipo': 'fichas'};
        cargar_options(parametros, ficha);
    }
})

fecha.on('dp.change',function(){
    var id = this.id;
    if(id==='f_inicio') {
        var valor = this.value;
        $('#f_fin').datetimepicker({viewMode:'years',format:'YYYY-MM-DD',startDate:valor,locale:'es'});
    }
});
search.on('click',get_results);

clear.on('click',function(){
    programa.html('');
    ficha.html('');
    coord.val('');
    nivel.val('');
    fecha.val('');
    $('select').selectpicker('refresh');
})

btn_export.on('click',function(){
    var content = $.parseHTML($('.export-content').html());
    var html = '';

    content.forEach(function (elem) {
        if($(elem).hasClass('row')||$(elem).hasClass('table')||elem.id==='graficas'||elem.nodeName==='H4') {
            if(elem.id==='graficas') {
                var charts = $(elem).find('.chart');
                $(charts).find('svg,div').remove();
                var images = $(charts).find('img');
                $(images).removeAttr('class');
            }
            html += elem.outerHTML;
        }
    })
    html = $.trim(html.replace(/[\t\n]+/g,''));
    html = html.replace(/\s+/g, " ");
    html = html.replace('> <','><');
    $.ajax({
        url:'src/escribir_archivo.php',type:'POST',data:{archivo:'../export.html',contenido:html},
        success:function(data){
            if(data!=='') window.open('?contenido=src/exportar.php&exportar=pdf','_blank');
        }
    })

})

function cargar_options(parametros,elem) {
    $.ajax({
        url:'src/web_services/get_options_html.php',type:'POST',dataType:'html',data:parametros,
        success:function(data){
            $(elem).html(data);$(elem).selectpicker('refresh');
        }
    })
}

function get_results() {
    $('#graficas').html('');
    $('.nota').text('');
    google.charts.load('current', {'packages':['corechart']});
    var form = document.getElementById('filtro_form');
    var form_data = new FormData(form)
    if(ficha.val()!==''&&ficha.val()!=='undefined'&&ficha.val()!==null) form_data.delete('id_programa');
    var data = '{';
    form_data.forEach(function (value,key) {
        if(value!=='') data+='"'+key+'":"'+value+'",';
    });
    data = JSON.parse(data.substr(0,data.length-1)+'}');
    $.ajax({
        url:'src/web_services/resultados_encuesta.php',
        type:form.method,dataType:'json',data:data,
        success:function (json) {
            var conteo = json.conteo;
            var preguntas = json.preguntas;
            $('#completas').text(conteo.completas);
            $('#faltantes').text(conteo.faltantes);
            if(preguntas.length>0) {
                var tipo_grafica = data.tipo_grafica;
                google.charts.setOnLoadCallback(function(){drawChart(preguntas,tipo_grafica)});
            } $('.nota').text(json.mensaje);
        }
    })
}

function drawChart(preguntas, tipo_grafica) {
    var options = {title:'Conteo de respuestas',width:'100%',height:'300%',is3D:true,legend:{position:'labeled'},tooltip:{trigger:'none'},pieSliceText:'value',pieSliceTextStyle:{color:'#000'},pieSliceBorderColor:'#fff',chartArea:{height:"80%",width:"70%"}};
    var i = 0;
    for(var pregunta of preguntas) {
        var data = [['opcion','cantidad',{role:'style'}]];
        if(tipo_grafica!=='column') data = [['opcion','cantidad']];
        var id = 'p'+(i+1);
        var html = '<ul class="text-left">';
        $('#graficas').append('<div class="chart" id="'+id+'"></div>');
        var respuestas = pregunta.respuestas;
        var j=0; var slices=[]
        for(var respuesta of respuestas) {
            var opcion = respuesta.respuesta;
            var cantidad = respuesta.cantidad;
            var texto = respuesta.texto;
            if(pregunta.tipo!=='RA') {
                var color = '#e5e5e5'; //gris
                if(opcion==='Totalmente satisfecho') color = '#00ff00'; //verde
                if(opcion==='Parcialmente satisfecho') color = '#ffff00'; //amarillo
                if(opcion==='Insatisfecho') color = '#ff0000'; //rojo
                if(tipo_grafica==='column')data.push([opcion,parseInt(cantidad),color]);
                else {
                    data.push([opcion,parseInt(cantidad)]);
                    slices[j] = {color:color};
                    if((j+1)===respuestas.length) options['slices'] = slices;
                }
            } else html += '<li>'+texto+'</li>';
            j++;
        } html += '</ul>';

        if(pregunta.tipo!=='RA') {
            data = google.visualization.arrayToDataTable(data);
            var chart = tipo_grafica==='column'?new google.visualization.ColumnChart(document.getElementById(id)):new google.visualization.PieChart(document.getElementById(id));
            chart.draw(data, options);
            $('#'+id).append('<img src="'+chart.getImageURI()+'" width="780" height="150" class="hidden"/>')
        } else $('#'+id).append(html);
        $('<h4 class="text-center">'+pregunta.enunciado+'</h4>').prependTo('#'+id);
        i++;
    }
}