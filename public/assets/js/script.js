var baseUrl = $('meta[name="base-url"]').attr('content');
var adminUrl = baseUrl;
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
$(function () {
    "use strict";
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': CSRF_TOKEN},
        error: function (e) {
            console.log(e.responseText);
        },
        complete: function () {
            $('.modal .modal-loader').hide();
            $('.loading-spinner').css('opacity', '1').fadeOut();
        },
        beforeSend: function () {
            if ($('body').hasClass('modal-open')) {
                var loader = $('.modal.fade.in .modal-loader');
                if (!loader.length) {
                    loader = $('<div><i class="fa fa-refresh fa-spinx"></i><div/>').addClass('modal-loader');
                    $('.modal.fade.in .modal-content').prepend(loader);
                }
                loader.show();
            } else {
                $('.loading-spinner').css('opacity', '0.5').fadeIn();
            }
        }
    });

    jconfirm.defaults = {
        closeIcon: true,
        columnClass: "medium",
        animateFromElement: false,
        animation: 'top',
        type: 'blue',
        title: '',
        defaultButtons: {
            ok: {
                action: function () {
                }
            },
            close: {
                text: 'Cancel',
                action: function () {
                }
            },
        }
    };

    toastr.options = {
        closeButton: true,
        newestOnTop: true,
        progressBar: false,
        preventDuplicates: true,
        positionClass: "toast-top-right",
        extendedTimeOut: 1000,
        showDuration: 100,
        hideDuration: 500,
        timeOut: 15000,
        debug: false,
        onclick: null,
        showEasing: 'swing',
        showMethod: 'fadeIn',
        hideEasing: 'linear',
        hideMethod: 'fadeOut'
    };


    if ($.fn.datepicker != undefined) {
        $('.datepicker').datepicker({
            autoclose: true,
            format: "dd-mm-yyyy",
            todayHighlight: true
        });
    }

    if ($.fn.daterangepicker != undefined) {
        $.fn.daterangepicker.defaultOptions = {
            timePicker: false,
            showDropdowns: true,
            drops: "auto",
            opens: 'auto',
            alwaysShowCalendars: true,
            showCustomRangeLabel: false,
            autoUpdateInput: false,
            locale: {
                "format": "DD/MM/YYYY",
                "applyLabel": "Apply",
                "cancelLabel": "Clear",
            },
            ranges: {
                'Today': [moment().startOf('day'), moment().endOf('day')],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'This Year': [moment().startOf('year'), moment().endOf('month')]
            }
        };
    }
    var selected = [];

    function updateSelected(id, add) {
        if (!$("#ids").length) {
            $('table.dataTable').after('<input type="hidden" name="entryIds" id="ids">');
        }
        if (id === 'clear') {
            selected = [];
            $("#scount").text(0);
            $("#ids").val('')
        } else {
            var id = id.replace('dt_', '');
            var index = $.inArray(id, selected);

            if (add && index === -1) {
                selected.push(id);
            }

            if (!add && index > -1) {
                selected.splice(index, 1);
            }

            $("#scount").text("Selected items: " + selected.length);
            $("#ids").val(selected.join(','));
        }
    }

    $.extend(true, $.fn.dataTable.defaults, {
        buttons: [],
        responsive: {
            details: {
                type: 'column',
                target: -1,
                renderer: function (api, rowIdx, columns) {
                    var data = $.map(columns, function (col, i) {
                        return col.hidden ?
                            '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
                            '<td>' + col.title + ':' + '</td> ' +
                            '<td>' + col.data + '</td>' +
                            '</tr>' :
                            '';
                    }).join('');
                    return data ? $('<table class="table p-0 table-sm" />').append(data) : false;
                }
            }
        },
        pageLength: '25',
        searchDelay: 500,
        order: [[0, 'desc']],
        deferRender: true,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search Record"
        },
        dom: "r<'row datatables_buttons'<'col-lg-12 text-right'B>>" +
            "<'row justify-content-between datatables_top mb-2'<'col-md-auto mr-auto'l><'col-md-auto ml-auto'f>>" +
            "<'row justify-content-md-center'<'col-sm-12't>>" +
            "<'row justify-content-between datatables_bottom'<'col-md-auto mr-auto'i><'col-md-auto ml-auto'p>>",
        columnDefs: [
            {target: -1, className: 'dtr-control', orderable: false},
            {targets: [0, 1], responsivePriority: 1},
            {targets: [-1, -2], responsivePriority: 2, orderable: false, searchable: false, className: "text-center nowrap"},
            {targets: 'status', className: "text-center"},
            {targets: 'text-center', className: "text-center"},
            {targets: 'text-right', className: "text-right"},
            {targets: 'nowrap', className: "nowrap"},
            {targets: 'no-sort', orderable: false},
            {targets: 'no-search', searchable: false},
        ],
        rowCallback: function (row, data, displayIndex, displayIndexFull) {
            var checkbox = $(row).find('.dt-checkboxes');
            if (checkbox.length) {
                var id = data.DT_RowId.toString().replace('dt_', '');
                if ($.inArray(id, selected) !== -1) {
                    $(row).find('.dt-checkboxes').prop('checked', true);
                }
                checkbox.on('change', function () {
                    var checked = $(this).prop('checked');
                    updateSelected($(this).val(), checked);
                });
            }
        },
        initComplete: function (settings, json) {
            var filters = $("#filters .form-control:not(.no-filter)");
            if (filters.length) {
                var _api = this.api();
                filters.each(function (i) {
                    $(this).on('change', function () {
                        _api.draw();
                    });
                });
                _api.on('preXhr.dt', function (e, settings, data) {
                    filters.each(function (i) {
                        var _name = $(this).attr('data-column') || $(this).attr('name');
                        data[_name] = $(this).val();
                    });
                })
            }
        },
    });

    $(document).on('init.dt', function (e, settings) {
        var dti = $('.dataTables_filter input');
        var api = new $.fn.dataTable.Api(settings);
        var timer = null;
        var delay = api.settings()[0].searchDelay || 300;
        dti.off().on('input', function (e) {
            clearTimeout(timer);
            var str = this.value;
            timer = setTimeout(function () {
                if (str.length > 2 || e.keyCode == 13) {
                    api.search(str).draw();
                }
                if (str == '') {
                    api.search('').draw();
                }
            }, delay);
            return;
        });
    });

    $('body').on('change', '#massSelectAll', function () {
        var rows, checked;
        rows = $(this).closest('table').find('tbody tr');
        checked = $(this).prop('checked');
        $.each(rows, function () {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            checkbox.prop('checked', checked);
            updateSelected(checkbox.val(), checked);
        });
    });

    $(document).on("wheel", "input[type=number]", function (e) {
        $(this).blur();
    });

    $(".disable").on('click', function (e) {
        e.stopPropagation();
        e.preventDefault();
    });
});

$(document).ready(function (e) {

    $('#fmFrame').on('load', function () {
        $('.modal .modal-loader').css('opacity', '1').fadeOut();
        $('.loader').css('opacity', '1').fadeOut();
    });

    $.fn.passGenerator = function () {
        var field = $(this);
        var confirmField = field.attr('data-confirm-field') || 'confirm_password';

        field.wrap('<div class="input-group"/>');
        var html = $('<div class="input-group-append">' +
            '<button class="btn btn-default btn-generate" type="button">Generate</button>' +
            '<div class="password-generator card panel" style="display:none;width:250px;padding:0;position:absolute;top:99%;right:0;z-index:99999"><div class="card-body panel-body">' +
            '<div class="close" style="position: absolute;top: 1px;right: 2px;font-size: 1em;"><i class="fa fa-times-circle"></i></div>' +
            '<div class="form-group"><div class="input-group input-group-sm">' +
            '<input class="form-control temp-pass"/>' +
            '<div class="input-group-append">' +
            '<button type="button" class="btn btn-default refresh"><i class=" fa fa-refresh"></i></button>' +
            '</div></div>' +
            '</div>' +
            '<div class="text-right">' +
            '<input type="button" class="btn btn-primary btn-block btn-sm use-password" value="Use Password">' +
            '</div></div></div>');
        field.after(html);

        var randomPassword = function (length) {
            var chars = "abcdefghijklmnopqrstuvwxyz!@#$%^&*()-+ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
            var pass = "";
            for (var x = 0; x < length; x++) {
                var i = Math.floor(Math.random() * chars.length);
                pass += chars.charAt(i);
            }
            return pass;
        };

        var wrap = field.closest('.input-group');
        var popup = wrap.find('.password-generator');
        var temp_pass = wrap.find('.temp-pass');
        wrap.find(".btn-generate").on('click', function () {
            if (temp_pass.val() == '') {
                temp_pass.val(randomPassword(15));
            }
            popup.toggle();
        });
        wrap.find('.refresh').on('click', function () {
            temp_pass.val(randomPassword(15));
        });
        wrap.find('.use-password').on('click', function () {
            var _pass = temp_pass.val();
            field.val(_pass);
            if ($('#' + confirmField).length) {
                $('#' + confirmField).val(_pass);
            }
            popup.hide();
        });
        popup.find(".close").on('click', function () {
            popup.hide();
        });
    };

    $(".generate-password").passGenerator();

    if (jQuery.validator) {
        var script = $('<script type="text/javascript" src="' + baseUrl + '/assets/plugins/jquery-validation/additional-methods.min.js">');
        $("script[src*='jquery-validate']").last().after(script);
        var getWrapper = function (element) {
            var ewrap = $(element).closest('.form-group');
            if (!ewrap.length) {
                if ($(element).parent('td').length) {
                    ewrap = $(element).parent('td');
                } else if ($(element).closest('[class^="col-"]').length) {
                    ewrap = $(element).closest('[class^="col-"]');
                }
            }
            return ewrap;
        };

        jQuery.validator.setDefaults({
            debug: false,
            errorClass: 'error',
            pendingClass: 'pending',
            validClass: 'valid',
            errorElement: 'span',
            ignore: 'input[type=hidden], .select2-input, .select2-focusser',
            rules: {
                password: {
                    minlength: 6
                },
                confirm_password: {
                    required: function (element) {
                        return $("#password").val().length > 0;
                    },
                    minlength: 6,
                    equalTo: "#password"
                }
            },
            errorPlacement: function (error, element) {
                // render error placement for each input type
                var ewrap = getWrapper(element);
                ewrap.addClass('has-error');

                var inputgr = ewrap.find('.input-group, .form-check, .radio');
                var inputfile = ewrap.find('.fileinput');

                element.addClass('validate');
                error.addClass('help-block invalid-tooltip');
                if (inputgr.length) {
                    error.addClass('left');
                    error.insertAfter(inputgr);
                } else if (inputfile.length) {
                    error.addClass('left');
                    error.insertAfter(inputfile.last());
                } else {
                    error.insertAfter(element);
                }
            },

            highlight: function (element, errorClass, validClass) {
                var elem = $(element);
                elem.removeClass('valid').addClass('error');
                var ewrap = getWrapper(element);
                var inputgr = ewrap.find('.input-group, .form-check, .radio');

                if (inputgr.length) {
                    if (ewrap.find('.validate.error').length < 1) {
                        ewrap.addClass('has-error');
                    }
                } else {
                    ewrap.addClass('has-error');
                }
                ewrap.addClass('has-error');
                if (element.type === 'radio') {
                    this.findByName(element.name).addClass(errorClass).removeClass(validClass);
                } else {
                    elem.addClass(errorClass).removeClass(validClass);
                }
            },
            unhighlight: function (element, errorClass, validClass) {
                var elem = $(element);
                elem.removeClass('error').addClass('valid');
                var ewrap = getWrapper(element);

                var inputgr = ewrap.find('.input-group, .form-check, .radio');

                if (inputgr.length) {
                    if (ewrap.find('.validate.error').length < 1) {
                        ewrap.removeClass('has-error');
                    }
                } else {
                    ewrap.removeClass('has-error');
                }
                if (element.type === 'radio') {
                    this.findByName(element.name).removeClass(errorClass).addClass(validClass);
                } else {
                    elem.removeClass(errorClass).addClass(validClass);
                }
                elem.closest('.card').removeClass('has-error');
            },
            invalidHandler: function (e, validator) {
                var errors = validator.numberOfInvalids();
                var hidden = $(validator.currentForm).find('[required]').not(':visible').length;
                if (errors && hidden) {
                    toastr.options.timeOut = 3000;
                    var message = errors == 1
                        ? 'You missed 1 field. It has been highlighted below'
                        : 'You missed ' + errors + ' fields.  They have been highlighted below';
                    toastr.error(message);
                }
                setTimeout(function () {
                    $('.form-control.error:disabled').removeClass('error').closest('.has-error').removeClass('has-error');
                    if ($(validator.currentForm).find('.card [data-toggle="collapse"]').length) {
                        $('.card').removeClass('has-error');
                        $(validator.currentForm).find('.card .card-body:has(.form-control.error:not(:disabled))').each(function () {
                            $(this).closest('.card').addClass('has-error')
                        });
                    }
                    $('#tabs .nav-tabs a').removeClass('tab-has-error');
                    var validatePane = $('#tabs .tab-content .tab-pane:has(.form-control.error:not(:disabled))').each(function () {
                        var id = $(this).closest('.tab-pane').attr('id')
                        $('[href="#' + id + '"]').addClass('tab-has-error');
                    });
                }, 100);
            },
            success: function (label, element) {
                label.parent().removeClass('has-error');
                label.remove();
            },
            submitHandler: function (form) {
                form.submit();
            }
        });
        $.validator.addMethod('filesize', function (value, element, param) {
            return this.optional(element) || (element.files[0].size <= param)
        }, 'File size must be less than {0}');

        $("form.validate-form,form.validate").validate();
    }

    //set behaviour of link as post
    $(document).on('click', "[data-delete], [data-method], [data-confirm]", function (e) {
        e.stopPropagation();
        e.preventDefault();
        var self = $(this);
        var disabled = $(this).attr('disabled') || false;
        var url = this.href || $(this).attr('data-action');
        if (url && !disabled) {
            var method = $(this).attr('data-method');
            var message = $(this).attr('data-confirm');
            var title = $(this).attr('title') || '';

            if (method) {
                method = method.toUpperCase()
            }

            var form_action = function (url, method) {
                var form = $('<form></form>').attr('id', uniqueId()).attr('action', url).attr('method', 'POST').attr('role', 'form');
                var _token = $("<input>").attr('type', 'hidden').attr('name', '_token').attr('value', CSRF_TOKEN);
                form.append(_token);
                if (method === 'DELETE') {
                    var _method = $("<input>").attr('type', 'hidden').attr('name', '_method').attr('value', method);
                    form.append(_method);
                }

                var _params = getUrlParams(url);
                $.each(_params, function (i, v) {
                    var _field = $("<input>").attr('type', 'hidden').attr('name', i).attr('value', v);
                    form.append(_field);
                });
                $("body").append(form);
                form.submit();
            }
            if (message) {
                $.confirm({
                    title: title,
                    content: message,
                    type: 'red',
                    theme: 'modern',
                    buttons: {
                        ok: {
                            text: 'Yes',
                            btnClass: 'btn-success',
                            action: function () {
                                if (method) {
                                    form_action(url, method);
                                } else {
                                    location.href = url;
                                }
                            }
                        },
                        cancel: {
                            text: 'No',
                            btnClass: 'btn-default',
                        },
                    }
                });
            } else {
                if (method) {
                    form_action(url, method);
                } else {
                    location.href = url;
                }
            }
        } else {
            return false;
        }
    });

    $(document).on('click', '.ajax-request', function (e) {
        e.preventDefault();
        /* prevents the submit or reload */
        var confirmation = confirm("Are you sure you want to perform this action?");
        if (confirmation) {
            saveAjaxRequest(adminUrl, this);
        }
    });

    $(document).on('click', '#darkMode', function () {
        var _ischecked = $(this).is(':checked');
        if (_ischecked) {
            $('.main-header').removeClass('navbar-white navbar-light').addClass('navbar-dark');
            $('body').addClass('dark-mode');
        } else {
            $('.main-header').addClass('navbar-white navbar-light').removeClass('navbar-dark');
            $('body').removeClass('dark-mode');
        }
        cookie('dark-mode', _ischecked ? 1 : 0, 360);
    });

    $('#slug, input.slugify').on('keyup', function () {
        var space = $(this).data('space');
        $(this).val(slugify($(this).val(), space));
    }).on('blur', function () {
        $(this).val($(this).val().replace(/-+$/, ''));
    });
});

function cookie(cname, cvalue, exdays) {
    if (cvalue == undefined) {
        let name = cname + "=";
        let ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    } else {
        const d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        let expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }
}

function saveAjaxRequest(adminUrl, el) {

    var $self = $(this);
    /* magic here! */

    /* Get database info */
    var field = $(el).data('field');
    var id = $(el).data('id');
    var value = $(el).data('value');
    var table = $(el).data('table');
    var model = $(el).data('model');
    var line_id = $(el).attr('id');

    /* Remove dot (.) from var (referring to the PHP var) */
    line_id = line_id.split('.').join("");

    $.ajax({
        method: 'POST',
        url: adminUrl + '/ajax-request/' + table + '/' + field + '',
        context: this,
        data: {'primaryKey': id, 'model': model, 'value': value}
    }).done(function (data) {
        if (data.status != 1) {
            if (data.message) {
                toastr.error(data.message);
            }
            return false;
        }

        if (data.fieldValue == 1) {
            $('#' + line_id).find('.icon').removeClass('fa fa-toggle-off').addClass('fa fa-toggle-on').blur();
        } else {
            $('#' + line_id).find('.icon').removeClass('fa fa-toggle-on').addClass('fa fa-toggle-off').blur();
        }

        var label = '';
        if ($('#' + line_id).find('span').length) {
            label = $('#' + line_id).find('span')
        }

        /* Decoration */
        if (label != '' && label.length) {
            if (data.fieldValue == 1) {
                label.attr('class', 'badge badge-success').text('Active');
            } else {
                label.attr('class', 'badge badge-danger').text('InActive');
            }
        }

        return false;
    }).fail(function (xhr, textStatus, errorThrown) {
        /* Show an alert with the result */
        if (typeof xhr.responseText !== 'undefined') {
            if (xhr.responseText.indexOf("Unauthorized") >= 0) {
                toastr.error(xhr.responseText);
                return false;
            }
        }
        /* Show an alert with the standard message */
        toastr.error(xhr.responseText);
        return false;
    });

    return false;
}

if (typeof String.prototype.ucfirst != 'function') {
    String.prototype.ucfirst = function () {
        return this.charAt(0).toUpperCase() + this.slice(1);
    }
}

//  Checks that string starts with the specific string
if (typeof String.prototype.startsWith != 'function') {
    String.prototype.startsWith = function (str) {
        return this.slice(0, str.length) == str;
    };
}

//  Checks that string ends with the specific string...
if (typeof String.prototype.endsWith != 'function') {
    String.prototype.endsWith = function (str) {
        return this.slice(-str.length) == str;
    };
}

function getUrlParams(url, keys) {
    if (url === undefined) {
        url = window.location.href;
    }
    var pieces = url.split("?");
    var output = {};
    if (pieces[1]) {
        var params = pieces[1].split("&");
        params.forEach(function (it) {
            if (it) {
                var param = it.split("=");
                output[param[0]] = param[1] || null;
            }
        });
    }
    if (keys) {
        output = Object.keys(output).join(',');
    }
    return output;
}

function uniqueId() {
    function chr4() {
        return Math.random().toString(16).slice(-4);
    }

    return chr4() + chr4() + '-' + chr4() + '-' + chr4() + '-' + chr4() + '-' + Date.now();
}

function slugify(text, space_delemeter) {
    const a = 'àáäâãèéëêìíïîòóöôùúüûñçßÿœæŕśńṕẃǵǹḿǘẍźḧ·/_,:;';
    const b = 'aaaaaeeeeiiiioooouuuuncsyoarsnpwgnmuxzh------';
    const p = new RegExp(a.split('').join('|'), 'g');
    if (space_delemeter == undefined) {
        space_delemeter = '-'
    }
    return text.toString()
        .toLowerCase()
        .replace(/\s+/g, space_delemeter)           // Replace spaces with -
        .replace(p, c => b.charAt(a.indexOf(c)))     // Replace special chars
        .replace(/&/g, space_delemeter + 'and' + space_delemeter)         // Replace & with 'and'
        .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
        .replace(/\-\-+/g, '-')         // Replace multiple - with single -
        .replace(/^-+/, '')             // Trim - from start of text
    //.replace(/-+$/, '')             // Trim - from end of text
}

function limit(text, count, dots) {
    if (dots == undefined) {
        dots = true
    }
    return text.slice(0, count) + (((text.length > count) && dots) ? "..." : "");
}
