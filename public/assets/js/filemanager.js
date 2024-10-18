var image_base_url = window.location.protocol + '//' + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
if (image_base_url && image_base_url.substr(-1) === '/') {
    image_base_url = image_base_url.substr(0, image_base_url.length - 1);
}
$(function () {
    "use strict";
    $.fn.filemanager = function (options) {

        var defaults = {
            wrapprer_class: 'fm-wrapper'
        };
        options = $.extend({}, defaults, options);

        String.prototype.trim = function (c) {
            var re = new RegExp("^[" + c + "]+|[" + c + "]+$", "g");
            return this.replace(re, "");
        };

        var remove_dots = function (str) {
            var re = new RegExp("^[.]+|[.]+$", "g");
            str = str.replace(re, "");
            return str;
        };

        var getType = function (type) {
            switch (type) {
                case 'all':
                case 'image':
                    type = 1;
                    break;
                case 'audio':
                case 'video':
                    type = 3;
                    break;
                default:
                    type = 2;
            }
            return type;
        };

        $(this).each(function (i) {
            var field = $(this);
            if (field.attr('type') === 'text' && !field.closest('.' + options.wrapprer_class).length) {
                var id = field.attr('id'),
                    type = getType(field.data('type')),
                    multiple = (field.data('multiple') || 0),
                    key = (field.data('rfm-key') || $('meta[name="rfm-key"]').attr('content') || ''),
                    basefolder = (field.data('basefolder') || field.data('base-folder') || ''),
                    subfolder = (field.data('subfolder') || ''),
                    val = field.val(),
                    src = field.attr('data-src');

                if (!val) {
                    val = field.attr('data-src');
                }

                if (id == undefined || id == '') {
                    id = 'fm_' + Math.floor(Math.random() * 26) + Date.now();
                }

                field.attr({
                    'autocomplete': 'off',
                    'autocorrect': 'off',
                    'spellcheck': 'false',
                    'id': id
                });

                field.wrap('<div class="' + options.wrapprer_class + '"><div class="input-group"></div></div>');

                var wraper = field.parents('.' + options.wrapprer_class);

                var btns = '<div class="input-group-append input-group-btn">';
                btns += '<button type="button" class="btn btn-default empty"><i class="fa fa-times"></i> </button>';
                btns += '<button type="button" class="btn btn-default preview"><i class="fa fa-eye"></i> </button>';
                btns += '<button class="btn btn-info browse" onclick="open_modal(\'' + id + '\',' + type + ',' + multiple + ',\'' + basefolder + '\',\'' + subfolder + '\',\'' + key + '\')" type="button">' + (val ? 'Change' : 'Select') + '</button></div>';

                wraper.find('.input-group').append(btns);

                wraper.find('.preview').on('click', function () {
                    wraper.find('.fm-preview').show();
                });

                wraper.find('.empty').on('click', function () {
                    field.val('');
                    wraper.find('.fm-preview').hide().find('.fm-body').text('');
                    wraper.find('.preview, .empty').hide();
                    wraper.find('.browse').text('Select');
                });

                wraper.append('<div class="fm-preview" style="display: none"><div class="fm-inner"><span class="close">&times;</span><div class="fm-body"></div></div></div>');

                wraper.find('.fm-preview, .fm-preview .close').on('click', function ($e) {
                    var popup;
                    if ($(event.target).is($(".fm-preview"))) {
                        popup = $(this);
                        popup.find(".inner").off();
                    } else {
                        popup = $(this).parents('.fm-preview');
                    }
                    popup.hide();
                    popup.find('audio, video').each(function () {
                        this.pause();
                        this.currentTime = 0;
                    });
                });

                field.bind('cut paste keypress keydown', function (e) {
                    e.preventDefault();
                    return false;
                }).change(function () {
                    if (this.value) {
                        wraper.find('.preview, .empty').show();
                        wraper.find('.browse').text('Change');
                    } else {
                        wraper.find('.fm-preview .fm-body').html('');
                        wraper.find('.preview, .empty').hide();
                        wraper.find('.browse').text('Select');
                    }
                });

                if (val) {
                    var file_type = filetype(val);
                    if (!val.startsWith(image_base_url)) {
                        val = image_base_url + rfmFixUrl(val);
                    }

                    if (file_type === 'image' && !wraper.find('.fm-preview img').length) {
                        wraper.find('.fm-preview .fm-body').html('<img src="' + val + '" />');
                    }

                    if (file_type === 'audio' && !wraper.find('.fm-preview img').length) {
                        wraper.find('.fm-preview .fm-body').html('<audio controls><source src="' + val + '" type="audio/mpeg"></audio>');
                    }

                    if (file_type === 'video' && !wraper.find('.fm-preview img').length) {
                        wraper.find('.fm-preview .fm-body').html('<video controls><source src="' + val + '" type="audio/mpeg"></video>');
                    }
                } else {
                    wraper.find('.preview, .empty').hide();
                }
            }
        });
    };

    $("input.filemanager").filemanager();
});


function filetype(file) {
    if (file !== undefined) {
        if (file.match(/\.(jpg|jpeg|png|gif|bmp|tiff|webp)$/)) {
            return 'image';
        } else if (file.match(/\.(mp3|wav|wma|aac)$/)) {
            return 'audio';
        } else if (file.match(/\.(mp4)$/)) {
            return 'video';
        }
    }
    return false;
}
function rfmFixUrl(url){
    if (url && url.substr(1) !== '/') {
        url = "/" +url;
    }
    return url;
}
function filemanager_callback(field_id) {
    var field = $('#' + field_id);
    var url = field.data('src') || field.val();
    var file_type = filetype(url);

    if (!url.startsWith(image_base_url)) {
        url = image_base_url + rfmFixUrl(url)
    }
    field.trigger('change');
    var wrapper = field.parents('.fm-wrapper');
    if (wrapper.find('.fm-preview').length) {
        if (file_type === 'image') {
            wrapper.find('.fm-preview .fm-body').html('<img src="' + url + '" />');
        }
        if (file_type === 'audio') {
            wrapper.find('.fm-preview .fm-body').html('<audio controls><source src="' + url + '" type="audio/mpeg"></audio>');
        }
        if (file_type === 'video') {
            wrapper.find('.fm-preview .fm-body').html('<video controls><source src="' + url + '" type="video/mp4"></video>');
        }
        if (jQuery.inArray(file_type, ['image', 'audio', 'video']) !== -1) {
            wrapper.find('.preview, .empty').show();
        }
    }
}

function open_modal(field, type, multiple, basefolder, subfolder, key) {
    var modal = $("#rfmModal");
    var url = image_base_url + '/filemanager/dialog.php?type=' + type + '&akey=' + key + '&callback=filemanager_callback&field_id=' + field;
    if (multiple) {
        url += '&multiple=' + multiple;
    }
    if (basefolder) {
        url += '&base_fldr=' + basefolder;
    }
    if (subfolder) {
        url += '&fldr=' + subfolder;
    }
    if (modal.length) {
        if (modal.find('iframe').attr('src') != url) {
            modal.find('iframe').attr('src', url);
            modal.find('.modal-loader').css('opacity', '1').fadeIn();
        }
    } else {
        var h = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
        h = h < 500 ? 500 : (h - 100);
        var html = '<div class="modal fade" id="rfmModal">' +
            '<div class="modal-dialog modal-xl modal-lg modal-full"><div class="modal-content">' +
            '<div class="modal-header" style="padding: 0 1rem;">' +
            //'<h4 class="modal-title">File Manager</h4>' +
            '<button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>' +
            '</div>' +
            '<div class="modal-body" style="padding:0; margin:0; width: 100%;">' +
            '<div class="modal-loader" style="display: block"><i class="fa fa-refresh fa-spinx"></i></div>' +
            '<iframe id="fmFrame" width="100%" height="' + h + '" src="' + url + '" frameborder="0" style="overflow: scroll; overflow-x: hidden; overflow-y: scroll; min-height: ' + h + 'px"></iframe>' +
            '</div>' +
            '</div>' +
            '</div></div>';

        modal = $(html);
        $('body').append(modal);
    }
    modal.find('iframe').on('load', function () {
        modal.find('.modal-loader').css('opacity', '1').fadeOut();
    });
    modal.modal();
}

//  Checks that string starts with the specific string
if (typeof String.prototype.startsWith != 'function') {
    String.prototype.startsWith = function (str) {
        return this.slice(0, str.length) == str;
    };
}
