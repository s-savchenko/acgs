<?php
/**
 * Created by PhpStorm.
 * User: savchenko
 * Date: 25.01.17
 * Time: 15:28
 */

require_once 'common.php';

$g = new GoogleSheets($config['googleAppName'], $config['googleCredentialsFile'], $config['googleClientSecretFile']);

if (!$g->isReady()) {
    $_SESSION['auth_url'] = $g->getAuthUrl();
    header("Location: token.php");
    die();
}

$ac = new ActiveCampaign($config['acApiKey'], $config['acApiUrl']);

$type = $_POST['type'];
$contactId = (int)$_POST['contact']['id'];
file_put_contents('type'.microtime(), $type);
try {
    $contact = $ac->getContactList([$contactId])[0];
    $contact = [
        $contactId,
        $contact['first_name'],
        $contact['last_name'],
        $contact['ip'],
        $contact['ip4'],
        $contact['email'],
        $contact['tag_names'],
        $contact['list_names'],
        $contact['status'] == 1 ? 'subscribed' : 'unsubscribed',
    ];

    if (in_array($type, [ActiveCampaign::REQUEST_TYPE_SUBSCRIBE, ActiveCampaign::REQUEST_TYPE_UPDATE])) {
        $contacts = $g->getRows($config['googleListId'], 'Sheet1');
        $updated = false;
        for ($i = 0; $i < count($contacts); $i++) {
            if (is_array($contacts[$i]) && $contacts[$i][0] == $contactId) {
                $i++;
                $g->writeRow(
                    $config['googleListId'],
                    explode('!', $config['googleSheetRange'])[0] . '!A' . $i,
                    $contact);
                $updated = true;
            }
        }

        if ($updated == false) {
            $g->append($config['googleListId'], $config['googleSheetRange'], $contact);
        }
    }

} catch (Exception $e) {
    var_dump($e);
}