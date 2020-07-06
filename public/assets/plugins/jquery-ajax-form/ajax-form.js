$(function () {
    if ($.isFunction($.fn.validate)) {
        $("form.validate").each(function (i, el) {
            var $this = $(el),
                opts = {
                    rules: {},
                    messages: {},
                    errorElement: 'span',
                    errorClass: 'help-block',
                    highlight: function (element) {
                        $(element).closest('.form-group').addClass('has-error');
                    },
                    unhighlight: function (element) {
                        $(element).closest('.form-group').removeClass('has-error');
                    },
                    errorPlacement: function (error, element) {
                        if (element.closest('.has-switch').length) {
                            error.insertAfter(element.closest('.has-switch'));
                        }
                        else if (element.parent('.checkbox, .radio').length || element.parent('.input-group').length) {
                            error.insertAfter(element.parent());
                        }
                        else {
                            error.insertAfter(element);
                        }
                    }
                },
                $fields = $this.find('[data-validate]');


            $fields.each(function (j, el2) {
                var $field = $(el2),
                    name = $field.attr('name'),
                    validate = attrDefault($field, 'validate', '').toString(),
                    _validate = validate.split(',');

                for (var k in _validate) {
                    var rule = _validate[k],
                        params,
                        message;

                    if (typeof opts['rules'][name] === 'undefined') {
                        opts['rules'][name] = {};
                        opts['messages'][name] = {};
                    }

                    if ($.inArray(rule, ['required', 'url', 'email', 'number', 'date', 'creditcard', 'digits']) !== -1) {
                        opts['rules'][name][rule] = true;

                        message = $field.data('message-' + rule);

                        if (message) {
                            opts['messages'][name][rule] = message;
                        }
                    }
                    // Parameter Value (#1 parameter)
                    else if (params = rule.match(/(\w+)\[(.*?)\]/i)) {
                        if ($.inArray(params[1], ['min', 'max', 'minlength', 'maxlength', 'equalTo']) !== -1) {
                            opts['rules'][name][params[1]] = params[2];


                            message = $field.data('message-' + params[1]);

                            if (message) {
                                opts['messages'][name][params[1]] = message;
                            }
                        }
                    }
                }
            });

            $this.validate(opts);
        });

        if ($.isFunction($.fn.ajaxForm)) {
            $("form.ajax-form").each(function (i, el) {
                var $form = $(el);
                $form.ajaxForm({
                    dataType: 'json', success: function (data) {
                        try {
                            if (typeof show_loading_bar !== 'undefined' && $.isFunction(show_loading_bar)) {
                                show_loading_bar(100);
                            }
                            $form.data('updated', 'true');
                            if (data.hasOwnProperty('redirect')) {
                                location.href = data.redirect;
                            }
                            if (data.hasOwnProperty('message')) {
                                $form.find(".alert")
                                     .removeClass()
                                     .addClass('alert alert-success')
                                     .html(data.message)
                                     .fadeOut()
                                     .fadeIn();
                            }
                            if ($form.attr('data-callback')) {
                                window[$form.data('callback')]($form, data);
                            }
                        } catch (e) {
                            console.log(e);
                        }
                    }, error: function (data) {
                        try {
                            if (typeof show_loading_bar !== 'undefined' && $.isFunction(show_loading_bar)) {
                                show_loading_bar(100);
                            }
                            if (data.hasOwnProperty('responseJSON') && data.responseJSON.hasOwnProperty('message')) {
                                $form.find(".alert")
                                     .removeClass()
                                     .addClass('alert alert-danger')
                                     .html(data.responseJSON.message)
                                     .fadeOut()
                                     .fadeIn();
                            }
                            if (data.status === 422 && data.hasOwnProperty('responseJSON')) {
                                if (data.responseJSON.hasOwnProperty('errors')) {
                                    $form.validate().showErrors(transformErrors(data.responseJSON.errors));
                                } else {
                                    $form.validate().showErrors(transformErrors(data.responseJSON));
                                }
                            }
                            if (data.hasOwnProperty('responseJSON') && data.responseJSON.hasOwnProperty('errors')) {
                                $form.validate().showErrors((data.responseJSON.errors));
                            }
                            if (data.hasOwnProperty('responseJSON') && data.responseJSON.hasOwnProperty('redirect')) {
                                location.href = data.responseJSON.redirect;
                            }
                        } catch (e) {
                            console.log(e);
                        }
                    }, beforeSend: function () {
                        if (typeof show_loading_bar !== 'undefined' && $.isFunction(show_loading_bar)) {
                            show_loading_bar(65);
                        }
                    }
                });
            });
        }
    }

    $('.confirm-form').on('click', function (e) {
        var form = $(this).closest('form');
        e.preventDefault();
        BootstrapDialog.confirm({
            title: 'Action Confirmation',
            message: 'Do you really want to perform this action?',
            type: BootstrapDialog.TYPE_DANGER,
            closable: true,
            draggable: false,
            btnCancelLabel: 'Close',
            btnOKLabel: 'Confirm',
            btnOKClass: 'btn-danger',
            callback: function (result) {
                if (result) {
                    form.submit();
                }
            }
        });
    });
});

function transformErrors(errors) {
    var formattedErrors = {};
    $.each(errors, function (k, v) {
        formattedErrors[transformDotArrayKeys(k)] = v;
    });

    return formattedErrors;
}

function transformDotArrayKeys(key) {
    var keys = key.split(".");

    if (keys.length == 1) {
        return key;
    }

    var newKey = keys[0];

    $.each(keys, function (k, v) {
        if (k == 0) {
            return;
        }
        newKey += "[" + v + "]";
    });
    return newKey;
}