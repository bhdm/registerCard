
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
    //console.log(tt = container);
    //console.log(container.html());
    container.children('input[name="x1"]').val(selection.x);
    container.children('input[name="y1"]').val(selection.y);
    container.children('input[name="x2"]').val(selection.x2);
    container.children('input[name="y2"]').val(selection.y2);
};

//
//function clearCoords()
//{
//    return true;
//};


function getImage(data,container){
    if ( data.data.error != undefined ) {
        $('.error-msg').fadeIn();
        $('.error-msg').html(data.data.error);
        var control = container.children('div').children('input[type=file]');
        //console.log(ttt = control);
        control.replaceWith( control = control.clone( true ) );

    }else{
        $('.error-msg').fadeOut();

        //$(".imgareaselect-selection").parent().remove();
        //$(".imgareaselect-outer").remove();

        var fileDoc = container.children('.fileDoc');

        //fileDoc.html('<img src=""  brightness="0" contrast="0" />');
        var container = container;
        if (jcrop_api != null){
            jcrop_api.destroy();
            fileDoc.children('img').removeAttr('style');
            //jcrop_api.setImage(data.data.img);
        }
        fileDoc.children('img').attr('src',data.data.img);
        fileDoc.children('.jcrop-holder').children('img').attr('src',data.data.img);
        var type = container.children('.jq-file').children('input[type=file]').attr('id');

        if (type == 'photoFile'){
            fileDoc.children('img').Jcrop({
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
                onChange:   function(c){showCoords(c, container) },
                onSelect:   function(c){showCoords(c, container) }
            },function(){
                jcrop_api = this;
            });
        }
        //console.log(w=imgAreaSelect);
    }
}



$(document).ready(function(){

    $( ".slider-vertical-contrast" ).slider({
        orientation: "vertical",
        range: "min",
        min: 0,
        max: 255,
        value: 128
    });

    $( ".slider-vertical-contrast" ).on( "slidestop", function( event, ui ) {
            var container = $(this).parent().parent().parent();
            //console.log(container);
            var type = container.children('.jq-file').children('input[type=file]').attr('id');
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
        orientation: "vertical",
        range: "min",
        min: 0,
        max: 255,
        value: 128
    });

    $( ".slider-vertical-brightness" ).on( "slidestop", function( event, ui ) {
            var container = $(this).parent().parent().parent();
            //console.log(container);
            var type = container.children('.jq-file').children('input[type=file]').attr('id');

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

    $('.fileAjax').on('change', function(event){

        var container = $(this).parent();
        if (container.hasClass('fileAjax')){
            container = container.parent();
        }
        var progressbar = container.children('.progress');
        var navigateFile = container.children('.navigateFile');

        var loader = '/bundles/crmmain/images/ajax_loader.gif';
        container.children('.fileDoc').children('img').attr('src',loader);
        //return false;

        file = event.target.files[0];
        var formData = new FormData();
        formData.append('file', file);
        progressbar.css('display','block');
        progressbar.attr({value:0, max:100});
        var type = container.children('.jq-file').children('input[type=file]').attr('id');
        //console.log(t=container);
        $.ajax({
            url: Routing.generate('upload_document', {'type': type}),
            type: 'POST',
            xhr: function()
            {
                var xhr = new window.XMLHttpRequest();
                //Upload progress
                xhr.upload.addEventListener("progress", function(evt){
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        //Do something with upload progress
                        progressbar.attr({value:evt.loaded,max:evt.total});

                        if ( evt.loaded == evt.total ){
                            var loader = 'bundles/crmmain/images/ajax_loader.gif';
                            //alert(container.children('.fileDoc').children('img').attr('src'));
                            container.children('.fileDoc').children('img').attr('src',loader);
                        }
                    }
                }, false);
                //Download progress
                xhr.addEventListener("progress", function(evt){
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        //Do something with download progress
                        progressbar.attr({value:evt.loaded,max:evt.total});

                        if ( evt.loaded == evt.total ){
                            var loader = 'bundles/crmmain/images/ajax_loader.gif';
                            //alert(container.children('.fileDoc').children('img').attr('src'));
                            container.children('.fileDoc').children('img').attr('src',loader);
                        }
                    }
                }, false);
                return xhr;
            },
            success: function(msg){
                progressbar.css('display','none');
                getImage(msg, container);
                $('.navigateFile').css('display','mome');
                navigateFile.css('display','block');
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
        var container = $(this).parent().parent();
        var type = container.children('.jq-file').children('input[type=file]').attr('id');
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
        var container = $(this).parent().parent();
        var type = container.children('.jq-file').children('input[type=file]').attr('id');
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
        var container = $(this).parent().parent().parent();
        var x1 = container.children('input[name="x1"]').val();
        var x2 = container.children('input[name="x2"]').val();
        var y1 = container.children('input[name="y1"]').val();
        var y2 = container.children('input[name="y2"]').val();

        var width = container.children('.fileDoc').children('img').css('width');
        var height = container.children('.fileDoc').children('img').css('height');
        var type = container.children('.jq-file').children('input[type=file]').attr('id');
        $.ajax({
            url: Routing.generate('crop_image', {'type': type,  'width' : width, 'height' : height, 'x1': x1, 'y1' : y1, 'x2' : x2, 'y2' : y2 }),
            type: 'POST',
            success: function(msg){ getImage(msg, container); },
            error:function (error) {
                console.log(error);
            }
        });
    });

    //$('.brightness').click(function(){
    //    var container = $(this).parent().parent();
    //
    //    var type = container.children('.jq-file').children('input[type=file]').attr('id');
    //    var brightness = $(this).attr('data-brightness');
    //    var brightnessNow = container.children('.fileDoc').children('img').attr('brightness');
    //    if (brightness == 'plus'){
    //        brightnessNow = brightnessNow + 20;
    //    }else{
    //        brightnessNow = brightnessNow - 20;
    //    }
    //    container.children('.fileDoc').children('img').attr('brightness',brightnessNow);
    //    //console.log(t=container);
    //    $.ajax({
    //        url: Routing.generate('brightness_image', {'type': type, 'brightness': brightnessNow }),
    //        type: 'POST',
    //        success: function(msg){ getImage(msg, container); },
    //        error:function (error) {
    //            console.log(error);
    //        }
    //    });
    //});

    //$('.contrast').click(function(){
    //    var container = $(this).parent().parent();
    //
    //    var type = container.children('.jq-file').children('input[type=file]').attr('id');
    //    var contrast = $(this).attr('data-contrast');
    //    var contrastNow = container.children('.fileDoc').children('img').attr('contrast');
    //    if (contrast == 'plus'){
    //        contrastNow = contrastNow + 20;
    //    }else{
    //        contrastNow = contrastNow - 20;
    //    }
    //    container.children('.fileDoc').children('img').attr('contrast',contrastNow);
    //    console.log(t=container);
    //    $.ajax({
    //        url: Routing.generate('contrast_image', {'type': type, 'contrast': contrastNow }),
    //        type: 'POST',
    //        success: function(msg){ getImage(msg, container); },
    //        error:function (error) {
    //            console.log(error);
    //        }
    //    });
    //});

});