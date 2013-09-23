$(function() {
    $('ul.a2lix_translationsLocales').on('click', 'a', function(evt) {
        evt.preventDefault();
        $(this).tab('show');
    });

    $('div.a2lix_translationsLocalesSelector').on('change', 'input', function(evt) {
        var $tabs = $('ul.a2lix_translationsLocales');

        $('div.a2lix_translationsLocalesSelector').find('input').each(function() {
            $tabs.find('li:has(a[data-target=".a2lix_translationsFields-' + this.value + '"])').toggle(this.checked);
        });

        $('ul.a2lix_translationsLocales li:visible:first').find('a').tab('show');
    }).trigger('change');
});
