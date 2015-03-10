<?php
/**
 * Created by PhpStorm.
 * User: Sergio Zambrano Delfa <sergio.zambrano@gmail.com>
 * Date: 8/3/15
 * Time: 19:12
 */
namespace skilla\regressions;

use skilla\matrix\MatrixBase;

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
            if ($a==1) {
                $text = 'Y';
            } else {
                $text = 'X'.($a-1);
            }
            $pos = $this->drawBoxSize * $a;
            $this->imageTextCentered($image, $color, $pos, $pos, $pos + $this->drawBoxSize, $pos + $this->drawBoxSize, 'Holalola');

        }
        imagepng($image, "test.png");
    }

    public function imageTextCentered($image, $color, $x, $y, $x1, $y1, $text)
    {
        $angulo = 0;
        $tb = imagettfbbox($this->fontSize, $angulo, $this->fontName, $text);
        $minx = $tb[9] = min($tb[0], $tb[2], $tb[4], $tb[6]);
        $maxx = $tb[10] = max($tb[0], $tb[2], $tb[4], $tb[6]);
        $miny = $tb[11] = min($tb[1], $tb[3], $tb[5], $tb[7]);
        $maxy = $tb[12] = max($tb[1], $tb[3], $tb[5], $tb[7]);
        $width  = $tb[13] = abs($maxx-$minx);
        $height = $tb[14] = abs($maxy-$miny);
        var_dump($tb);
        var_dump(array($x, $y, $x1, $y1));
        $horizontal = $x  + ceil(($x1 - $x - $width) / 2); // lower left X coordinate for text
        $vertical   = $y1 - ceil(($y1 - $y - $height) / 2); // lower left Y coordinate for text
        imagettftext($image, $this->fontSize, $angulo, $horizontal, $vertical, $color, $this->fontName, $text);
        imagettftext($image, $this->fontSize, 0, $x, $y, $color, $this->fontName, $text);
    }
}
