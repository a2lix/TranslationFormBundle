$('ul.translations_tabs_locale').on('click', 'a', function(evt) {
    evt.preventDefault();
    
    $('div.translations_tabs li[data-lang='+ $(this).parent().data('lang') +']')
        .addClass('active').siblings().removeClass('active');
});