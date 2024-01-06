(function ($) {
    "use strict";
    $(document).ready(function () {

        shorDescriptionCharacterCount();

        $(document).on('keyup keydown', '.short_description_field', function (event) {
            shorDescriptionCharacterCount();

        });

        function shorDescriptionCharacterCount() {
            let length = parseInt($('.short_description_field').val().length);
            $('.short_description_character_count').html(length + ' characters count')
        }

    });

})(jQuery);
