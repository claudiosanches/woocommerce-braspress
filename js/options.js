jQuery(document).ready(function($) {
    var display_date = $('#woocommerce_braspress_display_date');
    braspress_adddays = $('.form-table:eq(0) tr:eq(8)');
    braspress_adddays.hide();

    function addtionalDaysDisplay() {
        if ( display_date.is(':checked') ) {
            braspress_adddays.show();
        } else {
            braspress_adddays.hide();
        }
    }
    addtionalDaysDisplay();

    display_date.on('click', function() {
        addtionalDaysDisplay();
    });
});
