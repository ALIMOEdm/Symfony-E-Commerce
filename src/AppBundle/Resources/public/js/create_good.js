/**
 * Created by php05 on 09.12.15.
 */

function GoodFile(){
    this.files = {};
}

GoodFile.prototype.add = function(name, type, file){
    this.files[name] = {
        file : file,
        type: type,
        name: name
    };
};

GoodFile.prototype.toJsonString = function(inp){
    return JSON.stringify(this.files);
};

GoodFile.prototype.remove = function(name){
    if(this.files[name]){
        delete this.files[name];
        return true;
    }
    return false;
};

var files_cache = new GoodFile();

$(document).on('click', '[data-role="good-choose-photo"]', function(event){
    $('[data-role="good_image_field"]').trigger('click');
});

$(document).on('change', '[data-role="good_image_field"]', function(event){
    var input = event.target;
    var reader = new FileReader();
    var arr = [];
    for(var i = 0; i < input.files.length; i++){
        (function(){
            arr[i] = new FileReader();
            var file_name = input.files[i].name;
            var file_type = input.files[i].type;
            arr[i].onload = function(event){
                files_cache.add(file_name, file_type, event.target.result);
                var tpl = photoTemplate({src: event.target.result, photo_title:file_name});
                $('.photo_gallery').append(tpl);
            };
            arr[i].readAsDataURL(input.files[i]);
        })()
    }
});

function photoTemplate(object){
    var tpl = $('#photoTemplate').text();
    for(var prop in object){
        if(object.hasOwnProperty(prop)){
            tpl = tpl.replace(new RegExp("<%"+prop+"=%>",'gi'), object[prop]);
        }
    }
    return tpl;
}


$(document).on('submit', '[name="create_good"]', function(event){
    $('[data-role="good_image_hidden_field"]').val(files_cache.toJsonString());

    var extra_fields = {};
    $('[data-role="good_extra_field"]').each(function (index, value) {
        extra_fields[$(value).data('xml_name')] = {
            id: $(value).data('identity'),
            value: $(value).val()
        }
    });

    $('[data-role="good_hidden_extra_fields"]').val(JSON.stringify(extra_fields));

    //event.preventDefault();
});

$(document).on('change', '[data-role="good_create_category_list"]', function(event){
    getExtraAttributeList();
});

$(document).ready(function(){
    getExtraAttributeList();
});

function getExtraAttributeList(){
    var category_id = $('[data-role="good_create_category_list"]').val();

    var data_to_server = {
        category_id: category_id
    };

    if(window.good_id){
        data_to_server['id'] = window.good_id;
    }

    $.ajax({
        type: 'post',
        url: routes['get_category_extra_fields'],
        data: data_to_server,
        success: function(data){
            if(data.html){
                $('[data-role="extra_fields_wrapper"]').html(data.html);
            }
        },
        error: function(){}
    })
}

$(document).on('click', '[data-role="remove-fish-photo"]', function(event){

    var el = event.target;
    var remove_url = $(el).data('remove_url');
    var photo_name = $(el).data('photo_title');
    if(!remove_url){
        remove_url = $(el).parents('[data-role="remove-fish-photo"]').data('remove_url');
        photo_name = $(el).parents('[data-role="remove-fish-photo"]').data('photo_title');
    }

    if(!remove_url){
        if(files_cache.remove(photo_name)){
            var elem = $(el).parents('.good-photo-wrapper');
            elem.hide('slow').remove();
            return;
        }else{
            alert('При удалении произошла ошибка');
        }

        return;
    }

    if(!confirm('Вы уверены, что ходите удалить эту фотографию??')){
        return;
    }

    $.ajax({
        type: 'post',
        url: remove_url,
        success: function (data) {
            if(data.error !== undefined && !data.error) {
                var elem = $(el).parents('.good-photo-wrapper');
                elem.hide('slow').remove();
            }else if(data.error){
                alert(data.message);
            }
        },
        error: function () {
        }
    });
});