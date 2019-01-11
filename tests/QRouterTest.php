<?php

namespace QualityPHP\QualityRouter\Tests;

use QualityPHP\QualityRouter\QRouter;
use QualityPHP\QualityRouter\Request;
use PHPUnit\Framework\TestCase;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class QRouterTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var \Mockery\MockInterface */
    protected $QRouter;

    /** @var object */
    protected $route;

    /** @var \org\bovigo\vfs\vfsStreamDirectory */
    protected $APP_DIR;
    
    public function setUp()
    {
        $this->QRouter = Mockery::mock(QRouter::class)
                                ->shouldAllowMockingProtectedMethods();
        $this->QRouter->config = ['APP_DIR' => 'vfs://APP'];
        
        $this->APP_DIR = vfsStream::setup('APP');
        vfsStream::newFile('FILENAME.php')->at($this->APP_DIR)->setContent('FOOCONTENT');

        $this->route = Mockery::mock('overload:route');
        $this->route->METHOD = 'GET';
        $this->route->CALLBACK = function () {
        };
    }

    public function tearDown()
    {
        \Mockery::close();
    }

    /**
    * @test
    * @small
    */
    public function respondsToValidRequest()
    {
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->QRouter->shouldReceive('has')
                      ->once()
                      ->andReturn(true);

        $this->QRouter->shouldReceive('getRoute')
                      ->once()
                      ->andReturn($this->route);

        $this->route->shouldReceive('CALLBACK')
                    ->once();
  
        $request = new Request($this->QRouter, $_SERVER);
        $request->intercept();
    }

    /**
     * @test
     * @small
     */
    public function returnsValidCallbackOuput()
    {
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->route->CALLBACK = function () {
            echo "TEST";
        };

        $this->QRouter->shouldReceive('has')
                      ->once()
                      ->andReturn(true);

        $this->QRouter->shouldReceive('getRoute')
                      ->once()
                      ->andReturn($this->route);

        $this->route->shouldReceive('CALLBACK')
                    ->once();
  
        $request = new Request($this->QRouter, $_SERVER);

        $request->intercept();

        $this->expectOutputString('TEST');
    }

    /**
     * @test
     * @small
     * @expectedException \Exception
     * @expectedExceptionCode 405
     */
    public function throwsExceptionForInvalidMethod()
    {
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $this->QRouter->shouldReceive('has')
                ->once()
                ->andReturn(true);

        $this->QRouter->shouldReceive('getRoute')
                      ->once()
                      ->andReturn($this->route);

        $this->route->shouldReceive('CALLBACK')
                    ->once();
  
        $request = new Request($this->QRouter, $_SERVER);
        $request->intercept();
    }

    /**
     * @test
     * @small
     * @expectedException \Exception
     * @expectedExceptionCode 404
     */
    public function throwsExceptionForInvalidRoute()
    {
        $_SERVER['REQUEST_URI'] = '/INVALID-URI';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->QRouter->shouldReceive('has')
                ->once()
                ->andReturn(false);

        $this->QRouter->shouldNotReceive('getRoute');

        $this->route->shouldNotReceive('CALLBACK');

        $request = new Request($this->QRouter, $_SERVER);
        $request->intercept();
    }

    /**
     * @test
     * @small
     */
    public function autoRespondsToValidFilename()
    {
        $_SERVER['REQUEST_URI'] = '/FILENAME';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->QRouter::$rConfig['AUTO'] = true;
        
        $request = new Request($this->QRouter, $_SERVER);
        $request->intercept();

        $this->expectOutputString('FOOCONTENT');
    }

    /**
     * @test
     * @small
     * @expectedException \Exception
     * @expectedExceptionCode 404
     */
    public function autoThrowsExceptionForInvalidFilename()
    {
        $_SERVER['REQUEST_URI'] = '/INVALID-FILENAME';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->QRouter::$rConfig['AUTO'] = true;

        $request = new Request($this->QRouter, $_SERVER);
        
        $request->intercept();
    }
}
