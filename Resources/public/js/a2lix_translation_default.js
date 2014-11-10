$(function() {
    $('ul.a2lix_translationsLocales').on('click', 'a', function(evt) {
        evt.preventDefault();

        var target = $(this).attr('data-target');
        $('li:has(a[data-target="' + target + '"]), div' + target, 'div.a2lix_translations').addClass('active')
            .siblings().removeClass('active');
    });

    $('div.a2lix_translationsLocalesSelector').on('change', 'input', function(evt) {
        var $tabs = $('ul.a2lix_translationsLocales');

        $('div.a2lix_translationsLocalesSelector').find('input').each(function() {
            $tabs.find('li:has(a[data-target=".a2lix_translationsFields-' + this.value + '"])').toggle(this.checked);
        });

        $('ul.a2lix_translationsLocales li:visible:first').find('a').trigger('click');
    }).trigger('change');
});
