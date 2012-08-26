$('ul.a2lix_translationsTabsLocales').on('click', 'a[data-translation-locale]', function(evt) {
    evt.preventDefault();

    var selectedLocale = $(this).data('translation-locale');

    // Set tab and content active
    $('li:has(a[data-translation-locale='+ selectedLocale +']), div.a2lix_translationLocale-'+ selectedLocale, 'div.a2lix_translationsTabs')
        .addClass('active').siblings().removeClass('active');
});