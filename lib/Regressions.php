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

    public function __construct(MatrixBase $independentVars, MatrixBase $dependentVars)
    {
        $this->independentVars = $independentVars;
        $this->dependentVars = $dependentVars;
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
            
        }
        imagepng($image, "test.png");
    }
}
