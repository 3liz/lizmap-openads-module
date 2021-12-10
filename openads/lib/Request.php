<?php

namespace openADS;

/**
 * Request for Jelix that support additionnal url mapping.
 */
class Request extends \jClassicRequest
{
    protected $mapping = array(
        // 'pattern pathinfo' => 'newpathinfo',
        // 'pattern pathinfo' => array ('http method' => 'basic path info'),
    );

    public function __construct($mapping)
    {
        $this->mapping = $mapping;
        parent::__construct();
    }

    protected function _initParams()
    {
        $pathInfo = $this->urlPathInfo;

        foreach ($this->mapping as $path => $realPathInfo) {
            $regexp = preg_replace('/(\\:([a-zA-Z_0-9]+))/', '(?P<${2}>[^/]+)', $path);
            $regexp = '!^' . $regexp . '$!';
            if (preg_match($regexp, $pathInfo, $m)) {
                if (is_array($realPathInfo)) {
                    if (!isset($realPathInfo[$_SERVER['REQUEST_METHOD']])) {
                        continue;
                    }
                    $this->urlPathInfo = $realPathInfo[$_SERVER['REQUEST_METHOD']];
                } else {
                    $this->urlPathInfo = $realPathInfo;
                }

                foreach ($m as $name => $value) {
                    if (!is_numeric($name)) {
                        $_GET[$name] = $value;
                    }
                }

                break;
            }
        }
        parent::_initParams();
    }
}
