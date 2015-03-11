$('a.build-link').on('click', function(e) {
    e.preventDefault();

    $(this).addClass('build-pending');

    $.ajax({
        url: "/build/" + $(this).data('template-name'),
        context: $(this)
    }).done(function(data) {
        $(this).removeClass('build-pending');
        $(this).addClass('build-successful');
        $(this).prev().prev('span.timestamp').text('Last build: ' + data.lastBuild);
    });
})