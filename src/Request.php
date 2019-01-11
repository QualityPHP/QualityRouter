<?php

namespace QualityPHP\QualityRouter;

use Exception;

class Request
{
    /**
     * The router instance
     *
     * @var \QualityPHP\QualityRouter\QRouter
     */
    protected $router;

    /**
     * The configuration array
     *
     * @var array
     */
    protected $config;
    
    /**
     * The request's array
     *
     * @var array
     */
    protected $request;

    /**
     * Create a new instance of the Request class
     *
     * @param \QualityPHP\QualityRouter\QRouter $router
     * @return void
     */
    public function __construct(QRouter $router, array $request)
    {
        $this->router = $router;
        $this->request = $request;
        $this->config = $this->router->config;
    }

    /**
     * Intercept the request
     *
     * @return void
     */
    public function intercept()
    {
        $uri = $this->getRequestUri($this->router->config);

        if ($this->router::$rConfig['AUTO']) {
            return $this->automaticResponse($uri);
        }
        
        $this->regularResponse($uri);
    }
    
    /**
     * Regular response; The route's callback will be called
     *
     * @param string $uri
     * @return void
     */
    protected function regularResponse(string $uri)
    {
        $this->checkURI($uri);

        $route = $this->router->getRoute($uri);

        $this->checkMethod($route);

        return call_user_func($route->CALLBACK);
    }

    /**
     * Automatic Response; The file contents from the given filename
     * will be included
     *
     * @param string $uri
     * @return void
     * @throws \Exception
     */
    protected function automaticResponse(string $uri)
    {
        $appDir = $this->appDir();
        #TODO: Add dynamic extensions support
        if (!file_exists($appDir.$uri.'.php')) {
            http_response_code(405);
            throw new Exception('Not found: '.$appDir.$uri.'.php', 404);
        }

        include $appDir.$uri.'.php';
    }

    /**
     * Check if the request method is valid
     *
     * @param object $route
     * @return void
     * @throws \Exception
     */
    protected function checkMethod(object $route)
    {
        if ($route->METHOD != $this->request['REQUEST_METHOD']) {
            #TODO: Implement error pages
            http_response_code(405);
            throw new Exception("<h1> 405 Method Not Allowed</h1>", 405);
        }
    }

    /**
     * Check if the request URI is valid
     *
     * @param string $uri
     * @return void
     * @throws \Exception
     */
    protected function checkURI(string $uri)
    {
        if (!$this->router->has($uri)) {
            #TODO: Implement error pages
            http_response_code(404);
            throw new Exception("<h1> 404 Not Found</h1>".
                                "<hr/>".
                                "<p>The requested page was not found.</p>", 404);
        }
    }
    
    /**
     * Get the request URI
     *
     * @param array $config
     * @return string
     */
    protected function getRequestUri()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $currentDir = '/'.basename(__DIR__);

        return str_replace($currentDir, '', $uri);
    }

    /**
     * Get the application directory
     *
     * @return string
     */
    protected function appDir()
    {
        return $this->config['APP_DIR'];
    }
}
