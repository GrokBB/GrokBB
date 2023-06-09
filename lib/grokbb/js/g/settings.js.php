/* G > Settings */

if (window.location.hash != '') {
    var activeTab = $(window.location.hash);
    
    if (activeTab) {
        $(window.location.hash).addClass('uk-active');
        $(window.location.hash).attr('id', '#');
    }
}

$('[id*="settings-plan-"]').click(function() {
    $('[id*="settings-plan-"]').removeClass('uk-panel-box-primary');
    $(this).addClass('uk-panel-box-primary');
    
    $('#' + this.id + '-radio').prop('checked', true);
});

$('[id*="settings-type-"]').click(function() {
    $('[id*="settings-type-"]').removeClass('uk-panel-box-primary');
    $(this).addClass('uk-panel-box-primary');
    
    $('#' + this.id + '-radio').prop('checked', true);
});

$(function(){
    var progressbar = $('#settings-header-progressbar');
    var bar = progressbar.find('.uk-progress-bar');
    
    var settings = {
        action: '<?php echo SITE_BASE_URL; ?>/api.php',
        params: {
            apihash: '<?php echo $_SESSION['apihash']; ?>',
             object: 'Board',
             method: 'uploadHeader',
             params: '<?php echo $_SESSION['gbbboard']; ?>,'
        },
        
        filelimit: 1,
        allow: '*.(png)',
        
        loadstart: function() {
            bar.css('width', '0%').text('0%');
            progressbar.css('display', 'inline-block');
        },
        
        progress: function(percent) {
            percent = Math.ceil(percent);
            bar.css('width', percent + '%').text(percent + '%');
        },

        allcomplete: function(data) {
            var data = $.parseJSON(data);
            
            bar.css('width', '100%').text('100%');
            
            setTimeout(function(){
                progressbar.hide();
                
                if (data.result) {
                    UIkit.notify('Your header image has been updated.', { status: 'info' });
                    
                    setTimeout(function() {    
                        window.location.href = '<?php echo SITE_BASE_URL; ?>/g/' + $('#settings-name').val().replace(/ /g, '_') + '/settings';
                    }, 1000);
                }
            }, 1250);
        }
    };
    
    $('#settings-header-select').click(function() {
        settings.params.params += '#000000,'; // $('#settings-header-back-color').val() + ',';
        settings.params.params += ($('#settings-header-back-repeat').is(':checked')) ? 1 : 0;
        UIkit.uploadSelect($(this), settings);
    });
});

<?php if ($_SESSION['board']->type == 1) { ?>
codeMirrorBR = UIkit.htmleditor($('#board-request-access'), { markdown: true, height: '200px' });
<?php } ?>

$('#settings-update').click(function() {
    $('#settings-msg').hide();
    
    if ($('#settings-name').val() == '') {
        $('#settings-name').focus();
        
        $('#settings-msg').html('You must enter a name.');
        $('#settings-msg').show();
    } else {
        var params = {
                 'id': $('#board-id').val(),
               'plan': ($('input[name="settings-plan"]:checked').length > 0) ? $('input[name="settings-plan"]:checked').val() : -1,
               'type': $('input[name="settings-type"]:checked').val(),
               'name': $('#settings-name').val(),
            'tagline': $('#settings-tagline').val(),
               'tags': [$('#settings-tag1').val(), $('#settings-tag2').val(), $('#settings-tag3').val()],
                'hbc': '#000000', // $('#settings-header-back-color').val(),
                'hbr': ($('#settings-header-back-repeat').is(':checked')) ? 1 : 0,
                'bra': $('#board-request-access').val(),
        };
        
        $.ajax({
            method: 'POST',
               url: '<?php echo SITE_BASE_URL; ?>/api.php',
              data: {
                  apihash: '<?php echo $_SESSION['apihash']; ?>',
                   object: 'Board',
                   method: 'update',
                   params: params
              }
        }).done(function(data) {
            var data = $.parseJSON(data);
            
            if (data.result) {
                UIkit.notify('Your updates have been saved.', { status: 'info' });
                
                setTimeout(function() {
                    window.location.href = '<?php echo SITE_BASE_URL; ?>/g/' + $('#settings-name').val().replace(/ /g, '_') + '/settings';
                }, 1000);
            } else {
                $('#settings-msg').html(data.msg);
                $('#settings-msg').show();
            }
        });
    }
});

/* Stripe

var handler = StripeCheckout.configure({
    key: '<?php echo (CC_LIVE) ? CC_LIVE_PK : CC_TEST_PK; ?>',
    locale: 'auto',
    token: function(token) {
        $.ajax({
            method: 'POST',
               url: '<?php echo SITE_BASE_URL; ?>/api.php',
              data: {
                  apihash: '<?php echo $_SESSION['apihash']; ?>',
                   object: 'Board',
                   method: 'subscribe',
                   params: {
                          id: $('#board-id').val(),
                       token: token,
                        plan: $('input[name="settings-plan"]:checked').val()
                   }
              }
        }).done(function(data) {
            $('#settings-msg').hide();
            
            var data = $.parseJSON(data);
            
            if (data.result) {
                UIkit.notify('You have been subscribed successfully.', { status: 'info' });
                
                setTimeout(function() {
                    window.location.href = '<?php echo SITE_BASE_URL; ?>/g/' + $('#settings-name').val().replace(/ /g, '_') + '/settings';
                }, 1000);
            } else {
                $('#settings-msg').html(data.msg);
                $('#settings-msg').show();
            }
        });
    }
});

$('#settings-plan-stripe').on('click', function(e) {
    <?php if (CC_TURN_ON == 0) { ?>
    UIkit.notify('This feature is currently disabled.', { status: 'info' });
    <?php } else { ?>
    if ($('#settings-plan-monthly-radio').is(':checked')) {
        var stripeDescription = 'Monthly Subscription ($3 per month)';
    } else {
        var stripeDescription = 'Yearly Subscription ($30 per year)';
    }
    
    handler.open({
        name: 'GrokBB',
        description: stripeDescription,
        panelLabel: 'Add Credit Card'
    });
    
    e.preventDefault();
    <?php } ?>
});

$('#settings-cancel').on('click', function(e) {
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'cancel',
               params: {
                      id: $('#board-id').val()
               }
          }
    }).done(function(data) {
        $('#settings-msg').hide();
        
        var data = $.parseJSON(data);
        
        if (data.result) {
            UIkit.notify('You have cancelled your subscription.', { status: 'info' });
            
            setTimeout(function() {
                window.location.href = '<?php echo SITE_BASE_URL; ?>/g/' + $('#settings-name').val().replace(/ /g, '_') + '/settings';
            }, 1000);
        } else {
            $('#settings-msg').html(data.msg);
            $('#settings-msg').show();
        }
    });
});

$('#settings-return').click(function() {
    UIkit.modal('#modal-cancel').hide();
});

*/

$(window).on('popstate', function() {
    handler.close();
});

/* Sidebar Description */

$('#editor-save-sidebar').click(function() {
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'setSidebar',
               params: {
                   name: '<?php echo $_SESSION['gbbboard']; ?>',
                   desc: $('#editor-text-sidebar').val(),
                   mods: (($('#desc_sidebar_mods').is(':checked')) ? 1 : 0),
                   whos: (($('#desc_sidebar_whos').is(':checked')) ? 1 : 0)
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            UIkit.notify('Your updates have been saved.', { status: 'info' });
            
            // $('#sidebar-description').html(data.msg);
            
            keepAliveClose();
            
            setTimeout(function() {
                $('#settings-sidebar').attr('id', '#');
                window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/settings/#settings-sidebar';
                if (activeTab) { window.location.reload(true); }
            }, 1000);
        }
    });
});

var codeMirrorSB = UIkit.htmleditor($('#editor-text-sidebar'), { markdown: true });
$('#editor-char-sidebar').text(codeMirrorSB.editor.doc.getValue().length);

codeMirrorSB.editor.doc.on('change', function(arr) {
    var charLength = codeMirrorSB.editor.doc.getValue().length;
    $('#editor-char-sidebar').text(charLength);
    
    if (charLength > 15000) {
        $('#editor-char-sidebar').addClass('uk-text-danger');
    } else {
        $('#editor-char-sidebar').removeClass('uk-text-danger');
    }
    
    if (keepAlive == false) {
        keepAliveStart();
    }
});

/* Stylesheet */

// $('#settings-header-back-color').spectrum(spectrumOptions);
$('#settings-header-menu-color').spectrum(spectrumOptions);
$('#settings-header-name-color').spectrum(spectrumOptions);
$('#settings-button-color').spectrum(spectrumOptions);
$('#settings-button-hover').spectrum(spectrumOptions);
$('#settings-button-text-color').spectrum(spectrumOptions);
$('#settings-button-text-hover').spectrum(spectrumOptions);
$('#settings-tag-color').spectrum(spectrumOptions);

$('#editor-save-stylesheet').click(function() {
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'setStylesheet',
               params: {
                   name: '<?php echo $_SESSION['gbbboard']; ?>',
                    css: $('#editor-text-stylesheet').val(),
                    hmc: $('#settings-header-menu-color').val(),
                    hnc: $('#settings-header-name-color').val(),
                    hnf: $('#settings-header-name-font').val(),
                     bc: $('#settings-button-color').val(),
                     bh: $('#settings-button-hover').val(),
                     bf: $('#settings-button-font').val(),
                    btc: $('#settings-button-text-color').val(),
                    bth: $('#settings-button-text-hover').val(),
                     tc: $('#settings-tag-color').val()
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            UIkit.notify('Your updates have been saved.', { status: 'info' });
            
            keepAliveClose();
            
            setTimeout(function() {
                $('#settings-stylesheet').attr('id', '#');
                window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/settings/#settings-stylesheet';
                if (activeTab) { window.location.reload(true); }
            }, 1000);
        }
    });
});

var codeMirrorSS = UIkit.htmleditor($('#editor-text-stylesheet'), { mode: 'tab' });
$('#editor-char-stylesheet').text(codeMirrorSS.editor.doc.getValue().length);

codeMirrorSS.editor.doc.on('change', function(arr) {
    var charLength = codeMirrorSS.editor.doc.getValue().length;
    $('#editor-char-stylesheet').text(charLength);
    
    if (charLength > 15000) {
        $('#editor-char-stylesheet').addClass('uk-text-danger');
    } else {
        $('#editor-char-stylesheet').removeClass('uk-text-danger');
    }
    
    if (keepAlive == false) {
        keepAliveStart();
    }
});

/* Upload Images */

$('[id*="settings-upload"]').click(function() {
    $('#settings-images').attr('id', '#');
    window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/settings/#settings-images';
    if (activeTab) { window.location.reload(true); }
});

$(function(){
    var progressbar = $('#image-progressbar');
    var bar = progressbar.find('.uk-progress-bar');
    
    var settings = {
        action: '<?php echo SITE_BASE_URL; ?>/api.php',
        params: {
            apihash: '<?php echo $_SESSION['apihash']; ?>',
             object: 'Board',
             method: 'uploadImage',
             params: '<?php echo $_SESSION['gbbboard']; ?>'
        },
        
        filelimit: 1,
        allow: '*.(png|jpg|jpeg|gif)',
        
        loadstart: function() {
            bar.css('width', '0%').text('0%');
            progressbar.css('display', 'inline-block');
            
            $('#upload-msg').hide();
        },
        
        progress: function(percent) {
            percent = Math.ceil(percent);
            bar.css('width', percent + '%').text(percent + '%');
        },

        allcomplete: function(data) {
            var data = $.parseJSON(data);
            
            bar.css('width', '100%').text('100%');
            
            setTimeout(function(){
                progressbar.hide();
                
                if (data.result) {
                    $('#settings-images').attr('id', '#');
                    window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/settings/#settings-images';
                    if (activeTab) { window.location.reload(true); }
                } else {
                    $('#upload-msg').html(data.msg);
                    $('#upload-msg').show();
                }
            }, 1250);
        }
    };

    UIkit.uploadSelect($('#image-select'), settings);
});

function deleteImage(file) {
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'deleteImage',
               params: {
                   name: '<?php echo $_SESSION['gbbboard']; ?>',
                   file: file
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            UIkit.notify('Your image has been deleted.', { status: 'info' });
            
            setTimeout(function() {
                $('#settings-images').attr('id', '#');
                window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/settings/#settings-images';
                if (activeTab) { window.location.reload(true); }
            }, 1000);
        }
    });
}

function copyToClipboard(elementId) {
    // create a "hidden" input
    var aux = document.createElement('input');
    
    // assign it the image URL
    aux.setAttribute('value', $('#' + elementId).text());
    
    // append it to the body
    document.body.appendChild(aux);
    
    // select the content
    aux.select();
    
    // copy the selected text
    document.execCommand('copy');
    
    // remove it
    document.body.removeChild(aux);
    
    UIkit.notify('The image URL has been copied to your clipboard.', { status: 'info' });
}

/* Topic Categories */

$('#category-color').spectrum(spectrumOptions);

$('#category-create').click(function() {
    $('#category-msg').hide();
    
    if ($('#category-name').val() == '') {
        $('#category-name').focus();
        
        $('#category-msg').html('You must enter a name.');
        $('#category-msg').show();
    } else {
        $.ajax({
            method: 'POST',
               url: '<?php echo SITE_BASE_URL; ?>/api.php',
              data: {
                  apihash: '<?php echo $_SESSION['apihash']; ?>',
                   object: 'Board',
                   method: 'createCategory',
                   params: {
                          name: '<?php echo $_SESSION['gbbboard']; ?>',
                       nameCat: $('#category-name').val(),
                         color: $('#category-color').val(),
                       private: (($('#category-private').is(':checked')) ? 1 : 0)
                   }
              }
        }).done(function(data) {
            var data = $.parseJSON(data);
            
            if (data.result) {
                UIkit.notify('Your category has been added.', { status: 'info' });
                
                setTimeout(function() {    
                    $('#settings-categories').attr('id', '#');
                    window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/settings/#settings-categories';
                    if (activeTab) { window.location.reload(true); }
                }, 1000);
            } else {
                $('#category-name').focus();
                
                $('#category-msg').html(data.msg);
                $('#category-msg').show();
            }
        });
    }
});

$(function(){
    var progressbar = $('#category-progressbar');
    var bar = progressbar.find('.uk-progress-bar');
    
    var settings = {
        action: '<?php echo SITE_BASE_URL; ?>/api.php',
        params: {
            apihash: '<?php echo $_SESSION['apihash']; ?>',
             object: 'Board',
             method: 'uploadCategory',
             params: '<?php echo $_SESSION['gbbboard']; ?>,'
        },
        
        filelimit: 1,
        allow: '*.(png)',
        
        loadstart: function() {
            bar.css('width', '0%').text('0%');
            progressbar.css('display', 'inline-block');
            
            $('#category-msg').hide();
        },
        
        progress: function(percent) {
            percent = Math.ceil(percent);
            bar.css('width', percent + '%').text(percent + '%');
        },

        allcomplete: function(data) {
            var data = $.parseJSON(data);
            
            bar.css('width', '100%').text('100%');
            
            setTimeout(function(){
                progressbar.hide();
                
                if (data.result) {
                    UIkit.notify('Your category has been added.', { status: 'info' });
                    
                    setTimeout(function() {    
                        $('#settings-categories').attr('id', '#');
                        window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/settings/#settings-categories';
                        if (activeTab) { window.location.reload(true); }
                    }, 1000);
                } else {
                    $('#category-name').focus();
                    
                    $('#category-msg').html(data.msg);
                    $('#category-msg').show();
                }
            }, 1250);
        }
    };
    
    $('#category-select').click(function() {
        $('#category-msg').hide();
        
        if ($('#category-name').val() == '') {
            $('#category-name').focus();
            
            $('#category-msg').html('You must enter a name before you can upload an image.');
            $('#category-msg').show();
            
            return false;
        }
        
        settings.params.params += $('#category-name').val() + ',';
        settings.params.params += $('#category-color').val();
        UIkit.uploadSelect($(this), settings);
    });
});

var categoryToUpdate = 0;

function updateCategory(id, name, color, private) {
    categoryToUpdate = id;
    
    $('#category-update-name').val(name);
    $('#category-update-color').val(color);
    
    if (private) {
        $('#category-update-private').prop('checked', true);
    } else {
        $('#category-update-private').prop('checked', false);
    }
    
    setTimeout(function() {
        $('#category-update-name').focus();
    }, 0);
    
    $('#category-update-color').spectrum(spectrumOptions);
    
    $('#category-update-msg').hide();
    
    UIkit.modal('#modal-category-update').show();
}

$('#category-update-cancel').click(function() {
    categoryToUpdate = 0;
    
    $('#category-update-name').val('');
    $('#category-update-color').val('');
    $('#category-update-private').prop('checked', false);
    
    UIkit.modal('#modal-category-update').hide();
});

$('#category-update-submit').click(function() {
    $('#category-update-msg').hide();
    
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'updateCategory',
               params: {
                      name: '<?php echo $_SESSION['gbbboard']; ?>',
                        id: categoryToUpdate,
                   nameCat: $('#category-update-name').val(),
                     color: $('#category-update-color').val(),
                   private: (($('#category-update-private').is(':checked')) ? 1 : 0)
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            categoryToUpdate = 0;
        
            $('#category-update-name').val('');
            $('#category-update-color').val('');
            UIkit.modal('#modal-category-update').hide();
            
            UIkit.notify('Your category has been updated.', { status: 'info' });
            
            setTimeout(function() {
                $('#settings-categories').attr('id', '#');
                window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/settings/#settings-categories';
                if (activeTab) { window.location.reload(true); }
            }, 1000);
        } else {
            $('#category-update-name').focus();
            
            $('#category-update-msg').html(data.msg);
            $('#category-update-msg').show();
        }
    });
});

var categoryToDelete = 0;

function deleteCategory(id, name) {
    categoryToDelete = id;
    
    $('#category-delete-name').text(name);
    UIkit.modal('#modal-category-delete').show();
}

$('#category-delete-cancel').click(function() {
    categoryToDelete = 0;
    
    $('#category-delete-name').text('');
    UIkit.modal('#modal-category-delete').hide();
});

$('#category-delete-confirm').click(function() {
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'deleteCategory',
               params: {
                   name: '<?php echo $_SESSION['gbbboard']; ?>',
                     id: categoryToDelete
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        categoryToDelete = 0;
        
        $('#category-delete-name').text('');
        UIkit.modal('#modal-category-delete').hide();
        
        if (data.result) {
            UIkit.notify('Your category has been deleted.', { status: 'info' });
            
            setTimeout(function() {
                $('#settings-categories').attr('id', '#');
                window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/settings/#settings-categories';
                if (activeTab) { window.location.reload(true); }
            }, 1000);
        }
    });
});

function updateCategoryDefault(id) {
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'updateCategoryDefault',
               params: {
                   name: '<?php echo $_SESSION['gbbboard']; ?>',
                     id: id
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            UIkit.notify('The default category has been changed.', { status: 'info' });
            
            setTimeout(function() {
                $('#settings-categories').attr('id', '#');
                window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/settings/#settings-categories';
                if (activeTab) { window.location.reload(true); }
            }, 1000);
        }
    });
}

function removeCategoryImage(id) {
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'removeCategoryImage',
               params: {
                   name: '<?php echo $_SESSION['gbbboard']; ?>',
                     id: id
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            UIkit.notify('The category\'s image has been removed.', { status: 'info' });
            
            setTimeout(function() {
                $('#settings-categories').attr('id', '#');
                window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/settings/#settings-categories';
                if (activeTab) { window.location.reload(true); }
            }, 1000);
        }
    });
}

$(function(){
    var progressbar = $('#category-progressbar');
    var bar = progressbar.find('.uk-progress-bar');
    
    var settings = {
        action: '<?php echo SITE_BASE_URL; ?>/api.php',
        params: {
            apihash: '<?php echo $_SESSION['apihash']; ?>',
             object: 'Board',
             method: 'updateCategoryImage',
             params: '<?php echo $_SESSION['gbbboard']; ?>,'
        },
        
        filelimit: 1,
        allow: '*.(png)',
        
        loadstart: function() {
            bar.css('width', '0%').text('0%');
            progressbar.css('display', 'inline-block');
            
            $('#category-msg').hide();
        },
        
        progress: function(percent) {
            percent = Math.ceil(percent);
            bar.css('width', percent + '%').text(percent + '%');
        },

        allcomplete: function(data) {
            var data = $.parseJSON(data);
            
            bar.css('width', '100%').text('100%');
            
            setTimeout(function(){
                progressbar.hide();
                
                if (data.result) {
                    UIkit.notify('Your category image has been updated.', { status: 'info' });
                    
                    setTimeout(function() {    
                        $('#settings-categories').attr('id', '#');
                        window.location.href = '<?php echo SITE_BASE_URL; ?>/g/<?php echo $_SESSION['gbbboard']; ?>/settings/#settings-categories';
                        if (activeTab) { window.location.reload(true); }
                    }, 1000);
                } else {
                    $('#category-msg').html(data.msg);
                    $('#category-msg').show();
                }
            }, 1250);
        }
    };
    
    $('[id*="category-upload-select-"]').click(function() {
        var cid = this.id.substr(this.id.lastIndexOf('-') + 1);
        
        $('#category-msg').hide();
        
        settings.params.params += cid;
        UIkit.uploadSelect($(this), settings);
    });
});

/* Topic Settings */

function addTag(tag) {
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'addTag',
               params: {
                   name: '<?php echo $_SESSION['gbbboard']; ?>',
                    tag: tag
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
    
        if (data.result) {
            $('.tagHandler ul.tagHandlerContainer li.tagItem').attr('data-uk-tooltip', '{ pos: "bottom-left" }');
            $('.tagHandler ul.tagHandlerContainer li.tagItem').attr('title', 'Click to Delete Tag');
            
            return true;
        }
    });
}

function delTag(tag) {
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'delTag',
               params: {
                   name: '<?php echo $_SESSION['gbbboard']; ?>',
                    tag: tag
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
    
        if (data.result) {
            return true;
        }
    });
}

var tagHandlerSettings = {
    assignedTags: [<?php echo (isset($_SESSION['board']->tags)) ? "'" . implode("','", $_SESSION['board']->tags) . "'" : ""; ?>],
           onAdd: function(tag) { return addTag(tag); },
        onDelete: function(tag) { return delTag(tag); }
}

$('#board-tags').tagHandler(tagHandlerSettings);

$('#settings-update-topics').click(function() {
    $.ajax({
        method: 'POST',
           url: '<?php echo SITE_BASE_URL; ?>/api.php',
          data: {
              apihash: '<?php echo $_SESSION['apihash']; ?>',
               object: 'Board',
               method: 'updateSettings',
               params: {
                   name: '<?php echo $_SESSION['gbbboard']; ?>',
                    tcn: $('#topic-content-name').val(),
                    tcd: $('#topic-content-desc').val(),
                    tra: $('#topic-request-access').val(),
                    tap: (($('#topic-content-allowpolls').is(':checked')) ? 1 : 0)
               }
          }
    }).done(function(data) {
        var data = $.parseJSON(data);
        
        if (data.result) {
            UIkit.notify('Your topic settings have been updated.', { status: 'info' });
        }
    });
});

codeMirrorTC = UIkit.htmleditor($('#topic-content-desc'), { markdown: true, height: '200px' });

<?php if ($_SESSION['board']->type == 2) { ?>
codeMirrorTR = UIkit.htmleditor($('#topic-request-access'), { markdown: true, height: '200px' });
<?php } ?>

/* Tab Handling */

$('[data-uk-tab]').on('change.uk.tab', function(event, tab){
    switch(tab.attr('id')) {
        case 'settings-general' :
            setTimeout(function() {
                $('#settings-name').focus();
                <?php if ($_SESSION['board']->type == 1) { ?>
                codeMirrorBR.editor.refresh();
                <?php } ?>
            }, 0);
            break;
        case 'settings-sidebar' :
            setTimeout(function() {
                $(codeMirrorSB.editor.display.input.getField()).focus();
                codeMirrorSB.editor.refresh();
            }, 0);
            break;
        case 'settings-stylesheet' :
            setTimeout(function() {
                $(codeMirrorSS.editor.display.input.getField()).focus();
                codeMirrorSS.editor.refresh();
            }, 0);
            break;
        case 'settings-categories' :
            setTimeout(function() {
                $('#category-name').focus();
            }, 0);
            break;
        case 'settings-topics' :
            setTimeout(function() {
                $('#board-tags').click();
                codeMirrorTC.editor.refresh();
                <?php if ($_SESSION['board']->type == 2) { ?>
                codeMirrorTR.editor.refresh();
                <?php } ?>
            }, 0);
            break;
    }
});