
jQuery(document).ready(function($){

    $("input[name=admin_upload]").live('click', function(event) {
        var send_attachment_bkp = wp.media.editor.send.attachment;
        wp.media.editor.send.attachment = function(props, attachment) {
            $("input[name='settings[admin_logo_image]']").val(attachment.sizes.thumbnail.url);

            wp.media.editor.send.attachment = send_attachment_bkp;

        }
        wp.media.editor.open();
        event.preventDefault();
        return false;
    });

    //Login screen
    $("input[name=bg_upload]").live('click', function(event) {
        $(this).siblings()
        var send_attachment_bkp = wp.media.editor.send.attachment;
        wp.media.editor.send.attachment = function(props, attachment) {
            $("input[name='login_screen[background]']").val(attachment.sizes.full.url);
            wp.media.editor.send.attachment = send_attachment_bkp;

        }
        wp.media.editor.open();
        event.preventDefault();
        return false;
    });
    $("input[name=ls_upload]").live('click', function(event) {
        $(this).siblings()
        var send_attachment_bkp = wp.media.editor.send.attachment;
        wp.media.editor.send.attachment = function(props, attachment) {
            $("input[name='login_screen[image]']").val(attachment.sizes.thumbnail.url);

            wp.media.editor.send.attachment = send_attachment_bkp;

        }
        wp.media.editor.open();
        event.preventDefault();
        return false;
    });

});
