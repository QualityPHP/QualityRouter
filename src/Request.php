<?php

namespace yak0d3\QualityRouter;

class Request
{
    /**
     * The router instance
     *
     * @var \yak0d3\QualityRouter\QRouter
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
     * @param \yak0d3\QualityRouter\QRouter $router
     * @return void
     */
    public function __construct(QRouter $router)
    {
        $this->router = $router;
        $this->request = $_SERVER;
    }

    /**
     * Intercept the request
     *
     * @return void
     */
    public function intercept()
    {
        $uri = $this->getRequestUri($this->router->config);

        $this->checkURI($uri);

        $route = $this->router->getRoute($uri);

        $this->checkMethod($route);

        call_user_func($route->CALLBACK);
    }

    /**
     * Check if the request method is valid
     *
     * @param object $route
     * @return void
     */
    protected function checkMethod(object $route)
    {
        if ($route->METHOD != $this->request['REQUEST_METHOD']) {
            #TODO: Implement error pages
            http_response_code(405);
            echo "<h1> 405 Method Not Allowed</h1>";
            die();
        }
    }

    /**
     * Check if the request URI is valid
     *
     * @param string $uri
     * @return void
     */
    protected function checkURI(string $uri)
    {
        if (!$this->router->has($uri)) {
            #TODO: Implement error pages
            http_response_code(404);
            echo "<h1> 404 Not Found</h1>";
            echo "<hr/>";
            echo "<p>The requested page was not found.</p>";
            die();
        }
    }
    /**
     * Get the request URI
     *
     * @param array $config
     * @return string
     */
    protected function getRequestUri(array $config)
    {
        $uri = $_SERVER['REQUEST_URI'];
        $currentDir = '/'.basename(__DIR__);

        return str_replace($currentDir, '', $uri);
    }
}
