function getImage(data){
    if ( data.data.error != undefined ) {
        $('.error-msg').fadeIn();
        $('.error-msg').html(data.data.error);
    }else{
        $('.error-msg').fadeOut();
        $(".imgareaselect-selection").parent().remove();
        $(".imgareaselect-outer").remove();

        $('#file').html('<img src="" />');
        $('#file').children('img').attr('src',data.data.img);
        $('#file img').imgAreaSelect({
            handles: true,
            onSelectEnd: function (img, selection) {
                $('input[name="x1"]').val(selection.x1);
                $('input[name="y1"]').val(selection.y1);
                $('input[name="x2"]').val(selection.x2);
                $('input[name="y2"]').val(selection.y2);
            }
        });
    }
}



$(document).ready(function(){
    //          Загрузка файла
    var file;

    $('.fileAjax').on('change', function(event){
        file = event.target.files[0];
        var formData = new FormData();
        formData.append('file', file);
        $('#progress').css('display','block');
        $.ajax({
            url: Routing.generate('upload_document', {'type': 'passport'}),
            type: 'POST',
            xhr: function()
            {
                var xhr = new window.XMLHttpRequest();
                //Upload progress
                xhr.upload.addEventListener("progress", function(evt){
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        //Do something with upload progress
                        $('#progress').attr({value:evt.loaded,max:evt.total});
                        console.log(percentComplete);
                    }
                }, false);
                //Download progress
                xhr.addEventListener("progress", function(evt){
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        //Do something with download progress
                        $('#progress').attr({value:evt.loaded,max:evt.total});
                        console.log(percentComplete);
                    }
                }, false);
                return xhr;
            },
            success: function(msg){
                $('#progress').css('display','none');
                getImage(msg);
                $('.navigateFile').css('display','block');
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



    $('#rotateLeft').click(function(){
        $.ajax({
            url: Routing.generate('rotate_image', {'type': 'left'}),
            type: 'POST',
            success: function(msg){ getImage(msg); },
            error:function (error) {
                console.log(error);
            }
        });
    });

    $('#rotateRight').click(function(){
        $.ajax({
            url: Routing.generate('rotate_image', {'type': 'right'}),
            type: 'POST',
            success: function(msg){ getImage(msg); },
            error:function (error) {
                console.log(error);
            }
        });
    });

    $('#cropImage').click(function(){
        var x1 = $('#x1').val();
        var x2 = $('#x2').val();
        var y1 = $('#y1').val();
        var y2 = $('#y2').val();
        $.ajax({
            url: Routing.generate('crop_image', { 'x1': x1, 'y1' : y1, 'x2': x2, 'y2': y2 }),
            type: 'POST',
            success: function(msg){ getImage(msg); },
            error:function (error) {
                console.log(error);
            }
        });
    });

    $('#removeImage').click(function(){
        var x1 = $('#x1').val(0);
        var x2 = $('#x2').val(0);
        var y1 = $('#y1').val(0);
        var y2 = $('#y2').val(0);
        //$.ajax({
        //    url: Routing.generate('crop_image', { 'x1': x1, 'y1' : y1, 'x2': x2, 'y2': y2 }),
        //    type: 'POST',
        //    success: function(msg){ getImage(msg); },
        //    error:function (error) {
        //        console.log(error);
        //    }
        //});
        $('.fileAjax').parent().html($('.fileAjax').parent().html());
        $('.jq-file__name').val('Выберите файл');
        $('#file img').attr('src','');
        $('#progress').css('display','block');
        $('#progress').val(0);
        $('.navigateFile').css('display','none');
        console.log('clear input file');
    });

});