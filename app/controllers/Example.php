<?php


use mono\models\Controller;

class Example extends Controller
{

	public function __construct()
    {
        parent::__construct();
        //Here you can execute code that happens before the method
    }

    public function index()
    {
        //TO disable automatic render return false;
        return true;
    }
}
