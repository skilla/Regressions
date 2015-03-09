<?php
/**
* Created by PhpStorm.
* User: sergio zambrano <sergio.zambrano@gmail.com>
* Date: 08/03/15
* Time: 16:00
*/

namespace skilla\regegressions\test;

use skilla\matrix\MatrixBase;
use skilla\regressions\Regressions;

include_once "vendor/skilla/matrix/lib/MatrixBase.php";
include_once "lib/Regressions.php";

class MatrixBaseTest extends \PHPUnit_Framework_TestCase
{
    private $regression;

    public function testConstruct()
    {
        $this->regression = new Regressions(new MatrixBase(2, 10), new MatrixBase(1, 10));
        $this->regression->generateDraw();
    }
}
