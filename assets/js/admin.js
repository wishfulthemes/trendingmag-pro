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

    function trendingMagProAddNewOption() {
        $(document).on('click', '#add-new-option', function(e) {
            e.preventDefault();
            var html = `
            <div class="option-input-field-wrap">
                <input type="text" class="option-input-field" value="" name="" >
                <button class="button delete-option-input-field">X</button>
            </div>
            `;
            $('#options-list').append(html);
        });
    }

    function trendingMagProDeleteOption() {
        $(document).on('click', '.delete-option-input-field', function(e) {
            e.preventDefault();
            $(this).parent('.option-input-field-wrap').remove();
        });
    }


})(jQuery);