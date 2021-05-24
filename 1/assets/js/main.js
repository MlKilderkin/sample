(function ($) {
    $(function  () {

        $(".filters").sortable();


        $('input[name=edit-rule]').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var ruleData = $(this).closest('form').data('rule'),
                $editForm = $('#add-edit-rule');
            $editForm.find('input[name=rule-id]').val($(this).closest('form').find('input[name=rule-id]').val());
            $editForm.find('input[name=rule-title]').val(ruleData.title);
            $editForm.find('input[name=rule-regex]').val(ruleData.regex);
            $editForm.find('input[name=rule-flag]').val(ruleData.flag);
            $('html, body').animate({
                scrollTop: $editForm.offset().top
            }, 500);

        });
    });
})(jQuery);