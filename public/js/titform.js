window.onload = function() {

    // Maak verzend knop disabled tot form open is.
    $('.disabled-button').prop('disabled', true); //makes it disabled

    // Open 2e form blok
    $( ".first-item .form-input-group:last-child" ).change(function() {
        $('.disabled-button').prop('disabled', false); //makes it enabled
        $( ".calculate-item" ).slideDown(150, "swing");
    });


    // Extra veldje toevoegen als dit nodig is.
    $('.otherwise').hide();
    $('input.open-new-input').on('click', function(){
        var nextField = $(this).parent().parent().parent().next('.otherwise');
        if($(this).hasClass('open-new-input')){
            $(nextField).fadeIn( "fast" );
        }else{
            $(otherField).fadeOut( "fast" );
        }
    });

    $( "select" ).change(function() {
        var selected = $(this).children(":selected");
        var otherfield = $(selected).parent().parent().next('.form-input-group').find('.otherwise');
        if (selected.hasClass('open-new-input')){
            $(otherfield).fadeIn( "fast" );
        }else{
            $(otherfield).fadeOut( "fast" );
        }
    });
};