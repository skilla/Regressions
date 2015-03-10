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
        $this->fontSize = 100;
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
            if ($a==1) {
                $text = 'Y';
            } else {
                $text = 'X'.($a-1);
            }
            $this->imageTextCentered($image, $pos - $this->drawBoxSize, $pos + $this->drawBoxSize, $pos + $this->drawBoxSize - 1, $pos + $this->drawBoxSize - 1, 'Holalola');
        }
        imagepng($image, "test.png");
    }

    public function imageTextCentered($image, $x, $y, $x1, $y1, $text)
    {
        $tb = imagettfbbox($this->fontSize, 45, $this->fontName, $text);
        var_dump($tb);
        echo "\n";die();
        $horizontal = ceil(($x1 - $x - $tb[2]) / 2); // lower left X coordinate for text
        $vertical   = ceil(($y1 - $y + $tb[2]) / 2); // lower left X coordinate for text
        imagettftext($im, 17, 0, $x, $y, $tc, 'airlock.ttf', 'Hello world!');
    }
}
