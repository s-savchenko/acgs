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

$type = 'unsubscribe';
//$type = 'subscribe';
$contactId = 2;

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

    if ($type == ActiveCampaign::REQUEST_TYPE_SUBSCRIBE) {
        $g->append($config['googleListId'], $config['googleSheetRange'], $contact);
    } elseif ($type == ActiveCampaign::REQUEST_TYPE_UNSUBSCRIBE) {
        $contacts = $g->getRows($config['googleListId'], $config['googleSheetRange']);
        for ($i = 0; $i < count($contacts); $i++) {
            if (is_array($contacts[$i]) && $contacts[$i][0] == $contactId) {
                $i++;
                $x = $g->writeRow(
                    $config['googleListId'],
                    $config['googleSheetRange'] . '!A' . $i,
                    $contact);
                break;
            }
        }
    } elseif ($type == ActiveCampaign::REQUEST_TYPE_UPDATE) {

    }

} catch (Exception $e) {
    var_dump($e);
}
sleep(5);
//$g->append($config['googleListId'], $config['googleSheetRange'], [1,2,3]);