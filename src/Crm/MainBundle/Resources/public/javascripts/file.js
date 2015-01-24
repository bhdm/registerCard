function getImage(data,container){
    if ( data.data.error != undefined ) {
        $('.error-msg').fadeIn();
        $('.error-msg').html(data.data.error);
    }else{
        $('.error-msg').fadeOut();
        $(".imgareaselect-selection").parent().remove();
        $(".imgareaselect-outer").remove();
        var fileDoc = container.children('.fileDoc');

        fileDoc.html('<img src=""  brightness="0" contrast="0" />');
        fileDoc.children('img').attr('src',data.data.img);
        var imgAreaSelect = fileDoc.children('img').imgAreaSelect({
            handles: true,
            onSelectEnd: function (img, selection) {
                container.children('input[name="x1"]').val(selection.x1);
                container.children('input[name="y1"]').val(selection.y1);
                container.children('input[name="x2"]').val(selection.x2);
                container.children('input[name="y2"]').val(selection.y2);
            }
        });
        console.log(w=imgAreaSelect);
    }
}



$(document).ready(function(){
    //          Загрузка файла
    var file;

    $('.fileAjax').on('change', function(event){
        var container = $(this).parent();
        if (container.hasClass('fileAjax')){
            container = container.parent();
        }
        var progressbar = container.children('.progress');
        var navigateFile = container.children('.navigateFile');

        file = event.target.files[0];
        var formData = new FormData();
        formData.append('file', file);
        progressbar.css('display','block');
        progressbar.attr({value:0, max:100});
        var type = container.children('.jq-file').children('input[type=file]').attr('id');
        console.log(t=container);
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
                    }
                }, false);
                //Download progress
                xhr.addEventListener("progress", function(evt){
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        //Do something with download progress
                        progressbar.attr({value:evt.loaded,max:evt.total});
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
        var container = $(this).parent().parent();
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

    $('.brightness').click(function(){
        var container = $(this).parent().parent();

        var type = container.children('.jq-file').children('input[type=file]').attr('id');
        var brightness = $(this).attr('data-brightness');
        var brightnessNow = container.children('.fileDoc').children('img').attr('brightness');
        if (brightness == 'plus'){
            brightnessNow = brightnessNow + 20;
        }else{
            brightnessNow = brightnessNow - 20;
        }
        container.children('.fileDoc').children('img').attr('brightness',brightnessNow);
        console.log(t=container);
        $.ajax({
            url: Routing.generate('brightness_image', {'type': type, 'brightness': brightnessNow }),
            type: 'POST',
            success: function(msg){ getImage(msg, container); },
            error:function (error) {
                console.log(error);
            }
        });
    });

    $('.contrast').click(function(){
        var container = $(this).parent().parent();

        var type = container.children('.jq-file').children('input[type=file]').attr('id');
        var contrast = $(this).attr('data-contrast');
        var contrastNow = container.children('.fileDoc').children('img').attr('contrast');
        if (contrast == 'plus'){
            contrastNow = contrastNow + 20;
        }else{
            contrastNow = contrastNow - 20;
        }
        container.children('.fileDoc').children('img').attr('contrast',contrastNow);
        console.log(t=container);
        $.ajax({
            url: Routing.generate('contrast_image', {'type': type, 'contrast': contrastNow }),
            type: 'POST',
            success: function(msg){ getImage(msg, container); },
            error:function (error) {
                console.log(error);
            }
        });
    });


});