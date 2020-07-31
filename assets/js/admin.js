/**
 * IIFE jQuery scripts.
 */
(function ($) {

    /**
     * Load the codes on document ready.
     */
    $(function () {
        trendingMagProAddNewOption();
        trendingMagProDeleteOption();
    });

    /**
     * Adds new input field to the option list.
     */
    function trendingMagProAddNewOption() {
        var nameAttr = 'undefined' !== typeof trending_mag_pro.field_name ? trending_mag_pro.field_name.option_input_field : '';
        var html = `
        <div class="option-input-field-wrap">
            <input type="text" class="option-input-field" value="" name="${nameAttr}" >
            <button class="button delete-option-input-field">X</button>
        </div>
        `;
        $(document).on('click', '#add-new-option', function (e) {
            e.preventDefault();
            $('#options-list').append(html);
        });
    }

    /**
     * Deletes the current input field.
     */
    function trendingMagProDeleteOption() {
        $(document).on('click', '.delete-option-input-field', function (e) {
            e.preventDefault();
            $(this).parent('.option-input-field-wrap').remove();
        });
    }


})(jQuery);