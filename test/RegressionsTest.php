<?php
/**
* Created by PhpStorm.
* User: sergio zambrano <sergio.zambrano@gmail.com>
* Date: 08/03/15
* Time: 16:00
*/

namespace Skilla\Regegressions\Test;

use Skilla\Matrix\MatrixBase;
use Skilla\Regressions\Regressions;

include_once "vendor/skilla/matrix/lib/MatrixBase.php";
include_once "lib/Regressions.php";

class MatrixBaseTest extends \PHPUnit_Framework_TestCase
{
    private $regression;

    public function testConstruct()
    {
        $independent = new MatrixBase(2, 10, 50);

        $independent->setPoint(1, 1, 8);
        $independent->setPoint(1, 2, 7);
        $independent->setPoint(1, 3, 4);
        $independent->setPoint(1, 4, 9);
        $independent->setPoint(1, 5, 3);
        $independent->setPoint(1, 6, 12);
        $independent->setPoint(1, 7, 23);
        $independent->setPoint(1, 8, 4);
        $independent->setPoint(1, 9, 6);
        $independent->setPoint(1, 10, 15);

        $independent->setPoint(2, 1, 4);
        $independent->setPoint(2, 2, 17);
        $independent->setPoint(2, 3, 14);
        $independent->setPoint(2, 4, 6);
        $independent->setPoint(2, 5, 23);
        $independent->setPoint(2, 6, 5);
        $independent->setPoint(2, 7, 9);
        $independent->setPoint(2, 8, 12);
        $independent->setPoint(2, 9, 13);
        $independent->setPoint(2, 10, 4);

        $dependent   = new MatrixBase(1, 10, 50);

        $dependent->setPoint(1, 1, 8);
        $dependent->setPoint(1, 2, 9);
        $dependent->setPoint(1, 3, 23);
        $dependent->setPoint(1, 4, 34);
        $dependent->setPoint(1, 5, 16);
        $dependent->setPoint(1, 6, 15);
        $dependent->setPoint(1, 7, 17);
        $dependent->setPoint(1, 8, 19);
        $dependent->setPoint(1, 9, 29);
        $dependent->setPoint(1, 10, 31);

        $this->regression = new Regressions(
            $independent,
            $dependent,
            array('Ratio inmuebles', 'Ratio fotos'),
            array('Bajas'),
            50
        );
        $this->regression->generateDraw();
        $this->assertFileExists('test.png');

        $result = $this->regression->regresion('lineal');
        $this->assertArrayHasKey('B0', $result);
        $this->assertArrayHasKey('B1', $result);
        $this->assertArrayHasKey('B2', $result);
        $this->assertArrayHasKey('correlacion', $result);
        $this->assertArrayHasKey('r2', $result);

        if (isset($result['B0'])) {
            $this->assertEquals(25.5044, round($result['B0'], 4));
        }
        if (isset($result['B1'])) {
            $this->assertEquals(-0.1320, round($result['B1'], 4));
        }
        if (isset($result['B2'])) {
            $this->assertEquals(-0.3928, round($result['B2'], 4));
        }
        if (isset($result['correlacion'])) {
            $this->assertEquals(0.2385, round($result['correlacion'], 4));
        }
        if (isset($result['r2'])) {
            $this->assertEquals(0.0569, round($result['r2'], 4));
        }
    }
}
