<?php
namespace Hummer\Component\Route;

class Route{
    protected $Context;

    function __construct($Context=null) {
        $this->Context = $Context;
    }

    /**
     *  RUN FOR HTTP
     *  @param $REQ     HttpRequest
     *  @param $RES     HttpResponse
     *  @param $aRule   array
     **/
    public function generateFromHttp($REQ, $RES, $aRule=array())
    {
        $aCallBack = array();
        if (!$aRule || !is_array($aRule)) {
            throw new \InvalidArgumentException('[Route] : ERROR ROUTE PARAM');
        }
        foreach ($aRule as $aV) {
            if (!is_array($aV) || count($aV) < 4) {
                throw new \DomainException('[Route] : ERROR CONFIG');
            }
            $mV              = array_shift($aV);
            $sControllerPath = array_shift($aV);
            $sControllerPre  = array_shift($aV);
            $sActionPre      = array_shift($aV);
            if (!is_null($this->Context)) {
                $this->Context->registerMulti(array(
                    'sControllerPath' => $sControllerPath,
                    'sControllerPre'  => $sControllerPre,
                    'sActionPre'      => $sActionPre
                ));
            }
            $aCallBack[] = call_user_func_array(
                $mV,
                array($REQ, $RES, $sControllerPath, $sControllerPre, $sActionPre)
            );
        }
        return $aCallBack;
    }
}
