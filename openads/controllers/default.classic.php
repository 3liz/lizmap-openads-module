<?php
/**
* @package   lizmap
* @subpackage openads
* @author    your name
* @copyright 2011-2021 3liz
* @link      http://3liz.com
* @license    All rights reserved
*/

class defaultCtrl extends jController
{
    /**
    *
    */
    public function index()
    {
        $rep = $this->getResponse('html');

        return $rep;
    }
}
