/**
 * Copyright (c) De Webmakers
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
(function() {
    var div = document.createElement("div");
    div.setAttribute("id", "pnldebug");
    div.innerHTML = '<button class="btn">DBG</button><div class="dbgwrapper"></div>';
    document.getElementsByTagName("body")[0].appendChild(div);
    var dbgObj = document.querySelector('#pnldebug');
    var dbgWrp = document.querySelector('#pnldebug .dbgwrapper');
    var dbgBtn = document.querySelector('#pnldebug .btn');
    if(!!dbgObj && !!dbgBtn) {
        dbgBtn.addEventListener('click', function() {
            dbgObj.classList.toggle('active');
        }, false);
    }
    function dbg(msg) {
        var newEl = document.createElement('p');
        newEl.innerHTML = msg;
        if(!!dbgWrp) dbgWrp.appendChild(newEl);
    }
    dbg('PostNL init');
    if(typeof postnl_adrescheck_data == 'undefined') { dbg('No postnl settings found!'); return; }
    if(typeof jQuery != 'function') { dbg('No jquery found!'); return; }

    (function($) {

        if(postnl_adrescheck_data.elements.billing.length == 0) { dbg('This theme is not supported!'); return; }

        var app = {
            settings: postnl_adrescheck_data,
            theme: postnl_adrescheck_data.theme,
            elements: postnl_adrescheck_data.elements,
            hasDebug: (!!dbgObj && !!dbgBtn),
            steps: {},
            onXHR: function(e, xhr, opts) {
                if(opts.url == this.settings.endpoint) return; // not here
                if(opts.url.indexOf('checkout/country&country_id=') > -1) {
                    if(!!this.steps['billing']) this.steps['billing'].chooseZone(opts.url);
                    if(!!this.steps['shipping']) this.steps['shipping'].chooseZone(opts.url);
                }
                switch(opts.url) {
                    case 'index.php?route=checkout/guest':
                    case 'index.php?route=checkout/register':
                    case 'checkout/guest':
                        if(Object.keys(this.elements.billing).length == 0) return;
                        if($(this.elements.billing.country).length == 0) { dbg('Billing elements not found'); return; }
                        if(typeof this.steps['billing'] != 'undefined')  return;
                        this.steps['billing'] = new checkoutStep('billing');
                        this.steps['billing'].manipulateFields();
                        this.steps['billing'].fieldsEvents();
                    break;
                    case 'index.php?route=checkout/guest_shipping':
                    case 'checkout/guest_shipping':
                        if(Object.keys(this.elements.shipping).length == 0) return;
                        if($(this.elements.shipping.country).length == 0) { dbg('Shipping elements not found'); return; }
                        if(typeof this.steps['shipping'] != 'undefined')  return;
                        this.steps['shipping'] = new checkoutStep('shipping');
                        this.steps['shipping'].manipulateFields();
                        this.steps['shipping'].fieldsEvents();
                    break;
                }
                if(opts.url.indexOf('journal3/checkout/payment') > -1) {
                    if(Object.keys(this.elements.billing).length > 0 && $(this.elements.billing.country).length) {
                        if(!$('#postnl_adres_billing').length) {
                            this.steps['billing'] = new checkoutStep('billing');
                            this.steps['billing'].manipulateFields();
                            this.steps['billing'].fieldsEvents();
                        } else {
                            if(!!this.steps['billing']) this.steps['billing'].fillDefaultFields();
                        }
                    } else dbg('Billing elements not found');
                    if(Object.keys(this.elements.shipping).length > 0 && $(this.elements.shipping.country).length) {
                        if(!$('#postnl_adres_shipping').length) {
                            this.steps['shipping'] = new checkoutStep('shipping');
                            this.steps['shipping'].manipulateFields();
                            this.steps['shipping'].fieldsEvents();
                        } else {
                            if(!!this.steps['shipping']) this.steps['shipping'].fillDefaultFields();
                        }
                    } else dbg('Shipping elements not found');
                }
                if(this.hasDebug) console.log('Ajax event: ', opts.url);
            },
            endLoader: undefined,
            requests: {},
            request: function(url,post,cb,opt) {
                if(!!this.requests[url]) { this.requests[url].abort(); clearTimeout(this.endLoader); }
                var serializedData = post, options = Object.assign({}, {loader:true,disable:true}, opt);
                if(post instanceof jQuery) {
                    serializedData = post.serializeArray();
                    if(options.disable) post.find(':input').attr('disabled','disabled').addClass('disabled');
                }
                if(options.loader) this.toggleLoader(true);
                dbg('XHR send ' + JSON.stringify(serializedData));
                this.requests[url] = $.ajax({url:url,method:'POST',data:serializedData}).done(function(response) {
                    clearTimeout(this.endLoader);
                    this.requestComplete(response,post,cb,options);
                }.bind(this));
                this.endLoader = setTimeout(function() {
                    this.requests[url].abort();
                    if(options.loader) this.toggleLoader(false);
                    $('.postnl_adres_results').addClass('active').html('<p><em>'+settings.label_timeout+'</em></p>');
                    $('.postnl_adres_default').show();
                }.bind(this), 30*1000);
            },
            requestComplete: function(data,post,cb,options) {
                dbg('XHR result '+ JSON.stringify(data));
                if(!!data.redirect) { location.href = data.redirect; return; }
                if(options.disable && post instanceof jQuery) post.find(':input').removeAttr('disabled').removeClass('disabled');
                if(options.loader) this.toggleLoader(false);
                if(!!cb ) cb.call(this, data); //&& typeof data.result != 'undefined'
                if(!!data.debug) dbg(data.debug);
            },
            toggleLoader: function(b) {
                if(!!b) $('.postnl_adres_results').addClass('active').html('<p><small>'+this.settings.label_loading+'</small></p>'); // @todo: translate
                else $('.postnl_adres_results').removeClass('active').html('');
            },
        };

        class checkoutStep {

            constructor(type) {
                this.type = type;
                this.elements = app.elements[type];
                this.settings = app.settings;
                var data = localStorage.getItem('postnl_adrescheck_'+this.type);
                // Set values
                this.oldData = (!!data ? JSON.parse(data) : {zipcode:'',number:'',addition:''});
                // Check if copy-element is checked and copy over data
                this.checkCopy();
            }

            manipulateFields() {
                // Move country up (actually others down because of js-events on country select)
                // And wrap current fields in a default wrapper
                var wrp = $('<div>').addClass('postnl_adres_wrap theme_'+this.settings.theme).attr({id:'postnl_adres_'+this.type}).data('type', this.type);
                wrp.append($('<div>').addClass('postnl_adres_inner'));
                wrp.append($('<div>').addClass('postnl_adres_default'));
                //$(this.elements.wrapper).append(wrp);
                wrp.insertAfter($(this.elements.country).parents('.form-group'));
                this.obj = $('#postnl_adres_'+this.type);
                this.objInner = $('.postnl_adres_inner', this.obj);
                this.objDefault = $('.postnl_adres_default', this.obj);
                $(this.elements.address1).parents('.form-group').appendTo(this.objDefault);
                $(this.elements.address2).parents('.form-group').appendTo(this.objDefault);
                $(this.elements.zipcode).parents('.form-group').appendTo(this.objDefault);
                $(this.elements.city).parents('.form-group').appendTo(this.objDefault);

                // Add new fields to our inner wrapper
                var zip =  $(this.elements.zipcode).parents('.form-group').clone().addClass('postnl_adres_zipcode');
                zip.find('input').attr({name:'zipcode',value:this.oldData.zipcode,placeholder:'',id:'input-'+this.type+'-zipcode'}); // new name
                zip.find('label').attr({for:'input-'+this.type+'-zipcode'});
                var nr = $(this.elements.zipcode).parents('.form-group').clone().addClass('postnl_adres_number');
                nr.find('input').attr({name:'number',value:this.oldData.number,placeholder:'',id:'input-'+this.type+'-nr'});
                nr.find('label').attr({for:'input-'+this.type+'-nr'}).text(this.settings.label_number);
                var add = $(this.elements.zipcode).parents('.form-group').clone().addClass('postnl_adres_addition').removeClass('required');
                add.find('input').attr({name:'addition',value:this.oldData.addition,placeholder:'---',id:'input-'+this.type+'-addition'});
                add.find('label').attr({for:'input-'+this.type+'-addition'}).text(this.settings.label_addition);
                var results = $('<div>').addClass('postnl_adres_results_wrapper');
                results.append($('<div>').addClass('postnl_adres_results'));
                this.objInner.append(zip).append(nr).append(add).append(results).append($('<div>').addClass('postnl_adres_reset'));
                this.objResults = $('.postnl_adres_results', this.obj);

                // Show optional label
                if(this.settings.showlabel == 'on') {
                    var lbl = $('<img>').attr({src:this.settings.label_img,class:'label_img'});
                    $('.postnl_adres_reset',this.obj).append(lbl);
                }
                $('.postnl_adres_reset',this.obj).append($('<button>').addClass('btn-reset').text(this.settings.label_reset));
                this.btnReset = $('.btn-reset',this.obj).hide();

                this.objInner.append($('<div>').addClass('postnl_adres_chosen'));
                this.objDefault.hide();
            }

            fieldsEvents() {
                this.btnReset.off('click').on('click', this.onResetClick.bind(this));
                $(this.elements.country).on('change', this.onCountryChange.bind(this)).trigger('change');
                this.oldData = {zipcode:$('#input-'+this.type+'-zipcode').val(), number: $('#input-'+this.type+'-nr').val(), addition: $('#input-'+this.type+'-addition').val()};
                this.saveFieldData();
                $('#input-'+this.type+'-zipcode,#input-'+this.type+'-nr,#input-'+this.type+'-addition').off('change blur keyup').on('change blur keyup', this.onCustomFieldChange.bind(this));
            }

            chooseZone(url) {
                if($(this.elements.zone).parents('.form-group').is(':hidden')) { // Automatically choose the last one to circumvent validation
                    for(var country_id of this.settings.zone_disabled_countries) {
                        if(url.indexOf('checkout/country&country_id='+country_id) > -1) {
                            $(this.elements.zone).val($(this.elements.zone+' option:last').val());
                            break;
                        }
                    }
                }
            }

            onResetClick(e) {
                e.preventDefault();
                $('.postnl_adres_zipcode,.postnl_adres_number,.postnl_adres_addition', this.obj).show().find('input').val('').eq(0).focus();
                $('.postnl_adres_default', this.obj).hide();
                this.btnReset.hide();
            }

            onCountryChange(e) {
                var b = (this.settings.countries.indexOf(parseInt($(e.target).val())) > -1);
                $(this.elements.zone).parents('.form-group').toggle(!b); // hide province, not needed (in NL) if we use our module

                if(b == false) { // Not NL
                    this.objDefault.show();
                    $('.postnl_adres_default input', this.obj).val('');
                    $(this.elements.address2).parents('.form-group').show();
                    this.objInner.hide();
                } else { // NL
                    $(this.elements.address2).parents('.form-group').hide();
                    this.objInner.show();
                    $('.postnl_adres_zipcode,.postnl_adres_number,.postnl_adres_addition', this.obj).show();
                    $('#input-'+this.type+'-zipcode').val(this.oldData.zipcode);
                    $('#input-'+this.type+'-nr').val(this.oldData.number);
                    $('#input-'+this.type+'-addition').val(this.oldData.addition);

                    this.btnReset.hide();
                    //if(this.settings.manual_input == 'on' && Object.values(this.oldData).length) this.objDefault.show();
                    //else
                    this.objDefault.hide();
                }
            }

            onCustomFieldChange(e) {
                var z = $('#input-'+this.type+'-zipcode').val();
                var n = $('#input-'+this.type+'-nr').val();
                var a = $('#input-'+this.type+'-addition').val();
                if(this.oldData.zipcode == z &&  n == this.oldData.number && this.oldData.addition == a) return; // nothing changed, no request necessary
                this.oldData = {zipcode:z,number:n,addition:a};
                this.saveFieldData();
                if(z.length >= 6 && n.length > 0) {
                    $('#postnl_adres_'+this.type).find('.postnl_adres_chosen').html('');
                    app.request(this.settings.endpoint, {zipcode:z, number:n, addition:a}, this.showResults.bind(this));
                }
            }

            onAddressClick(e) {
                dbg('Clicked '+this.type+' result');
                this.fillResult();
                this.objResults.removeClass('active');
            }

            showResults(data) {
                this.objResults.addClass('active');
                this.btnReset.show();
                if(data.success == false) {
                    this.objResults.html('<p><em>'+ this.settings.label_noresults+'</em></p>');
                    return;
                }
                for(var nr in data.result) {
                    var addr = data.result[nr];
                    var r = $('<div>').addClass('postnl_adres_result').html([
                        '<label class="postnl_adres_label" for="postnl_adres_'+nr+'">',
                        '<input type="radio" name="postnl_adres" class="postnl_adres_checkbox" id="postnl_adres_'+nr+'">',
                        '<strong>'+addr.address1+'</strong>', addr.address2, '<small>'+addr.city+'</small>',
                        '</label>'
                    ].join('')).data(addr);
                    this.objResults.append(r);
                }
                if(data.result.length == 0) {
                    this.objResults.html('<p><em>'+this.settings.label_noresults+'</em></p>');
                }else if(data.result.length == 1) {
                    this.objResults.attr({checked:'checked'});
                }
                this.fillResult();

                // Global address check event
                $('.postnl_adres_checkbox', this.objResults).off('click').on('click', this.onAddressClick.bind(this));
            }

            fillResult() {
                var p = $('.postnl_adres_checkbox:checked', this.obj).parents('.postnl_adres_result');
                if(p.length == 0) return; // Nothing checked
                var addr = p.data();
                $('#input-'+this.type+'-nr').val(addr.number); // first our own fields
                $('#input-'+this.type+'-addition').val(addr.addition); // first our own fields

                $('.postnl_adres_zipcode,.postnl_adres_number,.postnl_adres_addition', this.obj).hide(); // Search again feature

                this.oldData = addr; // full result
                this.saveFieldData();
                this.fillDefaultFields();

                if(this.settings.manual_input == 'on') this.objDefault.show();
                else {
                    $('.postnl_adres_chosen', this.obj).html([
                        '<em>'+this.settings.label_chosen+'</em><br>',
                        '<strong>'+addr.address1+'</strong><br>',
                        (addr.address2!='' ? addr.address2+'<br>' : ''),
                        addr.zipcode+' '+addr.city
                    ].join(''));
                }
            }

            saveFieldData() {
                localStorage.setItem('postnl_adrescheck_'+this.type, JSON.stringify(this.oldData));
            }

            fillDefaultFields() {
                this.checkCopy();
                if(this.oldData.address1 != '') $(this.elements.address1).val(this.oldData.address1);
                if(this.oldData.address2 != '') $(this.elements.address2).val(this.oldData.address2);
                if(this.oldData.city != '') $(this.elements.city).val(this.oldData.city);
                if(this.oldData.zipcode != '') $(this.elements.zipcode).val(this.oldData.zipcode);
            }

            checkCopy() {
                if(!!app.elements['billing']) {
                    if($(app.elements['billing']['copy']).is(':checked')) {
                        var bdata = localStorage.getItem('postnl_adrescheck_billing');
                        if(this.type == 'billing' && !!app.steps['shipping']) { // Journal3
                            app.steps['shipping'].oldData = JSON.parse(bdata);
                            app.steps['shipping'].saveFieldData();
                        }
                        else if(this.type == 'shipping') {
                            this.oldData = JSON.parse(bdata);
                        }
                    }
                }
            }
        }

        // Most theme's and checkouts are loaded via xhr, so we need to manipulate after specific steps are loaded.
        $(document).bind("ajaxComplete", app.onXHR.bind(app));

    })(jQuery);
})();
