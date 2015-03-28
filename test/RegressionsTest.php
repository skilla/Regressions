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
    private $independent1;
    private $dependent1;
    private $independent2;
    private $dependent2;

    public function setUp()
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

        $this->independent1 = $independent;
        $this->dependent1   = $dependent;

        $independent = new MatrixBase(2, 8, 50);
        $independent->setPoint(1, 1, 38);
        $independent->setPoint(1, 2, 41);
        $independent->setPoint(1, 3, 34);
        $independent->setPoint(1, 4, 35);
        $independent->setPoint(1, 5, 31);
        $independent->setPoint(1, 6, 34);
        $independent->setPoint(1, 7, 29);
        $independent->setPoint(1, 8, 32);
        $independent->setPoint(2, 1, 47.5);
        $independent->setPoint(2, 2, 21.3);
        $independent->setPoint(2, 3, 36.5);
        $independent->setPoint(2, 4, 18.0);
        $independent->setPoint(2, 5, 29.5);
        $independent->setPoint(2, 6, 14.2);
        $independent->setPoint(2, 7, 21.0);
        $independent->setPoint(2, 8, 10.0);

        $dependent = new MatrixBase(1, 8, 50);
        $dependent->setPoint(1, 1, 66.0);
        $dependent->setpoint(1, 2, 43.0);
        $dependent->setPoint(1, 3, 36.0);
        $dependent->setPoint(1, 4, 23.0);
        $dependent->setPoint(1, 5, 22.0);
        $dependent->setPoint(1, 6, 14.0);
        $dependent->setPoint(1, 7, 12.0);
        $dependent->setPoint(1, 8, 7.60);

        $this->independent2 = $independent;
        $this->dependent2   = $dependent;
    }

    public function testRegresion1()
    {
        $this->regression = new Regressions(
            $this->independent1,
            $this->dependent1,
            array('Ratio inmuebles', 'Ratio fotos'),
            array('Bajas'),
            50
        );
        $this->regression->generateDraw('test1.png');
        $this->assertFileExists('test1.png');

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

    public function testRegresion2()
    {
        $x = new MatrixBase($this->independent1->getNumRows()+1, $this->independent1->getNumCols(), 50);
        for ($m=1; $m<=$x->getNumRows(); $m++) {
            for ($n=1; $n<=$x->getNumCols(); $n++) {
                $x->setPoint($m, $n, $m==1 ? 1 : $this->independent1->getPoint($m-1, $n, 50), 50);
            }
        }
        $y = $this->dependent1;

        /**
         * @var MatrixBase $xx
         */
        $xx = $x->multiplicationMatrix($x->transposed());
        /**
         * @var MatrixBase $xy
         */
        $xy = $y->multiplicationMatrix($x->transposed());
        /**
         * @var MatrixBase $b
         */
        $b  = $xy->multiplicationMatrix($xx->inversa());

        $this->assertEquals(25.5044, round($b->getPoint(1, 1), 4));
        $this->assertEquals(-0.1320, round($b->getPoint(1, 2), 4));
        $this->assertEquals(-0.3928, round($b->getPoint(1, 3), 4));
    }

    public function testRegresion3()
    {
        $this->regression = new Regressions(
            $this->independent2,
            $this->dependent2,
            array('Ratio inmuebles', 'Ratio fotos'),
            array('Bajas'),
            50
        );
        $this->regression->generateDraw('test3.png');
        $this->assertFileExists('test3.png');

        $result = $this->regression->regresion('lineal');
        $this->assertArrayHasKey('B0', $result);
        $this->assertArrayHasKey('B1', $result);
        $this->assertArrayHasKey('B2', $result);
        $this->assertArrayHasKey('correlacion', $result);
        $this->assertArrayHasKey('r2', $result);

        if (isset($result['B0'])) {
            $this->assertEquals(-94.552028844243459, round($result['B0'], 15));
        }
        if (isset($result['B1'])) {
            $this->assertEquals(2.801551359811446, round($result['B1'], 15));
        }
        if (isset($result['B2'])) {
            $this->assertEquals(1.072682616998037, round($result['B2'], 15));
        }
        if (isset($result['correlacion'])) {
            $this->assertEquals(0.991860492170965, round($result['correlacion'], 15));
        }
        if (isset($result['r2'])) {
            $this->assertEquals(0.983787235929630, round($result['r2'], 15));
        }
    }

    public function testRegresion4()
    {
        $x = new MatrixBase($this->independent2->getNumRows()+1, $this->independent2->getNumCols(), 50);
        for ($m=1; $m<=$x->getNumRows(); $m++) {
            for ($n=1; $n<=$x->getNumCols(); $n++) {
                $x->setPoint($m, $n, $m==1 ? 1 : $this->independent2->getPoint($m-1, $n, 50), 50);
            }
        }
        $y = $this->dependent2;

        /**
         * @var MatrixBase $xx
         */
        $xx = $x->multiplicationMatrix($x->transposed());
        /**
         * @var MatrixBase $xy
         */
        $xy = $y->multiplicationMatrix($x->transposed());
        /**
         * @var MatrixBase $b
         */
        $b  = $xy->multiplicationMatrix($xx->inversa());

        $this->assertEquals(-94.552028844243459, round($b->getPoint(1, 1), 15));
        $this->assertEquals(2.801551359811446, round($b->getPoint(1, 2), 15));
        $this->assertEquals(1.072682616998037, round($b->getPoint(1, 3), 15));
    }
}
