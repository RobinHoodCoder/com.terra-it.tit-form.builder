window.onload = function() {

    // Maak verzend knop disabled tot form open is.


    //Zet alle velden op disabled behalve de eerste
    $('.first-item input.field-item, .calculate-item input.field-item, .first-item select.field-item, .calculate-item select.field-item').not(':first').not('.checkbox').each(function(index, value){
        $(value).prop('disabled', true);
        $(value).addClass('disabled');
    });
    //Maak eerst volgende veld editable na change
    $( ".field-item" ).not('.checkbox').change(function() {

        var nextField = $(this).parents('form').find('.disabled:first');

        if ($(nextField).is(':disabled')){
            $(nextField).prop('disabled', false);
            $(nextField).removeClass('disabled');
        }

    });
    //Maak eerst volgende veld editable na focus
    $( ".checkbox" ).focusin(function() {

        var nextField = $(this).parents('form').find('.disabled:first');

        if ($(nextField).is(':disabled')){
            $(nextField).prop('disabled', false);
            $(nextField).removeClass('disabled');
        }

    });

    // als je ergens klikt op het document, voer dan deze check uit:




    // $( ".number" ).hover(function() {
    //     var nextField = $(this).parents('form').find('.disabled:first');
    //
    //     if ($(nextField).is(':disabled')){
    //         $(nextField).prop('disabled', false);
    //         $(nextField).removeClass('disabled');
    //     }
    // });

    // Maak verzend knop disabled tot form open is.
    $('.disabled-button').prop('disabled', true); //makes it disabled

    $('.form-item').each(function(i, e){
        $(this).children('.form-input-group').last().addClass('last-form-element');
    });

    $('.checkbox').change(function(){
        $(this).parent().toggleClass('checked-label');
    });


    var formItems = $('.form-item').size;

    console.log(formItems);


    if (formItems > 2){
        $('.form-item').parent().css({
            'display': 'flex',
            'flex-wrap': 'wrap'
        });
    }

    // Open 2e form blok
    $( ".form-item .last-form-element" ).click(function() {

        $(this).removeClass('last-form-element');
        $(this).parents('.form-item').removeClass('calculate-item');

        var nextFormItem = $('.calculate-item').first();

        $( nextFormItem ).slideDown(150, "swing");
    });


    //Maak button niet meer disabled bij aanpassen laatste inputveld
    var lastInput = $('.calculate-item input').last();
    $(lastInput).change(function(){
        $('.disabled-button').prop('disabled', false); //makes it enabled
        $('form button').removeClass('disabled-button');
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