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

    // Manage focus on right bootstrap tab when invalid event (A2lixTranslation tab or not, and inner tabs include)
    $(':input', 'div.tab-content').on('invalid', function(e) {
        var $tabPanes = $(this).parents('div.tab-pane');

        $tabPanes.each(function() {
            var $tabPane = $(this);

            if (!$tabPane.hasClass('active')) {
                var $tabNavs = $tabPane.parent('.tab-content')
                                       .siblings('ul.nav.nav-tabs');

                // Tab target by id
                if (this.id) {
                    $tabNavs.find('a[href="#'+ this.id +'"], a[data-target="#'+ this.id +'"]')
                            .trigger('click');

                    return true;
                }

                // Tab target by class for a2lixTranslation
                var a2lixTranslClass = /a2lix_translationsFields-[\S]+/.exec(this.className);
                if (a2lixTranslClass.length) {
                    $tabNavs.find('a[data-target=".'+ a2lixTranslClass[0] +'"]')
                            .trigger('click');

                    return true;
                }
            }
        });

        return true;
    });
});
