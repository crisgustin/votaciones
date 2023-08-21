let form = document.getElementById('form');
let s_oferta = form.querySelector('#oferta');
let s_modalidad = form.querySelector('#modalidad');
let s_coordinacion = form.querySelector('#coord');
let s_nivel = form.querySelector('#nivel');
let s_programa = form.querySelector('#programa');
let s_ficha = form.querySelector('#ficha');
let fecha_inicial = form.querySelector('#fecha_inicial');
let fecha_final = form.querySelector('#fecha_final');
let btn_clear = form.querySelector('#clear');
let btn_export = document.querySelector('#export');
let table = document.querySelector('.table');
let tbody = document.querySelector('.table tbody');
let mensaje = document.getElementById('mensaje');
let conteo = document.getElementById('conteo');
window.onload = ()=>{
    google.charts.load('current', {'packages':['corechart']});
    make_request();
}
form.addEventListener('submit',(e)=>{
    btn_export.classList.add('hidden');
    make_request();
    e.preventDefault();
    e.stopPropagation();
});

s_oferta.addEventListener('change',()=>{
    loadOptions(s_modalidad);
});

s_modalidad.addEventListener('change',()=>{
    loadOptions(s_coordinacion);
});

s_coordinacion.addEventListener('change',()=>{
    loadOptions(s_nivel);
});

s_nivel.addEventListener('change',()=>{
    loadOptions(s_programa);
});

s_programa.addEventListener('change',()=>{
    loadOptions(s_ficha);
});

btn_clear.addEventListener('click',(e)=>{
    s_oferta.value = '';
    fecha_inicial.value = '';
    fecha_final.value = '';
    s_modalidad.innerHTML='';
    s_coordinacion.innerHTML='';
    s_nivel.innerHTML='';
    s_programa.innerHTML='';
    s_ficha.innerHTML='';
    $('.selectpicker').selectpicker('refresh');
    e.preventDefault();
    e.stopPropagation();
});

btn_export.addEventListener('click',(e)=>{
    btn_export.classList.add('hidden');
    let html = $.parseHTML(table.outerHTML)[0];
    html.style.fontSize='14px';
    let header = html.querySelector('thead').innerHTML;
    let body = html.querySelector('tbody').innerHTML;
    html.innerHTML = header+body;
    html.style.borderCollapse='collapse';
    html.removeAttribute('class');
    html.querySelectorAll('.chart_container').forEach((e,i)=>{e.remove()});
    html.querySelectorAll('.chart_image').forEach((e,i)=>{e.src = e.src.replaceAll('data:image/png;base64,','@');});
    $.ajax('src/escribir_archivo.php',{
        type:form.method,dataType:'text',data:{archivo:'../export.html',contenido:html.outerHTML},
        success:(data)=>{
            if(parseInt(data)) window.open('src/exportar/pdf/graficas.php?send_email=false','_blank');
        }, error:(error)=>{
            console.log(error);
        }
    });
    e.preventDefault();
    e.stopPropagation();
});

function loadOptions(elem) {
    let url = '';
    let formData = new FormData(form);
    let data = '';
    formData.forEach((v,k,fd)=>{
        if(v!=='') data += '"'+k+'":"'+v+'",';
    }); data = JSON.parse('{'+data.substring(0,(data.length-1))+'}');
    switch (elem) {
        case s_modalidad:url='modalidades';break
        case s_coordinacion:url='coordinaciones';break
        case s_nivel:url='niveles';break
        case s_programa:url='programas';break
        case s_ficha:url='fichas';break
    } url = 'src/web_services/options_'+url+'.php';
    $.ajax(url,{
        type:form.method,dataType:'html', data:data,
        success:(data)=>{
            elem.innerHTML = data;
            $('.selectpicker').selectpicker('refresh');
        }, error:(error)=>{
            console.log(error);
        }
    });
}

function make_request() {
    let formData = new FormData(form);
    let data = '';
    if(parseInt(formData.get('id_ficha'))) {
        formData.delete('oferta');formData.delete('modalidad');
        formData.delete('coord');formData.delete('nivel');formData.delete('id_programa');
    }
    formData.forEach((v,k,fd)=>{
        if(v!=='') data += '"'+k+'":"'+v+'",';
    }); data = JSON.parse('{'+data.substring(0,(data.length-1))+'}');
    $.ajax(form.action,{
        type:form.method,dataType:'json',data:data,
        success:(data)=>{
            let tipo_grafica = formData.get('tipo_grafica');
            let total_resueltas = data.total_resueltas;
            mensaje.textContent = data.mensaje;
            mensaje.parentElement.classList.remove('hidden');
            conteo.innerHTML = '<p style="text-align:center">'+data.total_asignadas + ' encuestas asignadas</p><p style="text-align:center">' + total_resueltas + ' encuestas resueltas</p><p style="text-align:center">' + data.total_faltantes + ' encuestas faltantes por resolver</p>';
            conteo.parentElement.classList.remove('hidden');
            btn_export.classList.remove('hidden');
            if (total_resueltas>0) {
                let preguntas_escritas = data.preguntas.escritas;
                let preguntas_seleccion = data.preguntas.seleccionadas;
                google.charts.setOnLoadCallback(drawCharts(preguntas_seleccion, tipo_grafica));
                setTimeout(() => {listarPreguntasAbiertas(preguntas_escritas);}, 2000);
            } else {
                btn_export.classList.add('hidden');
                tbody.innerHTML='';
            }
        }, error:(error)=>{
            console.log(error);
        }
    });
}

function drawCharts(preguntas, tipo_grafica) {
    tbody.innerHTML='';
    var i = 0;
    for(let pregunta of preguntas) {
        let tr = document.createElement('tr');
        let td = document.createElement('td');
        let p = document.createElement('p');
        let div = document.createElement('div');
        td.style.padding='20px';
        p.style.textAlign='center';
        p.style.marginTop='10px';
        p.style.marginBottom='10px';
        p.style.fontWeight='bold';
        p.style.fontSize='14px';
        p.textContent = pregunta.enunciado;
        div.classList.add('chart_container');
        div.style.width='800px';
        div.style.height='280px';
        div.style.marginLeft='auto';
        div.style.marginRight='auto';
        div.id = 'chart'+i;
        tr.appendChild(td);
        td.appendChild(p);
        td.appendChild(div)
        tbody.appendChild(tr);
        if (tipo_grafica==='column') drawLineChart(div.id,pregunta.respuestas);
        if (tipo_grafica==='pie') drawPieChart(div.id,pregunta.respuestas);
        i++;
    }
}

function drawLineChart(id, respuestas) {
    let data = [];
    let total = 0;
    data.push(['Respuesta', 'Cantidad',{role:'style'},{ role:'annotation'}]);
    for(let respuesta of respuestas) {
        data.push([respuesta.respuesta, parseInt(respuesta.cantidad), respuesta.color, respuesta.cantidad]);
        total += parseInt(respuesta.cantidad);
    }
    data = google.visualization.arrayToDataTable(data);

    let options = {title:'TOTAL = '+total+' (100%)',legend:'none',annotations:{alwaysOutside:true,textStyle:{fontSize:18,bold:true,color:'#000',auraColor:'#d3d3d3',opacity:0.8}}};

    let chart = new google.visualization.ColumnChart(document.getElementById(id));
    chart.draw(data, options);
    let img = document.createElement('img');
    img.style.width='auto';
    img.style.height='180px';
    img.classList.add('chart_image');
    img.classList.add('hidden');
    img.src = chart.getImageURI();
    document.querySelector('#'+id).parentElement.appendChild(img);
}

function drawPieChart(id, respuestas) {
    let data = [];
    let colors = [];
    let total = 0;
    data.push(['Respuesta', 'Cantidad']);
    for(let respuesta of respuestas) {
        colors.push('#'+respuesta.color);
        data.push(['('+respuesta.cantidad+') - '+respuesta.respuesta, parseInt(respuesta.cantidad)]);
        total += parseInt(respuesta.cantidad);
    }
    data = google.visualization.arrayToDataTable(data);
    let options = {title:'TOTAL = '+total+' (100%)',tooltip:{trigger:'none'},pieSliceTextStyle:{color:'black',fontSize:10},legend:{fontSize:10},colors:colors};
    let chart = new google.visualization.PieChart(document.getElementById(id));
    chart.draw(data, options);
    let img = document.createElement('img');
    img.style.width='auto';
    img.style.height='180px';
    img.classList.add('chart_image');
    img.classList.add('hidden');
    img.src = chart.getImageURI();
    document.querySelector('#'+id).parentElement.appendChild(img);
}

function listarPreguntasAbiertas(preguntas) {
    let html = tbody.innerHTML;
    for(let pregunta of preguntas) {
        html += '<tr>';
        html += '<td style="padding:20px">';
        html += '<p style="text-align:center;font-size:20px">'+pregunta.enunciado+'</p>';
        let fichas = Object.keys(pregunta['respuestas']);
        for(let ficha of fichas) {
            html += '<p style="font-weight:bold;margin-top:25px">' + ficha + '</p>';
            html += '<ul>';
            for(let item of pregunta['respuestas'][ficha]) {
                html += '<li>'+item+'</li>';
            }
            html += '</ul>';
        }
        html += '</td>';
        html += '</tr>';
    }
    tbody.innerHTML = html;
}