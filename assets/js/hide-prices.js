(function($) {
    $(document).ready(function() {
        var replacePrice         = '.wpgks_hp_replace_price_form_field',
            replacementType      = '.wpgks_hp_replacement_type_form_field',
            replacementTypeField = '.wpgks_hp_replacement_type_field',
            replacementTextField = '.wpgks_hp_replacement_text_field',
            replacementUrlField  = '.wpgks_hp_replacement_url_field';

        $(document).on('change', replacePrice, function() {
            var parentSel = $(this).closest('div');

            if (this.checked) {
                parentSel.find(replacementTypeField).show();
                parentSel.find(replacementTextField).show();

                if ( 'button' === parentSel.find(replacementType).val() ) {
                    parentSel.find(replacementUrlField).show();
                }
            } else {
                parentSel.find(replacementTypeField).hide();
                parentSel.find(replacementTextField).hide();
                parentSel.find(replacementUrlField).hide();
            }
        });

        $(document).on('change', replacementType, function() {
            var parentSel = $(this).closest('div');

            if ( 'button' === parentSel.find(replacementType).val() ) {
                parentSel.find(replacementUrlField).show();
            } else {
                parentSel.find(replacementUrlField).hide();
            }
        });
    });
})(jQuery);
