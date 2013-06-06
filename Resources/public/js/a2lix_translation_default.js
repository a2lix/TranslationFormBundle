$(function() {
    $('ul.a2lix_translationsLocales').on('click', 'a', function(evt) {
        evt.preventDefault();
        $('li:has(a[href='+ this.href +']), div'+ this.href, 'div.a2lix_translations').addClass('active')
            .siblings().removeClass('active');
    });
});
