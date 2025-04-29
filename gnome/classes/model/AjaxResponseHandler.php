<?php

namespace gnome\classes\model;

// use gnome\classes\DBConnection as DBConnection;
// use gnome\classes\model\interface\Factory as Factory;



class AjaxResponseHandler
{

    private $model;

    private $response;

    public function __construct($model)
    {
        $this->model = $model;
    }

    // runs the method if it exists and wraps it in the correct response
    public function runAjax($method = 'background operation', $params = [])
    {

        if (method_exists($this->model, $method)) {
            $result = call_user_func_array([$this->model, $method], $params);


            if (is_array($result) && isset($result['success'])) {
                // control the response usually for false but can also be used for true with a different message
                // return $this->getAjaxResponse($result['success'], $result['message'] ?? '', $result['data'] ?? []);
                return $this->getAjaxResponseFromFlatArray($result);
            } elseif (is_array($result)) {
                // generic response
                return $this->getAjaxResponse(true, '', $result);
            } else {
                // returns a string that acts as a success
                return $this->getAjaxResponse($result);
            }
        } elseif ($method == 'background operation') {
            // we have a work around for 
            return $this->getAjaxResponse(true, '', ['status' => 'Background operation in progress']);
        } else {
            return $this->getAjaxResponse(false, "Method $method does not exist.");
        }
    }

    private function getAjaxResponse($success, $message = '', $data = [])
    {
        if ($success) {
            $this->response = [
                'status' => 'success', 
                'message' => $message ?: 'Operation successful.', 
                'data' => $data
            ];
        } else {
            $this->response = ['status' => 'error', 'message' => $message ?: 'Operation failed.'];
        }
        return $this;
    }

    // handles spreading unknown keys into 'data'
    private function getAjaxResponseFromFlatArray(array $result)
    {
        $base = [
            'success' => $result['success'] ?? false,
            'message' => $result['message'] ?? ''
        ];

        // Extract all other keys into 'data'
        $extraData = array_diff_key($result, ['success' => '', 'message' => '']);

        return $this->getAjaxResponse($base['success'], $base['message'], $extraData);
    }

    function getResponse($isJson = true): string|array
    {
        return $isJson ? json_encode($this->response) : $this->response;
    }

    /**
     * global method that allows for talored json responses
     *
     * 
     * */
    static function getAjaxResponseRaw($status = '', $message = '', $data = [])
    {

        $response = ['status' => $status, 'message' => $message ?: 'Operation successful.', 'data' => $data];

        return json_encode($response);
    }
}
