<?php
class Panel
{
    public $id;
    public $description;
    public $roofOrientation;
    public $roofPitch;

    /**
     * Constructor
     */
    function __construct()
    {
        $this->id = 1;
        $this->description='10 Aleo S_18 230W';
        $this->roofOrientation='100';
        $this->roofPitch='45';
    }
}