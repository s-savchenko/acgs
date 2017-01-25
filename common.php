<?php
/**
 * Created by PhpStorm.
 * User: savchenko
 * Date: 25.01.17
 * Time: 18:24
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once 'GoogleSheets.php';
require_once 'ActiveCampaign.php';

$config = require_once('config.php');

session_start();