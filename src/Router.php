<?php

/**
 * Classe de gerenciamento de rotas em php
 * @version 1.0.0
 */
final class Router
{
    private $url;
    private $path;
    private $method;
    private $routes = [];
    private $params = [];

    public function __construct(string $url)
    {
        $this->url = rtrim($url, "/");
        $this->path = "/" . trim($_SERVER["REQUEST_URI"], "/");
        $this->method = $_SERVER["REQUEST_METHOD"];
    }

    public function get(string $route, $action)
    {
        return $this->addRoute($route, $action);
    }

    /**
     * Adiciona uma nova rota na coleção
     *
     * @param string $route
     * @param [type] $action
     * @return void
     */
    private function addRoute(string $route, $action): void
    {
        $route = "/" . trim($route, "/");
        $this->routes[$route] = $action;
    }

    /**
     * faz a busca da rota, valida e por fim executa o método
     *
     * @return void
     */
    public function dispatch()
    {
        if (empty($this->routes)) {
            echo "Not Implemented";
            return false;
        }

        foreach ($this->routes as $route => $action) {

            if ($this->checkUrl($route)) {

                $result = $action;

                // verifica se é uma função anônima
                if ($result instanceof Closure) {

                    // imprime a pagina atual
                    echo $result(...$this->params);

                    // retrona o resultado da execucao
                    return true;
                }

                // se não for uma função anonima e for uma string
                if (is_string($result)) {
                    // quebra a string separando controller do metodo
                    $result = explode('::', $result);

                    // instancia um novo controller
                    $controller = new $result[0]($this);

                    //armazena o metodo
                    $action = $result[1];

                    // executa o metodo e imprime a pagina
                    echo $controller->$action(...$this->params);

                    // retorna o resultado da execucao
                    return true;
                }

                // caso não seja senm função e nem uma string valida, retorna false e imprime o erro na tela
                echo "Bad Request";
                return false;
            }
        }

        echo "Not Found";
        return false;
    }

    private function checkUrl(string $route)
    {
        preg_match_all("/\{([^\}]*)\}/", $route, $variables, PREG_SET_ORDER);
        $regex = "~^" . preg_replace('~{([^}]*)}~', "([^/]+)", $route) . "$~";
        if ($result = boolval(preg_match($regex, $this->path))) {
            $routeDiff = array_values(array_diff(explode("/", $this->path), explode("/", $route)));
            $offset = 0;
            foreach ($variables as $variable) {
                $this->params[$variable[1]] = ($routeDiff[$offset++] ?? null);
            }
        }
        return $result;
    }
}
