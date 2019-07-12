<?php

namespace Papi\Controller;

class TestController extends CommonController
{
    public function test()
    {
        $arr = I('get.');

        $jmcode = $this->endata($arr);

        echo $jmcode;
    }
}