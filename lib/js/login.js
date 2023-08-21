var form = $('#login_form');
var nota = form.find('.nota');
var tipo = form.find('#tipo_documento');
var documento = form.find('#documento');
var clave = form.find('#clave');
var url = form.attr('action');
var type = form.attr('method');

documento.on('keydown',function(e){
    console.log(e.key);
    var chars=['1','2','3','4','5','6','7','8','9','0','Backspace','Tab','Control','v','V'];
    //var numbers=['48','49','50','51','52','53','54','55','56','57'];
    if(!chars.includes(e.key))e.preventDefault()
}).focusin(function(){
    $(this).closest('.form-group').removeClass('has-error');
    tipo.closest('.form-group').removeClass('has-error');
    nota.text('')
});
clave.focusin(function(){$(this).closest('.form-group').removeClass('has-error'),nota.text('')});
tipo.change(function(){
    $(this).closest('.form-group').removeClass('has-error');
    documento.closest('.form-group').removeClass('has-error');
    nota.text('')
});

form.on('submit',function(e){
    var data = new FormData(e.target);
    var json = '{';
    for (var key of data.keys()) {
        json += '"'+key+'":"'+data.get(key)+'",';
    }json = json.substring(0,(json.length-1))+'}';
    json = JSON.parse(json);
    $.ajax(url,{
        type:type,data:json,
        //alert(data);
        success:function(data){
            if(data=='ok') window.location = '/votaciones/';
            else {
                if(data=='usuario incorrecto') {
                    tipo.closest('.form-group').addClass('has-error');
                    documento.closest('.form-group').addClass('has-error');
                } else if(data=='clave incorrecta') {
                    data = data.replace('clave','contraseña');
                    clave.closest('.form-group').addClass('has-error');
                }
                data='¡'+data.toUpperCase()+'!';
                nota.text(data);
            }
            console.log(data);
        },error:function(e){console.log(e)}
    }),e.preventDefault();
}),$('.selectpicker').selectpicker('mobile')