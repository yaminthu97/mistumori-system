<?php

namespace Tests;

use App\Constants\GeneralConst;
use Exception;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mockery;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $commonLibMock;
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * @runTestsInSeparateProcesses
     * @preserveGlobalState disabled
     */
    public function setMock($class)
    {
        try{
            $this->commonLibMock = Mockery::mock('overload:' . $class);
        }catch(Exception $e){

        }
    }
}
