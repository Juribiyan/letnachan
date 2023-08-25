<?php
require_once "Dklab/Realplexor.php";
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 19.10.2016
 * Time: 2:04
 */
$rpl = new Dklab_Realplexor("127.0.0.1", "10010", "main");
$rpl->send(array("updater"), "huy");
