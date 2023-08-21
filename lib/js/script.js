function cargar_contenido_modal(url){$('.modal-content').load(url)}

function cerrar_sesion() {
    $.ajax('logout.php',{
        type:'GET',
        success:function(){
            window.location = '/votaciones';
        },error:function(e){console.log(e)}
    })
}

function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('closed')
}

function ir_atras(){window.history.back()}

$(function(){
    $('.navbar-toggle').click(function(){
        $('.side-overlay,.sidebar').removeClass('hidden')
        setTimeout(function(){
            $('body').addClass('sidebar-open')
        },100);
    });
    
    $('.side-overlay,.sidebar a').click(function(){
        var is_dropdown = $(this).parent('li').hasClass('dropdown');
        if(!is_dropdown) {
            $('body').removeClass('sidebar-open')
            setTimeout(function(){
                $('.side-overlay,.sidebar').addClass('hidden')
            },500);
        }
    });

    /*$(window).bind('unload',() => {
        cerrar_sesion()
    })*/
});

$(document).on('hide.bs.modal','#modal',function(){
    $('.modal-content').html('');
})

$('form').on('submit',function(e){
    var submit = $(this).find('[type="submit"]');
    submit.attr({'disabled':'disabled'})
})

function eliminar(elem,id) {
    var url = $(elem).attr('data');
    cargar_contenido_modal('eliminar.html');
    setTimeout(function(){
        var form = $('.modal-content').find('form')
        form.attr({action:url});
        form.children('#id').val(id);
        form.submit(function(){
            form.find('button').attr({'disabled':true});
        });
    },50)
}