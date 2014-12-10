
// UI.selectmenu rebuild the select such that it can be layouted
jQuery.widget("custom.iconpickerSelectmenu", jQuery.ui.selectmenu, {
    _renderItem: function(ul, item) {
        var li = jQuery( "<li>");
        if(item.value) {
            li.css("background", 'url(' + DOKU_BASE + 'lib/plugins/custombuttons/ico/' + item.value + ') 2px center no-repeat')
                .css('padding-left', '20px');
        }


        if ( item.disabled ) {
            li.addClass( "ui-state-disabled" );
        }

        this._setText(li, item.label);

        return li.appendTo( ul );
    }
});

jQuery(function(){
    jQuery('.custombutton_iconpicker').iconpickerSelectmenu({
        change: function(event, ui){
            if(ui.item.value) {
                jQuery('.ui-selectmenu-text')
                    .css("background", 'url(' + DOKU_BASE + 'lib/plugins/custombuttons/ico/' + ui.item.value + ') 2px center no-repeat')
                    .css('padding-left', '20px');
            }
        }
    });
});
