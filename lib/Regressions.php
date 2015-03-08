<?php
/**
 * Created by PhpStorm.
 * User: Sergio Zambrano Delfa <sergio.zambrano@gmail.com>
 * Date: 8/3/15
 * Time: 19:12
 */
namespace skilla\regressions;

include "../../Matrix/lib/MatrixBase.php";

use skilla\matrix;

class Regressions
{
    public function __construct()
    {
    }

    public function setIndependentVars(Matrix $independentVars)
    {
        $this->independentVars = $independentVars;
    }
}