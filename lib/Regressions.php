<?php
/**
 * Created by PhpStorm.
 * User: Sergio Zambrano Delfa <sergio.zambrano@gmail.com>
 * Date: 8/3/15
 * Time: 19:12
 */
namespace Skilla\Regressions;

use Skilla\Matrix\MatrixBase;
use Skilla\Regressions\Exception;

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
     * @var array
     */
    private $independentTitles;

    /**
     * @var array
     */
    private $dependentTitle;

    /**
     * @var string
     */
    private $fontName;

    /**
     * @var int
     */
    private $fontSize;

    /**
     * Constructor de la clase, los parámetros son opcionales y se pueden asignar posteriormente mediante los métodos,
     * para el correcto funcionamiento de la clase los valores que se han de asignar como mínimo son $independentVars y
     * $dependentVars.
     *
     * @param MatrixBase $independentVars
     * @param MatrixBase $dependentVars
     * @param array $independentTitles
     * @param array $dependentTitle
     */
    public function __construct(
        MatrixBase $independentVars = null,
        MatrixBase $dependentVars = null,
        array $independentTitles = null,
        array $dependentTitle = null
    ) {
        $this->independentVars = $independentVars;
        $this->dependentVars = $dependentVars;
        $this->independentTitles = $independentTitles;
        $this->dependentTitle = $dependentTitle;
        $this->fontName = __DIR__ . '/../fonts/OpenSans.ttf';
        $this->fontSize = 30;
    }

    /**
     * Asigna la matriz de la(s) variable(s) independiente(s) "x(s)"
     *
     * @param MatrixBase $independentVars
     */
    public function setIndependentVars(MatrixBase $independentVars)
    {
        $this->independentVars = $independentVars;
    }

    /**
     * Asigna la matriz de la variable dependiente "y"
     *
     * @param MatrixBase $dependentVars
     */
    public function setDependentVar(MatrixBase $dependentVars)
    {
        $this->dependentVars = $dependentVars;
    }

    /**
     * Asigna un array (opcional) con los títulos para las variables independientes $array[0] => X1,$array[1] => X2
     *
     * @param array $independentTitles
     */
    public function setIndependentTitles(array $independentTitles)
    {
        $this->independentTitles = $independentTitles;
    }

    /**
     * Asigna un array (opcional) con el título pala la variable dependiente $array[0] => Y
     *
     * @param array $dependentTitle
     */
    public function setDependentTitle(array $dependentTitle)
    {
        $this->dependentTitle = $dependentTitle;
    }

    /**
     * Genera un gráfico con todas las combinaciones de gráficas entre todas las "x" y la "y", ej: f(x1,x2) = y
     * generará un gráfico con 6 gráficas (x1,y), (x1,x2), (x2,y), (x2,x1), (y,x1), (y,x2).
     * f(x1,x2,x3) = y generará 12 gráficas.
     * Las gráficas maximizaran el espacio de dibujo disponible, mostrarán la línea de tendencia, y la fórmula con ß0
     * ß1 y R2
     *
     * @param string $filename
     */
    public function generateDraw($filename = null)
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
                $text = isset($this->dependentTitle[0]) ? $this->dependentTitle[0] : 'Y';
            } else {
                $text = isset($this->independentTitles[$a-1]) ? $this->independentTitles[$a-1] : 'X'.$a;
            }
            $pos = $this->drawBoxSize * $a;
            $this->imageTextCentered(
                $image,
                $color,
                45,
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
        if (is_null($filename)) {
            $filename = 'test.png';
        }
        imagepng($image, $filename);
        return $filename;
    }

    private function imageTextCentered($image, $color, $angulo, $fontSize, $fontName, $x, $y, $x1, $y1, $text)
    {
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

    /**
     * @param $image
     * @param int $colorPoints
     * @param int $colorLine
     * @param int $colorFormula
     * @param int $x
     * @param int $y
     * @param int $x1
     * @param int $y1
     * @param MatrixBase $matrixX
     * @param MatrixBase $matrixY
     */
    private function drawDotPlot($image, $colorPoints, $colorLine, $colorFormula, $x, $y, $x1, $y1, MatrixBase $matrixX, MatrixBase $matrixY)
    {
        $width   = $x1 -$x;
        $height  = $y1 -$y;
        $arrayX  = $matrixX->getArray();
        $minX    = min($arrayX[1]);
        $maxX    = max($arrayX[1]);
        $arrayY  = $matrixY->getArray();
        $minY    = min($arrayY[1]);
        $maxY    = max($arrayY[1]);

        $data    = $this->regressionSimple($matrixX, $matrixY);

        $minY    = min($minY, $data['B0'] + $data['B1'] * $minX);
        $minY    = min($minY, $data['B0'] + $data['B1'] * $maxX);
        $maxY    = max($maxY, $data['B0'] + $data['B1'] * $minX);
        $maxY    = max($maxY, $data['B0'] + $data['B1'] * $maxX);

        $factorX = $width / abs($maxX - $minX);
        $factorY = $height / abs($maxY - $minY);

        for ($abscisas=1; $abscisas<=$matrixX->getNumCols(); $abscisas++) {
            $posX = $x + (($arrayX[1][$abscisas] - $minX) * $factorX);
            $posY = $y1 - (($arrayY[1][$abscisas] - $minY) * $factorY);

            imagesetpixel($image, $posX, $posY, $colorPoints);
            imagesetpixel($image, $posX+1, $posY, $colorPoints);
            imagesetpixel($image, $posX, $posY+1, $colorPoints);
            imagesetpixel($image, $posX+1, $posY+1, $colorPoints);
        }

        $posX1 = $x  + (($minX - $minX) * $factorX);
        $posY1 = $y1 - ((($data['B0'] + $data['B1'] * $minX) - $minY) * $factorY);
        $posX2 = $x  + (($maxX - $minX) * $factorX);
        $posY2 = $y1 - ((($data['B0'] + $data['B1'] * $maxX) - $minY) * $factorY);
        imageline($image, $posX1, $posY1, $posX2, $posY2, $colorLine);
        $this->imageTextCentered(
            $image,
            $colorFormula,
            0,
            $this->fontSize/3,
            $this->fontName,
            $x,
            $y,
            $x1,
            $y1,
            'y='.round($data['B0'], 3).'+'.round($data['B1'], 3)."x\nR2=".round($data['r2'], 3)
        );

    }

    public function regresion($tipo = 'lineal')
    {
        if ($this->independentVars->getNumRows()==1) {
            return $this->regressionSimple($this->independentVars, $this->dependentVars, $tipo);
        }
        return $this->regresionMultiple($this->independentVars, $this->dependentVars, $tipo);
    }

    /**
     * Genera los datos de regresión para una variable dependiente "Y" y una única variable independiente "x"
     * @param MatrixBase $x
     * @param MatrixBase $y
     * @param string $tipo
     * @return array
     */
    public function regressionSimple(MatrixBase $x, MatrixBase $y, $tipo = 'lineal')
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
            'B0'          => $ordenada,
            'B1'          => $pendiente,
            'correlacion' => $correlacion,
            'r2'          => pow($correlacion, 2),
        );
    }

    public function regresionMultiple(MatrixBase $x, MatrixBase $y, $tipo = 'lineal')
    {
        $ordenada    = 0;
        $pendiente   = 0;
        $correlacion = 0;

        $newX = new MatrixBase($x->getNumRows()+1, $x->getNumCols());
        for ($m=1; $m<=$newX->getNumRows(); $m++) {
            for ($n=1; $n<=$newX->getNumCols(); $n++) {
                $newX->setPoint($m, $n, $m==1 ? 1 : $x->getPoint($m-1, $n));
            }
        }
        $B = new MatrixBase(1, $newX->getNumRows());
        $P = new MatrixBase($newX->getNumRows(), $newX->getNumRows());
        for ($i=1; $i<=$newX->getNumRows(); $i++) {
            $sum = 0;
            for ($j = 1; $j <= $newX->getNumCols(); $j++) {
                $sum += $newX->getPoint($i, $j) * $y->getPoint(1, $j);
            }
            $B->setPoint(1, $i, $sum);

            for ($k=1; $k<=$newX->getNumRows(); $k++) {
                $sum = 0;
                for ($j = 1; $j <= $newX->getNumCols(); $j++) {
                    $sum += $newX->getPoint($i, $j) * $newX->getPoint($k, $j);
                    $P->setPoint($i, $k, $sum);
                }
            }
        }
        $P = $P->inversa();
        $R = array();
        for ($m=1; $m<=$P->getNumRows(); $m++) {
            $sum = 0;
            for ($n=1; $n<=$P->getNumCols(); $n++) {
                $sum += $P->getPoint($m, $n) * $B->getPoint(1, $n);
            }
            $R[$m] = $sum;
        }

        $result = array();
        $result['tipo']        = $tipo;

        for ($m=1; $m<=count($R); $m++) {
            $result['B'.($m-1)] = $R[$m];
        }

        unset($newX, $B, $P);

        $predicted = array();
        $residual  = array();
        $SE = 0;
        $ST = 0;

        for ($n=1; $n<=$x->getNumCols(); $n++) {
            $Y = $R[1];
            for ($m=1; $m<=$x->getNumRows(); $m++) {
                $Y += $R[$m+1] * $x->getPoint($m, $n);
            }
            $predicted[$n]  = $Y;
            $residual[$n]   = $y->getPoint(1, $n) - $predicted[$n];
            $SE            += $residual[$n];
            $ST            += $y->getPoint(1, $n);
        }

        $MSE = $SE / $x->getNumCols();
        $MST = $ST / $x->getNumCols();
        $SSE = 0;
        $SST = 0;
        for ($n=1; $n<=$x->getNumCols(); $n++) {
            $SSE += ($residual[$n] - $MSE) * ($residual[$n] - $MSE);
            $SST += ($y->getPoint(1, $n) - $MST) * ($y->getPoint(1, $n) - $MST);
        }
        $FR = (($x->getNumCols() - $x->getNumRows() - 1) * ($SST - $SSE)) / ($x->getNumRows() * $SSE) ;
        $RRSQ = 1 - ($SSE / $SST);

        $result['correlacion']  = pow($RRSQ, 1/2);
        $result['r2']           = $RRSQ;
        $result['estadisticoF'] = $FR;
        return $result;
    }
}
