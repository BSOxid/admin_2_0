<?php

/**
 * Request Dispatcher
 */
class Admin2_Dispatcher
{
    /**
     * Request object.
     *
     * @var Admin2_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * Instance of an class to format the output.
     *
     * @var Admin2_Output_Processor_Interface
     */
    protected $_outputProcessor;

    /**
     * The result of the controller action.
     *
     * @var Admin2_Controller_Result
     */
    protected $_result;

    /**
     * Directory where the controller are located.
     *
     * @var string
     */
    protected $_controllerDir;

    /**
     * Constructor
     *
     * @param Admin2_Controller_Request_Abstract $request
     * @param Admin2_Controller_Result           $result
     * @param array                              $config
     *
     * @return Admin2_Dispatcher
     */
    public function __construct(
        Admin2_Controller_Request_Abstract $request,
        Admin2_Controller_Result $result,
        $config = array()
    )
    {
        $this->_request         = $request;
        $this->_result          = $result;
        $outputProcClass        = 'Admin2_Output_Processor_' . ucfirst($request->getFormat());
        $this->_outputProcessor = new $outputProcClass();

        $this->_controllerDir = APPLICATION_PATH . '/controllers';
        if (isset($config['controllerDir'])) {
            $this->_controllerDir = rtrim($config['controllerDir'], '/');
        }
    }

    /**
     * Starting point for dispatcher execution
     *
     * @throws Admin2_Dispatcher_Exception
     *
     * @return void
     */
    public function run()
    {
        try {
            $controllerName = $this->_request->getContoller();
            if ($controllerName === null) {
                throw new Admin2_Dispatcher_Exception("There was no controller specified.");
            }

            $spacedClass = str_replace('_', ' ', $controllerName);
            $class = str_replace(' ', '_', ucwords($spacedClass)) . 'Controller';

            $controllerFile = $this->_controllerDir . '/' . str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';

            if (!file_exists($controllerFile)) {
                require_once 'Admin2/Dispatcher/Exception.php';
                throw new Admin2_Dispatcher_Exception(
                    "PHP file which contains the controller '$class', doesn't exists."
                );
            }

            include_once $controllerFile;

            if (!class_exists($class)) {
                require_once 'Admin2/Dispatcher/Exception.php';
                throw new Admin2_Dispatcher_Exception("Can't find controller '$class'.");
            }

            $controller = new $class($this->_request, $this->_result);
            if (!$controller instanceof Admin2_Controller_Abstract) {
                require_once 'Admin2/Dispatcher/Exception.php';
                throw new Admin2_Dispatcher_Exception(
                    "The controller '$class' doesn't extend the class 'Admin2_Controller_Abstract'."
                );
            }

            $method = $this->_request->getMethod();

            if ($method === null) {
                require_once 'Admin2/Dispatcher/Exception.php';
                throw new Admin2_Dispatcher_Exception("There was no request method specified.");
            }

            $realMethod = strtolower($method);
            $entity = $this->_request->getEntity();
            if (empty($entity)) {
                $realMethod = 'getList';
            }

            if (!method_exists($controller, $realMethod)) {
                require_once 'Admin2/Dispatcher/Exception.php';
                throw new Admin2_Dispatcher_Exception(
                    "The controller '$class' doesn't provide the method '$realMethod'."
                );
            }

            $controller->$realMethod();

            $processedData = '';
            if ($this->_result->hasData()) {
                $processedData = $this->_outputProcessor->process($this->_result);
                $responseCode = $this->_result->getResponseCode();
                if (!empty($responseCode)) {
                    header($responseCode);
                }
            } else {
                header('HTTP/1.0 204 No Content', true);
            }

            foreach ($this->_result->getResponseHeader() as $headerKey => $headerValue) {
                header($headerKey . ': ' . $headerValue);
            }

        } catch (Exception $exception) {
            $errorController = new Admin2_Controller_Error();
            $processedData   = $errorController->error($exception);
        }

        echo $processedData;
    }
}
