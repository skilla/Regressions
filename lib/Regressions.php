<?php
/**
 * Created by PhpStorm.
 * User: Sergio Zambrano Delfa <sergio.zambrano@gmail.com>
 * Date: 8/3/15
 * Time: 19:12
 */
namespace skilla\regressions;

use skilla\matrix\MatrixBase;
use skilla\regressions\Exception;

class Regressions
{
    /**
     * @var int
     */
    private $drawBoxSize = 300;

    /**
     * @var MatrixBase $independentVars
     */
    private $independentVars;

    /**
     * @var MatrixBase $independentVars
     */
    private $dependentVars;

    /**
     * @var string
     */
    private $fontName;

    /**
     * @var int
     */
    private $fontSize;

    public function __construct(MatrixBase $independentVars, MatrixBase $dependentVars)
    {
        $this->independentVars = $independentVars;
        $this->dependentVars = $dependentVars;
        $this->fontName = __DIR__ . '/../fonts/OpenSans.ttf';
        $this->fontSize = 30;
    }

    public function setIndependentVars(MatrixBase $independentVars)
    {
        $this->independentVars = $independentVars;
    }

    public function setDependentVar(MatrixBase $dependentVars)
    {
        $this->dependentVars = $dependentVars;
    }

    public function generateDraw()
    {
        $boxes = $this->independentVars->getNumRows() + $this->dependentVars->getNumRows();
        $drawSize = ($this->drawBoxSize + 1) * $boxes - 2;
        $image = imagecreatetruecolor($drawSize, $drawSize);
        $color = imagecolorallocate($image, 255, 255, 255);
        imagefilledrectangle($image, 0, 0, $drawSize, $drawSize, $color);
        $color = imagecolorallocate($image, 255, 0, 0);
        for ($a=1; $a<$boxes; $a++) {
            $pos = ($this->drawBoxSize + 1) * $a - 1;
            imageline($image, 0, $pos, $drawSize, $pos, $color);
            imageline($image, $pos, 0, $pos, $drawSize, $color);
        }
        for ($a=0; $a<$boxes; $a++) {
            if ($a==0) {
                $text = 'Y';
            } else {
                $text = 'X'.$a;
            }
            $pos = $this->drawBoxSize * $a;
            $this->imageTextCentered(
                $image,
                $color,
                $this->fontSize,
                $this->fontName,
                $pos,
                $pos,
                $pos + $this->drawBoxSize,
                $pos + $this->drawBoxSize,
                $text
            );
        }

        $colorPoint   = imagecolorallocate($image, 0, 0, 255);
        $colorLine    = imagecolorallocate($image, 0, 255, 0);
        $colorFormula = imagecolorallocate($image, 64, 64, 64);

        for ($a=0; $a<$boxes; $a++) {
            for ($b=0; $b<$boxes; $b++) {
                if ($a==$b) {
                    continue;
                }
                if ($a==0) {
                    $x = $this->dependentVars->getRow(1);
                } else {
                    $x = $this->independentVars->getRow($a);
                }
                if ($b==0) {
                    $y = $this->dependentVars->getRow(1);
                } else {
                    $y = $this->independentVars->getRow($b);
                }
                $this->drawDotPlot(
                    $image,
                    $colorPoint,
                    $colorLine,
                    $colorFormula,
                    ($this->drawBoxSize + 1) * $a,
                    ($this->drawBoxSize + 1) * $b,
                    ($this->drawBoxSize + 1) * ($a + 1) - 2,
                    ($this->drawBoxSize + 1) * ($b + 1) - 2,
                    $x,
                    $y
                );
            }
        }
        imagepng($image, "test.png");
    }

    public function imageTextCentered($image, $color, $fontSize, $fontName, $x, $y, $x1, $y1, $text)
    {
        $angulo     = 0;
        $tb         = imagettfbbox($fontSize, $angulo, $fontName, $text);
        $minx       = $tb[9] = min($tb[0], $tb[2], $tb[4], $tb[6]);
        $maxx       = $tb[10] = max($tb[0], $tb[2], $tb[4], $tb[6]);
        $miny       = $tb[11] = min($tb[1], $tb[3], $tb[5], $tb[7]);
        $maxy       = $tb[12] = max($tb[1], $tb[3], $tb[5], $tb[7]);
        $width      = $tb[13] = abs($maxx-$minx);
        $height     = $tb[14] = abs($maxy-$miny);
        $horizontal = $x  + ceil(($x1 - $x - $width) / 2); // lower left X coordinate for text
        $vertical   = $y1 - ceil(($y1 - $y - $height) / 2); // lower left Y coordinate for text
        imagettftext($image, $fontSize, $angulo, $horizontal, $vertical, $color, $fontName, $text);
    }

    public function drawDotPlot($image, $colorPoints, $colorLine, $colorFormula, $x, $y, $x1, $y1, MatrixBase $matrixX, MatrixBase $matrixY)
    {
        $width   = $x1 -$x;
        $height  = $y1 -$y;
        $arrayX  = $matrixX->getArray();
        $minX    = min($arrayX[1]);
        $maxX    = max($arrayX[1]);
        $arrayY  = $matrixY->getArray();
        $minY    = min($arrayY[1]);
        $maxY    = max($arrayY[1]);

        $data    = $this->regression($matrixX, $matrixY);
        var_dump($data);
        $minY    = min($minY, $data['ordenada'] + $data['pendiente'] * $minX);
        $minY    = min($minY, $data['ordenada'] + $data['pendiente'] * $maxX);
        $maxY    = max($maxY, $data['ordenada'] + $data['pendiente'] * $minX);
        $maxY    = max($maxY, $data['ordenada'] + $data['pendiente'] * $maxX);

        $factorX = $width / abs($maxX - $minX);
        $factorY = $height / abs($maxY - $minY);

        for ($abscisas=1; $abscisas<=$matrixX->getNumCols(); $abscisas++) {
            $posX = $x + (($arrayX[1][$abscisas] - $minX) * $factorX);
            $posY = $y1 - (($arrayY[1][$abscisas] - $minY) * $factorY);
            echo  "($posX, $posY\n";

            imagesetpixel($image, $posX, $posY, $colorPoints);
            imagesetpixel($image, $posX+1, $posY, $colorPoints);
            imagesetpixel($image, $posX, $posY+1, $colorPoints);
            imagesetpixel($image, $posX+1, $posY+1, $colorPoints);
        }

        $posX1 = $x  + (($minX - $minX) * $factorX);
        $posY1 = $y1 - ((($data['ordenada'] + $data['pendiente'] * $minX) - $minY) * $factorY);
        $posX2 = $x  + (($maxX - $minX) * $factorX);
        $posY2 = $y1 - ((($data['ordenada'] + $data['pendiente'] * $maxX) - $minY) * $factorY);
        imageline($image, $posX1, $posY1, $posX2, $posY2, $colorLine);
        $this->imageTextCentered(
            $image,
            $colorFormula,
            $this->fontSize/3,
            $this->fontName,
            $x,
            $y,
            $x1,
            $y1,
            'y='.round($data['ordenada'], 3).'+'.round($data['pendiente'], 3)."x\nR2=".round($data['r2'], 3)
        );

    }

    public function regression(MatrixBase $x, MatrixBase $y, $tipo='lineal')
    {
        $sx  = 0.0;
        $sy  = 0.0;
        $sx2 = 0.0;
        $sy2 = 0.0;
        $pxy = 0.0;
        for ($a=1; $a<=$x->getNumCols(); $a++) {
            $sx  += $x->getPoint(1, $a);
            $sy  += $y->getPoint(1, $a);
            $sx2 += $x->getPoint(1, $a) * $x->getPoint(1, $a);
            $sy2 += $y->getPoint(1, $a) * $y->getPoint(1, $a);
            $pxy += $x->getPoint(1, $a) * $y->getPoint(1, $a);
        }
        $pendiente = ($x->getNumCols()*$pxy-$sx*$sy)/($x->getNumCols()*$sx2-$sx*$sx);
        $ordenada  = ($sy-$pendiente*$sx)/$x->getNumCols();

        $mediaX    = $sx / $x->getNumCols();
        $mediaY    = $sy / $x->getNumCols();
        $dpxy      = 0.0;
        $dsx2      = 0.0;
        $dsy2      = 0.0;
        for ($a=1; $a<=$x->getNumCols(); $a++) {
            $dpxy += ($x->getPoint(1, $a) - $mediaX) * ($y->getPoint(1, $a) - $mediaY);
            $dsx2 += ($x->getPoint(1, $a) - $mediaX) * ($x->getPoint(1, $a) - $mediaX);
            $dsy2 += ($y->getPoint(1, $a) - $mediaY) * ($y->getPoint(1, $a) - $mediaY);
        }

        $correlacion = $dpxy/sqrt($dsx2*$dsy2);
        return array(
            'tipo'        => $tipo,
            'pendiente'   => $pendiente,
            'ordenada'    => $ordenada,
            'correlacion' => $correlacion,
            'r2'          => pow($correlacion, 2),
        );
    }
}
