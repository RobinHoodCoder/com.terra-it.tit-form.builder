window.onload = function() {

    // Maak verzend knop disabled tot form open is.


    //Zet alle velden op disabled behalve de eerste




    

    $('.multipart-form').each(function(){

        // Zet alles op disabled...

        //1. Dit zijn de input velden die die disabled moet zijn..
        inputs = $(this).find('input,select,textarea,button' );

        //2. Vind het eerste inputveld..
        firstInputGroup = $(this).find('.first-item .form-input-group');


        //3. Disable alles..
        $(this).find(inputs.addClass('disabled').attr('disabled',true));

        //4. Maak het eerste inputveld invulbaar..
        // Alles behalve het eerste veld
        setTimeout(function(){
            firstInputGroup.first().find('input').removeClass('disabled').attr('disabled',false);
        },200);

        /*
        * Maak veld na checkboxes actief na het aanklikken van 1 item.
        * Na uitklikken maak je het weer inactief
        * */

        // Zet een class op de aangeklikte checkbox!
        $(this).find('.checkbox').change(function(){
            $(this).parent().toggleClass('checked-label');
        });

        //Zet een class op het laatste form item (Formulier Sectie!!) en zet de class "last-form-group erop!"
        $(this).find('.form-item').each(function(i, e){
            $(this).children('.form-input-group').last().addClass('last-form-group');
        });

        console.group('Checkboxes checkerr');
        $(this).find(".contain-cbox").change(function() {
            var containerBox =  $(this);
            var foundItem = $(this).find('.checked-label');
            var nextGroup = containerBox.parents().next('.form-input-group');
            if (foundItem.length === 0){
                console.log('Geen gechecked items!!!');
                nextField = nextGroup.find('.field-item');
                nextField.addClass('disabled');
                nextField.attr('disabled', true);
            }
            if (foundItem.length >= 1){
                console.log('Er zijn gecheckede items!!!');
                nextGroup.find('.field-item').removeClass('disabled');
                nextGroup.find('.field-item').attr('disabled', false);
            }
        });
        console.groupEnd('Checkboxes checkerr');
        // function openNextFormItem(){alert('JAaaa er is eer een aangeklikt!!! :D:D:D');}
        // function closeNextFormItem(){alert('Er zijn geeen vinkjes meer aangekliikt :(:(');}

            /*
       * Als er iets is veranderd in aeeen niet-checkbox veld, maak dan het volgende item actief.
       * */

        $(this).find(".field-item" ).not('.checkbox').change(function() {

            //$Nexttfield is alle inputvelden in het volgende field item!!
            var nextField = $(this).parents('.form-input-group').next('.form-input-group').find('.field-item');
            console.log($(nextField));
            if ($(nextField).is(':disabled')){
                $(nextField).prop('disabled', false);
                $(nextField).removeClass('disabled');
            }
        });





        /*
        * TODO: Als laatste element van een item een checkox is, wat dan?
        *  last form group moet specifieker.. last-form group > een input, textfield of whatever...
        * */
        $(this).find(".form-item .last-form-group").click(function() {
            console.log(this);
            var lastItem = $(this).find('.field-item');

            if($(lastItem).hasClass('disabled')) {
                //DOE NIETS!
            }else{
                // MAak de volgende sectie van het formulier zichtbaar!!
                $(this).removeClass('last-form-group');
                $(this).parents('.form-item').removeClass('active-step');
                $(this).parents('.form-item').addClass('done');

                var nextFormStep =  $(this).parents('.done').next('.form-item');




                // MAak het eerste veld van de volgende sectie invulbaar!!


                var nextFormItem = $('.active-step');

                $(nextFormStep).slideDown(150, "swing").addClass('active-step');
            }

        });



        //Maak eerste veld van volgende stap invulbaar...


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

        //Maak button niet meer disabled bij aanpassen ALLERlaatste inputveld
        var lastInput = $(this).find('.calculate-item input').last();
        $(lastInput).change(function(){
            $('.disabled-button').prop('disabled', false); //makes it enabled
            $('form button').removeClass('disabled-button');
        });


    });








    // $(formInputs.items).not(':first').not('.checkbox').each(function(index, value){
    //     $(value).prop('disabled', true);
    //     $(value).addClass('disabled');
    // });
    //Maak eerst volgende veld editable na change

    // var log = $( ".field-item" ).has('checkbox');











   /********
  *     Maak volgende veld actief na veranderen van een radiobutton
  * */








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





    // if (formItems > 2){
    //     $('.form-item').parent().css({
    //         'display': 'flex',
    //         'flex-wrap': 'wrap'
    //     });
    // }

    // Open 2e form blok








};