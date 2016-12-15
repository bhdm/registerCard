
var jcrop_api = null;
//$('#coords').on('change','input',function(e){
//    var x1 = $('#x1').val(),
//        x2 = $('#x2').val(),
//        y1 = $('#y1').val(),
//        y2 = $('#y2').val();
//    jcrop_api.setSelect([x1,y1,x2,y2]);
//});
//
function showCoords(selection, container)
{

    container.children('input[name="x1"]').val(selection.x);
    container.children('input[name="y1"]').val(selection.y);
    container.children('input[name="x2"]').val(selection.x2);
    container.children('input[name="y2"]').val(selection.y2);
};



function getImage(data, container){
    $('.fileBox').fadeIn();
    if ( data.data.error != undefined ) {
        $('.error-msg').fadeIn();
        $('.error-msg').html(data.data.error);
        //Судя по всему это обновление inputа
        //var control = container.children('div').children('input[type=file]');
        //control.replaceWith( control = control.clone( true ) );

    }else{
        $('.error-msg').fadeOut();

        var fileDoc = $('.fileDoc');

        var container = container;
        if (jcrop_api != null){
            jcrop_api.destroy();
            fileDoc.children('img').removeAttr('style');
        }

        fileDoc.children('img').attr('src',data.data.img);
        fileDoc.children('.jcrop-holder').children('img').attr('src',data.data.img);
        var type = $('.fileBox').attr('data-type');

        var maxHeight = 400;
        var maxWidth = 400;
        if (type == 'photoFile'){
            fileDoc.children('img').Jcrop({
                boxHeight: maxHeight,
                boxWidth:  maxWidth,
                onChange:   function(c){showCoords(c, container) },
                onSelect:   function(c){showCoords(c, container) },
                aspectRatio: 1 / 1.285
            },function(){
                jcrop_api = this;
            });
            //console.log(tt = fileDoc.children('.jcrop-holder').children('div').children('div').children('.jcrop-tracker'));
            fileDoc.children('.jcrop-holder').children('div').children('div').children('.jcrop-tracker').addClass('imgareaselect-selection2');
        }else{
            fileDoc.children('img').Jcrop({
                boxHeight:  maxHeight,
                boxWidth:  maxWidth,
                onChange:   function(c){showCoords(c, container) },
                onSelect:   function(c){showCoords(c, container) }
            },function(){
                jcrop_api = this;
            });
        }
    }
}



$(document).ready(function(){

    $( ".slider-vertical-contrast" ).slider({
        range: "min",
        min: -100,
        max: 100,
        value: 0
    });

    $( ".slider-vertical-contrast" ).on( "slidestop", function( event, ui ) {
            var container = $('.file-container');
            //console.log(container);
            var type = $('.fileBox').attr('data-type');
            var contrast = ui.value;
            var brightness = container.children('.fileDoc').children('img').attr('brightness');
            //var contrastNow = container.children('.fileDoc').children('img').attr('contrast');

            container.children('.fileDoc').children('img').attr('contrast',contrast);
            $.ajax({
                url: Routing.generate('setting_image', {'type': type, 'contrast': contrast, 'brightness' : brightness }),
                type: 'POST',
                success: function(msg){ getImage(msg, container); },
                error:function (error) {
                    console.log(error);
                }
            });
            return false;
        }
    );


    $( ".slider-vertical-brightness" ).slider({
        range: "min",
        min: -255,
        max: 255,
        value: 0
    });

    $( ".slider-vertical-brightness" ).on( "slidestop", function( event, ui ) {
            var container = $('.file-container');
            //console.log(container);
            var type = $('.fileBox').attr('data-type');

            var brightness = ui.value;
            var contrast = container.children('.fileDoc').children('img').attr('contrast');
            //var contrastNow = container.children('.fileDoc').children('img').attr('contrast');

            container.children('.fileDoc').children('img').attr('brightness',brightness);
            $.ajax({
                url: Routing.generate('setting_image', {'type': type, 'contrast': contrast, 'brightness' : brightness }),
                type: 'POST',
                success: function(msg){ getImage(msg, container); },
                error:function (error) {
                    console.log(error);
                }
            });
            return false;
        }
    );


    //          Загрузка файла
    var file;


    $('input.fileAjax').on('click', function(event){
        $(this).attr("value", "");
        $(this).val("");
    });

    $('input.fileAjax').on('change', function(event){
        var container = $('.file-container');
        if (container.hasClass('fileAjax')){
            container = container.parent();
        }

        file = event.target.files[0];
        var formData = new FormData();
        formData.append('file', file);

        var type = $(this).attr('data-type');
        $('.fileBox').attr('data-type', type);

        $('body').loader('show',
            {
                className: 'loader',
                tpl: '<div class="{className} hide"><div class="{className}-load"></div><div class="{className}-overlay"></div></div>',
                delay: 200,
                loader: true,
                overlay: true
            }
        );

        $.ajax({
            url: Routing.generate('upload_document', {'type': type}),
            type: 'POST',
            success: function(msg){
                getImage(msg, container);
                $('.navigateFile').css('display','mome');
                $('body').loader('hide');
            },
            error:function (error) {
                console.log(error);
            },
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        });
    });



    $('.rotateLeft').click(function(){
        var container = $('.file-container');
        var type = $('.fileBox').attr('data-type');
        $.ajax({
            url: Routing.generate('image_rotate', {'type': type, 'rotate': 'left'}),
            type: 'POST',
            success: function(msg){ getImage(msg, container); },
            error:function (error) {
                console.log(error);
            }
        });
    });

    $('.rotateRight').click(function(){
        var container = $('.file-container');
        var type = $('.fileBox').attr('data-type');
        $.ajax({
            url: Routing.generate('image_rotate', {'type': type, 'rotate': 'right'}),
            type: 'POST',
            success: function(msg){ getImage(msg, container); },
            error:function (error) {
                console.log(error);
            }
        });
    });

    $('.cropImage').click(function(){
        var container = $('.file-container');
        var x1 = container.children('input[name="x1"]').val();
        var x2 = container.children('input[name="x2"]').val();
        var y1 = container.children('input[name="y1"]').val();
        var y2 = container.children('input[name="y2"]').val();

        var width = container.children('.fileDoc').children('img').css('width');
        var height = container.children('.fileDoc').children('img').css('height');
        var type = $('.fileBox').attr('data-type');
        $.ajax({
            url: Routing.generate('crop_image', {'type': type,  'width' : width, 'height' : height, 'x1': x1, 'y1' : y1, 'x2' : x2, 'y2' : y2 }),
            type: 'POST',
            success: function(msg){ getImage(msg, container); },
            error:function (error) {
                console.log(error);
            }
        });
    });

    $('.saveImage').click(function(){
        var type = $('.fileBox').attr('data-type');
        $.ajax({
            url: Routing.generate('panel_user_save_image', {'userId': $('.fileBox').attr('data-user'),'type': type }),
            type: 'POST',
            success: function(msg){
                console.log('#'+type+'Img');
                console.log(msg);

                $('#'+type+'Img').attr('src','/'+msg);
                $('.fileBox').fadeOut();
            },
            error:function (error) {
                alert('Ошибка сохранения')
            }
        });
    });

    $('.cancelImage').click(function(){
        $('.fileBox').fadeOut();
        var type = $('.fileBox').attr('data-type');
        $.ajax({
            url: Routing.generate('cancel_image', {'type': type }),
            type: 'POST',
            success: function(msg){
                $('#'+type+'Img').attr('src',msg.img);
                $('.fileBox').fadeOut();
                return false;
            },
            error:function (error) {
                alert('Ошибка сохранения');
                return false;
            }
        });
        return false;

    });


    $('.document').click(function(){
        $('.fileBox').attr('data-type', $(this).attr('data-type'));
        $('.fileBox').fadeIn();
        var fileDoc = $('.fileDoc');
        var container = $('.file-container');

        fileDoc.children('img').removeAttr("src").attr("src", $(this).attr('src'));
        if (jcrop_api != null){
            jcrop_api.destroy();
            fileDoc.children('img').removeAttr('style');
        }

        var maxHeight = 400;
        var maxWidth = 400;
        var type = $(this).attr('data-type');
        console.log(type);
        if (type == 'photoFile'){
            fileDoc.children('img').Jcrop({
                boxHeight: maxHeight,
                boxWidth:  maxWidth,
                onChange:   function(c){showCoords(c, container) },
                onSelect:   function(c){showCoords(c, container) },
                aspectRatio: 1 / 1.285
            },function(){
                jcrop_api = this;
            });
            //console.log(tt = fileDoc.children('.jcrop-holder').children('div').children('div').children('.jcrop-tracker'));
            fileDoc.children('.jcrop-holder').children('div').children('div').children('.jcrop-tracker').addClass('imgareaselect-selection2');
        }else{
            fileDoc.children('img').Jcrop({
                boxHeight:  maxHeight,
                boxWidth:  maxWidth,
                onChange:   function(c){showCoords(c, container) },
                onSelect:   function(c){showCoords(c, container) }
            },function(){
                jcrop_api = this;
            });
        }
    });
});