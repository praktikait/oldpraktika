<?php
ob_start();
require("/home/praktika/etc/gb_config.inc.php");

$userid = (int)$_POST["u"];
$row = array();

if($_POST["close"] == "1") {

    if($_POST["mA"] == "1") {
        $row[] = "praktika_ausland = 'true'";
        $sql = "SELECT email, name, anrede FROM prakt2.nutzer WHERE id = ".$userid;
        $nutzer = $hDB->query($sql, $praktdbmaster);
        $nutzer = mysql_fetch_assoc($nutzer);

        //Zeitbezug einstellen
        $aktstunde = date('H');
        if ($aktstunde < 10) {
                $begruessung = 'Guten Morgen';
        } elseif ($aktstunde > 19) {
                $begruessung = 'Guten Abend';
        } else {
                $begruessung = 'Guten Tag';
        }

        # $begruessung .= ' '.$nutzer['anrede'].' '.$nutzer['name'];

        /* infomail zusammenbauen */
        // Templatesystem vorbereiten
        $emailobj = new emailtemplate(90);

        // Ersetzungen durchf�hren
        $emailobj->replace('{id}',$insert_id);
        $emailobj->replace('{ansprache}',$begruessung);
        $emailobj->replace('{anrede}',$nutzer['anrede']);
        $emailobj->replace('{name}',$nutzer['name']);

        // infomail verschicken
        $emailobj->send_email($nutzer["email"]);

        // Emailobjekt l�schen
        unset($emailobj);
    }
    if($_POST["mB"] == "1") {
        $row[] = "praktika_sprachreise = 'true'";
    }

    $sql = "INSERT INTO ".$database_partner.".nutzer_reg_afilli SET ".implode(",",$row).", nutzerid = ".$userid;
    $hDB->query($sql, $praktdbmaster);
    
    Praktika_User::loginById($userid, LOGIN_CANDIDATES);
    exit();
}

?>
<div style="width:672px; height:415px; text-align:center; margin:20px;" class="smallbox_register_2">
    <p style="font-size:16px; font-weight:bold;">Vielen Dank f&uuml;r Ihre Registrierung auf praktika.de</p><br />
    Sie haben soeben eine E-Mail mit dem Betreff "<b>Willkommen bei PRAKTIKA!</b>" erhalten.<br />Sollten Sie keine E-Mail in Ihrem Posteingang finden, schauen Sie bitte auch in den Spam-Ordner. <br />
    <br />
<span style="color:red;">
    Folgen Sie bitte den Anweisungen in der E-Mail zur Aktivierung Ihres PRAKTIKA-Accounts. Nach der Aktivierung k�nnen Sie sich mit Ihren Zugangsdaten einloggen. <br />
</span>
<br />
<script type="text/javascript">
    function fertig() {
        modesA = $('affilli_praktika_ausland').checked==true?1:0;
        modesB = $('affilli_praktika_sprachreise').checked==true?1:0;
        
        xhr('/smallbox/login/step3','close=1&u=<?=$userid ?>&mA=' + modesA + '&mB=' + modesB);

        location.reload();
    }
</script>
Viel Spa�!<br />
Ihr PRAKTIKA Team<br />
    <br />
    <h4>Unser kostenfreies Dankesch&ouml;n f&uuml;r Ihre Anmeldung</h4>
        <p class="checkboxes" style=" width:40%; float:right;">
            <input type="checkbox" value="true" id="affilli_praktika_sprachreise" name="affilli[praktika_sprachreise]" <?=((isset($_POST['affilli']['praktika_sprachreise']) && ($_POST['affilli']['praktika_sprachreise'] == "true")) ? ' checked="checked"' : '') ?> />
            <label style="width:90% !important;" for="affilli_praktika_sprachreise">Ja, Bitte senden Sie mir den aktuellen Sprachreisekatalog <?=(date('Y') + 1)?> kostenlos zu.<br /><br /><br /><img src="/styles/images/home/sprachreisen_mini.gif" alt="" /></label>
        </p>
        <p class="checkboxes clearfix" style="width:40%;">
            <input type="checkbox" value="true" id="affilli_praktika_ausland" name="affilli[praktika_ausland]" <?=((isset($_POST['affilli']['praktika_ausland']) && ($_POST['affilli']['praktika_ausland'] == 'true')) ? ' checked="checked"' : '') ?> />
            <label  style="width:90% !important;"for="affilli_praktika_ausland">Ja, ich interessiere mich f&uuml;r ein Praktikum im Ausland, bitte senden Sie mir dazu kostenlose Informationen zu.<br /><br /><img src="/styles/images/home/praktika_mini.jpg" alt="" /></label>
        </p>
        <div style="text-align:center;"><button onclick="fertig(); smallbox.hide(false);"><span><span><span>Fertigstellen</span></span></span></button></div>
</div>
