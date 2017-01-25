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

$g->append($config['googleListId'], $config['googleSheetRange'], [1,2,3]);