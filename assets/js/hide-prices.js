(function($) {
    $(document).ready(function() {
        var replacePrice         = $('#wpgks_hp_replace_price'),
            replacementType      = $('#wpgks_hp_replacement_type'),
            replacementTypeField = $('.wpgks_hp_replacement_type_field'),
            replacementTextField = $('.wpgks_hp_replacement_text_field'),
            replacementUrlField  = $('.wpgks_hp_replacement_url_field');

        replacePrice.on('change', function() {
            if (this.checked) {
                replacementTypeField.show();
                replacementTextField.show();

                if ( 'button' === replacementType.val() ) {
                    replacementUrlField.show();
                }
            } else {
                replacementTypeField.hide();
                replacementTextField.hide();
                replacementUrlField.hide();
            }
        });

        replacementType.on('change', function() {
            if ( 'button' === replacementType.val() ) {
                replacementUrlField.show();
            } else {
                replacementUrlField.hide();
            }
        });
    });
})(jQuery);
