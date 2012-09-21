<?php
class Panel
{
    public $id;
    public $inverterId;
    public $description;
    public $roofOrientation;
    public $roofPitch;

    /**
     * Constructor
     */
    function __construct()
    {
        $this->id = -1;
        $this->inverterId = 0;
        $this->description='';
        $this->roofOrientation='';
        $this->roofPitch='';
    }
}