<?php
namespace app\admin\controller;

class Index extends Base
{
    public function index()
    {
        return view();
    }
    
    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }
}
