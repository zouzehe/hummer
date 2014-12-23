<?php
namespace Hummer\Framework;

use Hummer\Component\Route\Route;
use Hummer\Component\RDS\Factory;
use Hummer\Component\Context\Context;
use Hummer\Component\Http\HttpRequest;
use Hummer\Component\Http\HttpResponse;
use Hummer\Component\Route\RouteErrorException;

class Bootstrap{

    const S_RUN_CLI  = 'cli';
    const S_RUN_HTTP = 'http';

    public function __construct(
        $Configure,
        $sEnv = null
    ) {
        Context::makeInst();
        $this->Context = Context::getInst();
        $aRegisterMap = array(
            'Config'    => $Configure,
            'sEnv'      => $sEnv,
            'Route'     => new Route($this->Context),
            'sRunMode'  => strtolower(PHP_SAPI) === self::S_RUN_CLI ?
                self::S_RUN_CLI :
                self::S_RUN_HTTP
        );

        #HTTP
        if ($aRegisterMap['sRunMode'] == self::S_RUN_HTTP) {
            $aRegisterMap['HttpRequest']  = new HttpRequest();
            $aRegisterMap['HttpResponse'] = new HttpResponse();
        }
        $this->Context->registerMulti($aRegisterMap);
    }

    public static function setHandle(
        $mCBErrorHandle=array('Hummer\\Framework\\Bootstrap', 'handleError'),
        $iErrType = null
    ) {
        set_error_handler(
            $mCBErrorHandle,
            $iErrType === null ? (E_ALL | E_STRICT) : (int)$iErrType
        );
    }

    public static function handleError($iErrNum, $sErrStr, $sErrFile, $iErrLine, $sErrContext)
    {
        $sStr = sprintf('Catch Error.[%d]:%s %s In File [%s],line %d ',
                $iErrNum,
                $sErrStr,
                $sErrFile,
                $iErrLine
        );
        $C = Context::getInst();
        if ($C === null || !$C->isRegister('Log')) {
            trigger_error($sStr, E_USER_WARNING);
        }else{
            switch ($iErrNum)
            {
                case E_NOTICE:
                case E_USER_NOTICE:
                    $C->Log->notice($sStr);
                    break;
                case E_USER_ERROR:
                    throw new \ErrorException($sStr);
                    break;

                default:
                    $C->Log->warn($sStr);
                    break;
            }
        }
    }

    public function run($sRouteKey=null)
    {
        $C = $this->Context;
        try{
            $Log = $C->Log;
            switch ($C->sRunMode)
            {
                case self::S_RUN_HTTP:
                    $aCallBack = $C->Route->generateFromHttp(
                        $C->HttpRequest,
                        $C->HttpResponse,
                        $C->Config->get($sRouteKey === null ? 'route.http' : $sRouteKey)
                    );
                    foreach ($aCallBack as $CallBack) {
                        $CallBack->call();
                    }
                    break;
                case self::S_RUN_CLI:
                    break;
                default:
                    throw new \RuntimeException('[Bootstrap] : ERROR RUN MODE');
            }
        }catch(RouteErrorException $E){
            //$C->HttpResponse->setStatus(404);
            //$C->HttpResponse->send();
            echo $E->getMessage();
        }catch(\Exception $E){
            echo $E->getMessage();
        }
    }
}
