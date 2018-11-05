window.onload = function() {

    /*------------------------
    Algemene formulierfuncties!!
    --------------------*/
    $( "select" ).change(function() {
        var selected = $(this).children(":selected");
        var otherfield = $(selected).parent().parent().next('.form-input-group').find('.otherwise');
        if (selected.hasClass('open-new-input')){
            $(otherfield).fadeIn( "fast" );
        }else{
            $(otherfield).fadeOut( "fast" );
        }
    });
    // Extra veldje toevoegen bij "anders, namelijk:"
    $('.otherwise').hide();
    $('input.open-new-input').on('click', function(){
        var nextField = $(this).parent().parent().parent().next('.otherwise');
        if($(this).hasClass('open-new-input')){
            $(nextField).fadeIn( "fast" );
        }else{
            $(otherField).fadeOut( "fast" );
        }
    });






    /*------------------------
   Prijsbereken formulierfuncties!!
   --------------------*/
    $('.multipart-form').each(function(i, currentForm){

        var formSections = $(currentForm).find('.form-item');

        //1. Dit zijn de input velden die die disabled moeten zijn..
        inputs = $('input, select, textarea, button');

        //2. Disable alles..


        function makeDisabled(element, boolean){
            $(element).attr('disabled', boolean);
            if(boolean === true) element.addClass('disabled');
            else{element.removeClass('disabled');}
        }

        //var toDisable = $(formSections).find($(inputs));
        // makeDisabled(toDisable,true);

        $(this).find('.form-item').each(function(i, e){
            var containDisabledFields = $(e).find('.availability').not('.enable_all');
            makeDisabled($(containDisabledFields).find(inputs),true);
        });

        makeDisabled($(currentForm).find('button'),true);

       // makeDisabled($(this).find('button'),true);


        //var disabledFields =  $(formSections).find(inputs.addClass('disabled').attr('disabled',true));
        // console.log(disabledFields);


        //3. Vind het eerste inputveld van elke form item en maak deze invulbaar...
        $(this).find('.form-item').each(function(itemcount, formItem){
            var firstInputGroup = $(formItem).find('.form-input-group').first();

            firstInputGroup.each(function (itemcount, firstInputGroup) {
                makeDisabled($(firstInputGroup).find('.field-item'),false);
               // $(firstInputGroup).find('.field-item').removeClass('disabled').attr('disabled',false);
            });
            // $(firstInputGroup).find('.field-item').removeClass('disabled').attr('disabled',false);
        });

        //Zet een class op het laatste form item (Formulier Sectie!!) en zet de class "last-form-group erop!"
        $(this).find('.form-item').each(function(i, e){
            $(this).find('.availability').children('.form-input-group').last().addClass('last-form-group');
        });

        /*
        * Maak veld na checkboxes actief na het aanklikken van 1 item.
        * Na uitklikken maak je het weer inactief
        * */
        // -----------

        $(this).find('.contain-cbox').each(function (i,fieldItem) {
            $(fieldItem).find('.field-item').change(function(){
                $(this).toggleClass('checked');
            });
        });





        $('.field-item').change(function(){

            //Dit is allleeen voor chexbokses
            if ($(this).is('input[type=checkbox]')){
                var cboxContainer = $(this).parents('.contain-cbox');
                $(cboxContainer).find('.field-item').each(function (index, element) {


                    if($(element).parents('.form-input-group.last-form-group').length !== 0){

                    }else{
                        var nextInputGroup = $(element).parents('.form-input-group').next();
                        var nexInputs = nextInputGroup.find('.field-item');
                        //console.log(element);

                        //Als er een checked checkbox in de betreffende container zit, zoek dan de volgende
                        //-form input group en maak die bewerkbaar
                        var checkedbox = $(cboxContainer).find('.checked');


                        if(checkedbox.length === 0){
                            makeDisabled($(nexInputs),true);
                            //$(nexInputs).attr('disabled', true).addClass('disabled');
                        }else{
                            makeDisabled($(nexInputs),false);
                        }
                    }

                });
            }


        });



            /*
       * Als er iets is veranderd in NIET-checkbox veld, maak dan het volgende item actief.
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

        $(this).find(".form-item").find('.last-form-group').change(function() {

           // console.log('loog');

            var lastItem = $(this).find('.field-item');

            if($(lastItem).hasClass('disabled')) {
                //DOE NIETS als laatste element nog niet aanklikbaar is!!
            }else{


                // MAak de volgende sectie van het formulier zichtbaar!!
                $(this).removeClass('last-form-group');
                $(this).parents('.form-item').removeClass('active-step');
                $(this).parents('.form-item').addClass('done');

                var nextFormStep =  $(this).parents('.done').next('.form-item');


                // MAak het volgende stap in formulier zichtbaar
                $(nextFormStep).slideDown(150, "swing").addClass('active-step');
                var activeStep = $('.active-step');


                //$(activeStep).find('.field-item').removeClass('disabled').attr('disabled',false);
            }

        });
        var enableAlwaysItems =  $(currentForm).find('.enable-always');
        var lastEnableAlwaysItem = $(enableAlwaysItems).last();
        $(lastEnableAlwaysItem).change(function(i,changedItem){
            if($(changedItem).parents('.active-step')){
                makeDisabled($(currentForm).find('button'),false);
            }
        });
        makeDisabled($(enableAlwaysItems.find('.field-item')),false);





        //Maak verzendbutton niet meer disabled bij aanpassen ALLERlaatste inputveld
        var lastFormItem = $(this).find('.form-item').last();
        var lastFieldItems = $(lastFormItem).find('.form-input-group').find('.field-item');

        var lastDisabledFieldItem = $(lastFormItem).find('.form-input-group').find('.field-item.disabled').last();

        console.log('Last item reached');

        lastFieldItems = $(lastFieldItems);


        console.log($(lastFieldItems));


        // if(lastFieldItem.type === 'textarea'){
        //     console.log
        // };


        $(lastFieldItems).change(function(){
            if(!$(this).prop('required')){
                makeDisabled($('.disabled-button'),false);
            }else{
                console.log('Last item reached');

                makeDisabled($('.disabled-button'),false);

                $('.disabled-button').prop('disabled', false); //makes it enabled
                $('form button').removeClass('disabled-button');
            }


        });


        // Als laatste disabled field item veranderd, maak dan de verzendbutton actief.
        $(lastDisabledFieldItem).change(function(){
            console.log('Last item reached');
            makeDisabled($(currentForm).find('.disabled-button'),false);
            //$(this).find('.disabled-button').prop('disabled', false); //makes it enabled
            $('form button').removeClass('disabled-button');
        });
    });
    // $(formInputs.items).not(':first').not('.checkbox').each(function(index, value){
    //     $(value).prop('disabled', true);
    //     $(value).addClass('disabled');
    // });
    // $( ".number" ).hover(function() {
    //     var nextField = $(this).parents('form').find('.disabled:first');
    //
    //     if ($(nextField).is(':disabled')){
    //         $(nextField).prop('disabled', false);
    //         $(nextField).removeClass('disabled');
    //     }
    // });

    // Maak verzend knop disabled tot form open is.
};