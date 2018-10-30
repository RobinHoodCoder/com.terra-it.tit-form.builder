<?
/**
 * Terra-IT Form Builder
 *
 * Bouw formulieren in BigTree CMS. That's it ¯\_(ツ)_/¯
 * @author Jordi vd Broek <jordi@terra-it.com>
 * @version 1.1.2
 */

//Changelog
//12-10-2018
//Ook textarea krijgt een class met disabled
//Button om te bewerken toegevoegd
//01-10-2018
//1. Conversiecode optie toegevoegd
//2. Debugging optie toegevoegd:
//     wann. ingelogd alleen ingevulde formulieren naar ingelogde gebruiker sturen
//     Conversiecode staat uit wanneer ingelogd in het CMS
//
//3. LESS CSS geupdate (succesmelding is nu inbegrepen)
//4. Required form text laten zien boven verzendbutton



// 1. Namen van formuliersecties zichtbaar
// 2. Formulier introductie tekst zichtbaar.
// 3. Fuck google captcha, het is eruit.
// 4. Random string honeypot en minimale form invultijd honeypot added
// 5. Anders namelijk... bug gefixt.
// 6. Cleaned up
// 7. Server side required velden check fixed
// 8. Elke keer dat een form word gegenereerd krijgt deze een unieke ID, dit maakt meerdere forms op een pagina mogelijk, ook minder kans op misbruik.
// 9. IP Adres van gebruiker word meegestuurd.

// Robin Edit JWZ
// 1. Sterretjes bij verplicht
// 2. Om elk veld een form input group div gezet voor makkelijke css aanpasbaarheid
// 3. Let op, verwijder de list styling uit elements.less en gebruik form.less voor input velden styling


// Insert javascript in de head
//require('required/insert_js.php');
class mailTemplater{
    public function getMailTemplate($type){
        global $bigtree;
        $default = 'default.php';
        $defaultTemplate = file_get_contents('mail-template/' . $default, true);
        $custom = 'custom_mailtemplate.php';
        $customTemplate = file_get_contents('mail-template/' . $custom, true);

        if ($type == 'default'){
            $this->activeTemplate = $defaultTemplate;
        }
        if ($type == 'custom'){
            $this->activeTemplate = $customTemplate;
        }

        return $this;
    }

    public function formatTemplate($variables = false){

        $mailBody = $this->activeTemplate;

        if (isset($variables) && !empty($variables)){
            foreach($variables as $key => $value){
                $mailBody = str_replace($key, $value, $mailBody);
            }
        }

        return $mailBody;
    }
}

// Zorg voor juiste e-mail key veld
function replace_key_function($array, $key1, $key2)
{
    $keys = array_keys($array);
    $index = array_search($key1, $keys);

    if ($index !== false) {
        $keys[$index] = $key2;
        $array = array_combine($keys, $array);
    }
    return $array;
}


class TitForm extends BigTreeModule {
    var $Table = "formbuilder_formulieren";

    public function getForm($id){
        global $bigtree;

        // =======================================================================//
        // Security first. Maakt random bytes en zet deze verderop als input name en in sessie voor vergelijking.
        // Zet tijd verderop ook in input, check deze later tijdens post validatie.
        // =======================================================================//

        $noSpamIdentifier = md5(uniqid());
        if(!empty($_SESSION["spam-iden"])){
            // doe niks sessie is al gezet?
        }else{
            $_SESSION["spam-iden"] = $noSpamIdentifier;
        }

        // Zet tijdslimiet op 6 sec
        $time_limit = 6;

        // =======================================================================//

        //Haal basis formuliervelden op
        $baseFields = new TitFormBaseFields;
        $baseFields = $baseFields->getApproved('position ASC');
        $formItems = $this->getApproved('position ASC');


        //Kies het juiste formulier
        foreach($formItems as $formItem){

            if ($formItem['id'] == $id){
                $formItems = $formItem;
                $form = true;
            }
        }

        if ($formItems['type'] == 'calculate-form'){
            $formType = 'multipart-form';
            $formClass = 'calculate-item';
            $disabled = 'style="display:none;"';
            $buttonDisabled = 'disabled-button';
        }else{
            $formType = 'single-form';
            $formClass = '';
            $disabled = '';
            $buttonDisabled = '';
        }

        if ($form === true){
            // Selecteer alleen het stappen / prijsbereken formulier (stages)
            foreach($formItems as $key => $value){
                if($key == 'form_stages'){
                    $stages[$key] = $value;
                }
            }
            // Verkrijg de juiste velden uit de mess
            foreach($stages as $stage){
                $stageCount = count($stage);
                if(is_array($stage)){
                    foreach($stage as $stageItem){
                        if (isset($stageItem['section'])){
                            $fields[] = $stageItem['section'];
                        }
                    }
                }
            }


            // Veldjes loopen for required voor form opbouw
            if (isset($fields)){
                foreach($fields as $field){
                    foreach($field as $key => $value){
                        // Staan er basis velden in de lijst? haal ze op en gebruik ze.
                        foreach($baseFields as $baseField){
                            if($baseField['id'] == $value){
                                $fieldItems[] = $baseField;
                            }
                        }
                    }

                    // Titel voor een nieuw RTL programma: op zoek naar de error.
                    foreach($fieldItems as $fieldItem) {
                        if($fieldItem['required'] == 'on'){
                            if($fieldItem['type'] == 'email' && preg_match('/mail/',strtolower($fieldItem['title']))){
                                $errorInfos[] = array($fieldItem['type'] => $fieldItem['error']);
                            }else{
                                $errorInfos[] = array($fieldItem['title'] => $fieldItem['error']);
                            }
                        }
                    }

                    if ($errorInfos){
                        foreach($errorInfos as $errorInfo){
                            if (is_array($errorInfo)){
                                foreach($errorInfo as $key => $value){
                                    $rightError[$key] = $value;
                                }
                            }
                        }
                    }
                }
            }

            if($errorInfos){
                $errorInfoFields = array_unique($rightError, SORT_REGULAR);
            }
        }else{
            die;
        }

        // Start als er iets in de post zit
        if ($_POST['form_send'] == 'true') {

            // Clean de post een beetje
            $post = array();
            foreach($_POST as $key => $value){
                if (is_array($value)){
                    $value = implode(", ", $value);
                }
                $post[$key] = Bigtree::safeEncode(strip_tags(trim($value)));
            }

            // Vergelijk sessie code met post, klopt dit niet, verzenden we niks.
            $antiSpamCode = $_SESSION["spam-iden"];
            if(isset($post['fietsbel']) && $post['fietsbel'] == $antiSpamCode && time()-$time_limit > (int)$post['form-time']){

                $postKeys = array_keys($post);

                // vergelijk titels, check voor required en paas errors
                foreach($errorInfoFields as $infoKey => $infoValue){
                    foreach($post as $key => $value){
                        if($key == str_replace(' ', '_', $infoKey) && empty($value)){
                            $errors[] = $infoValue;
                        }
                    }
                }

                foreach($fieldItems as $fieldItem){
                    foreach($fieldItem as $fieldKey => $fieldValue){
                        //Originele titels met "_" om e-mails te kunnen versturen
                        $cleanTitles[] = $fieldValue;
                        //Maak mooie titels zonder "_" voor in e-mail die wordt verstuurd
                        $fixedTitles[] = str_replace(' ', '_', $fieldValue);

                        foreach($fixedTitles as $fixedTitle){

                            foreach ($postKeys as $postKey) {
                                if ($postKey == $fixedTitle) {
                                    // Prepareer HTML email values;
                                    foreach ($post as $postKey => $postValue) {

                                        if ($postKey == 'email' && !empty($postValue)){
                                            $reply = true;
                                        }

                                        foreach($cleanTitles as $cleanTitle){
                                            $mailPrepares[$postKey] = $postValue;
                                        }

                                        foreach($mailPrepares as $key => $value){
                                            if($key == $fixedTitle){
                                                foreach($cleanTitles as $cleanTitle){
                                                    if (str_replace(' ', '_', $cleanTitle) == $key){
                                                        //Gecleande formuliervelden
                                                        $mailItems[$cleanTitle] = $value;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }else{
                $errors[] = 'Spambot detected!';
            }

            // Zet juiste array key voor email

            if (isset($mailItems['email']) && !empty($mailItems['email'])){
                $mailItems = replace_key_function($mailItems, 'email', 'E-mailadres');
            }


            if (is_array($errors)){
                $errors = array_unique($errors);
            }


            //Errors gevonden? laat maar zien, zo niet, verzend die mail
            if(count($errors) > 0){
                # display errors
                $errorsHTML = '<ul>';
                foreach ($errors as $error) {
                    $errorsHTML .= '<li>' . $error . '</li>';
                }
                $errorsHTML .= '</ul>';

            }else{
                $userIP = $_SERVER["REMOTE_ADDR"];
                if (isset($_SESSION['bigtree_admin']['email']) && $formItems['debugging'] === 'on'){
                    $to = $_SESSION['bigtree_admin']['email'];
                }
                else{$to = $formItems['email_to'];}



                $subject = $formItems['subject'];
                $from = $formItems['email_from'];
                $return = $post['email'];
                $bcc = 'info@terra-it.com';

                // Zet email items
                foreach($mailItems as $key => $value){
                    $content .= '<tr>
                    <td style="padding: 5px; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #555555;">
                        <strong>'. $key .'</strong>
                    </td>
                    <td style="padding: 5px; font-family: sans-serif; font-size: 15px; mso-height-rule: exactly; line-height: 20px; color: #555555;">'. $value .'</td>
                    </tr>';
                }
                $variables = array(
                    '{{subject}}' => $formItems['subject'],
                    '{{userIP}}' => $userIP,
                    '{{image}}' => BigTree::prefixFile($formItems['image'], "mail_"),
                    '{{message}}' => $content
                );
                $templater = new mailTemplater();
                $html = $templater->getMailTemplate('default')->formatTemplate($variables);
                $mainSend = BigTree::sendEmail($to, $subject, $html, "", $from, $return, false, $bcc, false);
                unset($_SESSION['spam-iden']);

                // Eerste mail verstuurd, nu de volgende als dit van toepassing is.
                $html = '';
                if ($reply === true && $mainSend === true){

                    // Reply email nodig? zorg voor de juiste
                    if($formItems['reply_template'] == 'on'){
                        $templater = new mailTemplater();
                        $replyTemplate = $templater->getMailTemplate('custom');
                        $replyTemplate = (array)$replyTemplate;
                        $replyTemplate = $replyTemplate['activeTemplate'];
                    }elseif($formItems['reply_template'] != 'on' && isset($formItems['reply_template_cms']) && !empty($formItems['reply_template_cms'])){
                        $replyTemplate = $formItems['reply_template_cms'];
                    }
                    $to = $post['email'];
                    $subject = $formItems['return_title'];
                    $from = $formItems['email_from'];
                    $html = $replyTemplate;
                    BigTree::sendEmail($to, $subject, $html, "", $from, false, false, false);
                }

                // Zet unieke form identifier in sessie voor later use (redirect etc)
                foreach($post as $postKey => $postValue){
                    if (!empty($postKey) && $postKey == 'identifier'){
                        $_SESSION["identifier"] = $postValue;
                    }
                }

                // Redirect naar de juiste identifier.
                $link = trim($page['link'], '/');
                BigTree::redirect($link.'?formid='.$post["identifier"]);
            }

            // Geen post data? maak unieke identifier aan.
        }elseif(empty($_POST)){
            $identifier = uniqid('form-',false);
        }else{
            // doe niks
        }


        /*Check of unique identifier in url aanwezig is, en of deze overeen komt met de identifier in de sessie.
            Wanneer dat zo is, dan is success true en mag succesmelding getoond worden.
        */
        if (isset($_GET['formid']) && !empty($_SESSION['identifier'])){
            if ($_GET['formid'] === $_SESSION['identifier']){
                $success = true;
            }
        }
        if ($success === true) {

            if (!isset($_SESSION['bigtree_admin']['email'])){?>
<!--            Google conversiecode als deze is ingevuld en je niet bent ingelogd-->
                <?=$formItems['conversion_code']?>
            <?}?>
            <div id="attention" class="form-success-overlay"><?=$formItems['success_message']?></div>

            <?
            $_SESSION['identifier'] = '';
        }
        if (isset($errorsHTML)) {
            echo '<div class="form-error-overlay">'.$formItems['error_introduction'].'';
            echo '<ul>';

            if (is_array($errorsHTML)){
                foreach ($errorsHTML as $error) {
                    echo '<li>'. $error .'</li>';
                }
            }else{
                echo $errorsHTML;
            }
            echo '</div></ul>';
        }
        ?>



		<form class="<?= $formType?>" action="" method="post" class="contact-form" enctype="multipart/form-data">
			<div class="grid">
				<div>
					<input type="hidden" name="form_send" value="true"/>
					<input style="display:none;" type="text" name="fietsbel" value="<?=$_SESSION['spam-iden']?>" placeholder="" />
					<input style="display:none;" name="form-time" type="text" value="<?=time(); ?>" />
					<input type="hidden" name="identifier" value="<?=$identifier?>">

					<div class="col-12">
						<div class="form-intro">
                            <?
                            //Introductietekst formulier
                            if(isset($formItems[0]['comment'])){
                                echo $formItems[0]['comment'];
                            }elseif(isset($formItems['comment'])){
                                echo $formItems['comment'];
                            }
                            ?>
						</div>
					</div>

                    <?

                    $amount = count($fields);

                    // Uit hoeveel onderdelen bestaat dit formulier? lets find out!
                    if ($amount === 1){
                        echo '<div class="col-12 col-medium-12 col-small-12 col-mobile-12 form-item">';
                    }
                    if($amount > 1){
                        echo '<div class="col-6 col-small-12 col-mobile-12 form-item first-item">';
                    }

                    $i=0;
                    foreach($stages as $stage){






                        
                        if(is_array($stage)){

                            //var_dump($stage);
                            foreach($stage as $stageItem){
                                $i++;

                                // Show stage titel
                                if(isset($stageItem['title']) && !empty($stageItem['title']) && (!$stageItem['hide_title'])){
                                    echo '<div class="form-input-group-title"><h3>'.$stageItem['title'].'</h3></div>';
                                }
                                if($stageItem['hide_title'] === 'on'){?>
									<div class="form-spacer"></div>
                                <?}

                                // Tel op en loop

                                if (isset($stageItem['section'])) {
                                    foreach ($stageItem['section'] as $section) {
                                        if (isset($fieldItems)) {
                                            $check = array();
                                            foreach ($fieldItems as $fieldItem) {
                                                if (isset($fieldItem['id']) && $fieldItem['id'] == $section) {
                                                    // Zorg voor alleen unieke velden
                                                    if (!in_array($fieldItem[0], $check)) {
                                                        $rtitle = str_replace(' ', '_', $fieldItem['title']);

                                                        if($fieldItem['required'] == 'on'){
                                                            $required = 'required';
                                                            $star = ' *';
                                                        }else{
                                                            $star = '';
                                                            $required = '';
                                                        }

                                                        // Textitems
                                                        switch ($fieldItem['type']) {
                                                            //Tekstveld standaard
                                                            case 'text':
                                                                echo('<div class="form-input-group"><input class="field-item data-hj-whitelist '.$fieldItem['type'].'" name="' . $fieldItem['title'] . '" value="' . $post[$rtitle] . '" type="text" placeholder="' . $fieldItem['title'] .$star.'" ' . $required . '></div>');
                                                                break;
                                                            case 'email':
                                                                echo('<div class="form-input-group"><input class="field-item data-hj-whitelist" name="' . $fieldItem['type'] . '" value="' . $post[$rtitle] . '" type="email" placeholder="' . $fieldItem['title'].$star.'" ' . $required . '></div>');
                                                                break;
                                                            case 'tel':
                                                                echo('<div class="form-input-group"><input class="field-item data-hj-whitelist '.$fieldItem['type'].'" name="' . $fieldItem['title'] . '" value="' . $post[$rtitle] . '" type="text" placeholder="' . $fieldItem['title'].$star.'" ' . $required . '></div>');
                                                                break;
                                                            case 'number':
                                                                //Pak het woord dat tussen blokhaakjes staat bijvoorbeeld: "[meter]", zet deze in een variable en haal het uit de titel van het veld
                                                                $numberTitle = $fieldItem['title'];
                                                                preg_match_all("/\[[^\]]*\]/", $numberTitle, $matches);
                                                                $matches = implode(" ", $matches[0]);
                                                                $units = trim($matches,"[]");

                                                                $numberTitle = str_replace($matches, '', $numberTitle);
                                                                $numberTitle = trim($numberTitle);

                                                                echo '<div class="form-input-group"><div class="numberGroup">';
                                                                echo '<label for="'.$fieldItem['title'].'">'.$numberTitle.'</label>';
                                                                echo('<input id="'.$fieldItem['title'].'" class="field-item data-hj-whitelist '.$fieldItem['type'].' " name="' . $numberTitle . '" value="' . $post[$rtitle] . '" type="number" min="0" max="999" maxlength="4" placeholder="..." ' . $required . '>');
                                                                echo '</div></div>';
                                                                break;
                                                            case 'textarea':
                                                                echo('<div class="form-input-group">
                                                                        <textarea class="field-item data-hj-whitelist '.$fieldItem['type'].'" name="' . $fieldItem['title'] . '" placeholder="' . $fieldItem['title'].$star.'" ' . $required . '>' . $post[$rtitle] . '</textarea>
                                                                    </div>');
                                                                break;
                                                            case 'radio':
                                                                echo '<div class="form-input-group">';
                                                                echo (!empty($fieldItem['title'])) ? '<p>' . $fieldItem['title'].$star.'</p>' : '';
                                                                echo ('<radiogroup>');
                                                                foreach ($fieldItem['sub_fields'] as $radioField) {
                                                                    $rtitle = str_replace(' ', '_', $radioField['title']);

                                                                    if ($radioField['option-type'] == 'textual'){
                                                                        $textualClass = true;
                                                                        $openInput = 'open-new-input';
                                                                        $textualTitle = $radioField['title'];
                                                                    }else{
                                                                        $textualClass = false;
                                                                        $openInput = '';
                                                                        $textualTitle = '';
                                                                    }
                                                                    // Input value als form errors aanwezig zijn
                                                                    if (isset($post) && in_array($rtitle, $post)) {
                                                                        echo('<div class="contain-radio"><input class="field-item data-hj-whitelist '.$fieldItem['type'].'  '. $openInput .'" type="radio" name="' . $fieldItem['title'] . '" id="' . $radioField['title'] . '" value="' . $radioField['title'] . '" ' . $required . ' checked/><label for="' . $radioField['title'] . '">' . $radioField['title'] . '</label></div>');
                                                                    } else {
                                                                        echo('<div class="contain-radio"><input class="field-item data-hj-whitelist '.$fieldItem['type'].'  '. $openInput .'" type="radio" name="' . $fieldItem['title'] . '" id="' . $radioField['title'] . '" value="' . $radioField['title'] . '" ' . $required . '/><label for="' . $radioField['title'] . '">' . $radioField['title'] . '</label></div>');
                                                                    }
                                                                }
                                                                echo ('</radiogroup></div>');
                                                                if($textualClass){
                                                                    echo('<div class="form-input-group"><input class="field-item otherwise data-hj-whitelist" name="' . $textualTitle . '" value="' . $post[$rtitle] . '" type="text" placeholder="' . $textualTitle . '"></div>');
                                                                }
                                                                break;
                                                            case 'checkbox':
                                                                echo (!empty($fieldItem['title'])) ? '
                                                                <div class="form-input-group">
                                                                
                                                                    <p>' . $fieldItem['title'].$star.'</p>' : '';

                                                                    if (count($fieldItem['sub_fields']) > 1) {
                                                                        $chbxTitle = $fieldItem['title'];
                                                                    } else {
                                                                        $chbxTitle = $fieldItem['title'];
                                                                    }
                                                                    echo ('
                                                                    
                                                                        <div class="contain-cbox">');
                                                                        foreach ($fieldItem['sub_fields'] as $checkboxField) {
                                                                            $rtitle = str_replace(' ', '_', $checkboxField['title']);

                                                                            // Input value als form errors aanwezig zijn
                                                                            if (isset($post) && in_array($rtitle, $post)) {
                                                                                echo('
                                                                                <div>
                                                                                   <label class="cbox-label" for="'.$rtitle.'">
                                                                                    <input class="field-item checkbox data-hj-whitelist '.$fieldItem['type'].'" id="'.$rtitle.'" type="checkbox" name="' . $chbxTitle . '[]" value="' . $checkboxField['title'] . '" ' . $required . ' checked>
                                                                                        <span>' . $checkboxField['title'] . '</span>'.'
                                                                                        <div class="cleared"></div>
                                                                                    </label>
                                                                                </div>');
                                                                            } else {
                                                                                echo('
                                                                                <div>
                                                                                    <label class="cbox-label" for="'.$rtitle.'">
                                                                                        <input class="field-item data-hj-whitelist '.$fieldItem['type'].' label__checkbox" id="'.$rtitle.'" type="checkbox" name="' . $chbxTitle . '[]" value="' . $checkboxField['title'] . '" ' . $required . '>
                                                                                        <span>' . $checkboxField['title'] . '</span>
                                                                                        <div class="cleared"></div>                                                                               
                                                                                    </label>
                                                                                </div>');
                                                                            }
                                                                        }
                                                                        echo ('
                                                                        </div>
                                                                   
                                                                </div>
                                                                ');
                                                                break;
                                                            case 'select':
                                                                echo (!empty($fieldItem['title'])) ? '<div class="form-input-group"><label for="'.$fieldItem['title'].'">' . $fieldItem['title'].$star.'</label>' : '';
                                                                echo('<select id="'.$fieldItem['title'].'" class="field-item '.$fieldItem['type'].'" name="' . $fieldItem['title'] . '" ' . $required . '>');
                                                                echo '<option value="">Selecteer een optie</option> ';
                                                                foreach ($fieldItem['sub_fields'] as $selectField) {
                                                                    $rtitle = str_replace(' ', '_', $selectField['title']);

                                                                    if ($selectField['option-type'] == 'textual'){
                                                                        $textualSelect = true;
                                                                        $openInput = 'open-new-input';
                                                                        $selectTitle = $selectField['title'];
                                                                    }else{
                                                                        $textualSelect = false;
                                                                        $openInput = '';
                                                                        $selectTitle = '';
                                                                    }

                                                                    // Input value als form errors aanwezig zijn
                                                                    if (isset($post) && in_array($rtitle, $post)) {
                                                                        echo('<option class="data-hj-whitelist '. $openInput .'" value="' . $selectField['title'] . '" selected>' . $selectField['title'] . '</option> ');
                                                                    } else {
                                                                        echo('<option class="data-hj-whitelist '. $openInput .'" value="' . $selectField['title'] . '">' . $selectField['title'] . '</option> ');
                                                                    }
                                                                }
                                                                echo('</select></div>');
                                                                if($textualSelect){
                                                                    echo('<div class="form-input-group"><input class="field-item otherwise data-hj-whitelist" name="' . $selectTitle . '" value="' . $post[$rtitle] . '" type="text" placeholder="' . $selectTitle . '"></div>');

                                                                }
                                                                break;
                                                            case 'date':
                                                                echo('<div class="form-input-group date-group"><label>'.$fieldItem['title'].'<input class="field-item date data-hj-whitelist'.$fieldItem['type'].'" name="' . $fieldItem['title'] . '" value="' . $post[$rtitle] . '" type="text" placeholder="Kies datum: " ' . $required . '></label></div>');
                                                                break;
                                                            case 'upload':
                                                                echo '<p>'.$fieldItem['title'].'</p>';
                                                                echo('<div class="form-input-group"><input class="field-item '.$fieldItem['type'].'" name="' . $fieldItem['title'] . '" value="' . $post[$rtitle] . '" type="file" placeholder="' . $fieldItem['title'].$star.'" ' . $required . '></div>');
                                                                break;
                                                        }
                                                        $check[] = $fieldItem[0];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                if($i < $amount){
                                    echo '</div><div class="col-6 col-small-12 col-mobile-12 form-item '.$formClass.'" '.$disabled.'>';
                                }elseif($i === $amount){

                                }
                                elseif($i <= 1){

                                }

                            }
                        }
                    }
                    ?>
				</div>

				<div class="col-12">
                    <?if(isset($formItems['required_text'])){?>
                        <p class="required-form-text"><?=$formItems['required_text']?></p>
                    <?}?>
					<button type="submit" class="btn-green-send-s <?=$buttonDisabled?> content-button" name="form-submit"><?=$formItems['button_text']?></button>
                    <?
                    if (isset($_SESSION['bigtree_admin']['email'])){?>
                        <a href="<?=ADMIN_ROOT?>com.terra-it.tit-form.builder*formulieren/edit/<?=$formItems['id']?>">Edit this form</a>
                    <?}
                    ?>
                </div>
			</div>
			</div>
		</form>
    <?}
}
?>
