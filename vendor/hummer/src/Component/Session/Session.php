<?php
/*************************************************************************************

   +-----------------------------------------------------------------------------+
   | Hummer [ Make Code Beauty And Web Easy ]                                    |
   +-----------------------------------------------------------------------------+
   | Copyright (c) 2014 https://github.com/damonfei123 All rights reserved.      |
   +-----------------------------------------------------------------------------+
   | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )                     |
   +-----------------------------------------------------------------------------+
   | Author: Damon <zhangyinfei313com@163.com>                                   |
   +-----------------------------------------------------------------------------+

**************************************************************************************/
namespace Hummer\Component\Session;

use Hummer\Component\Helper\Arr;
use Hummer\Component\Helper\Helper;

class Session{

    public function __construct()
    {
        session_start();
    }

    public function set($sVarName, $mV)
    {
        $_SESSION[$sVarName] = Helper::TOOP(is_array($mV), json_encode($mV), $mV);
    }
    public function get($sVarName)
    {
        return Arr::get($_SESSION, $sVarName, null);
    }
    public function del($sVarName)
    {
        if (isset($_SESSION[$sVarName])) {
            unset($_SESSION[$sVarName]);
        }
    }
    public function __destruct()
    {
        session_destroy();
    }
}
