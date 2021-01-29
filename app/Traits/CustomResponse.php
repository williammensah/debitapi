<?php

namespace App\Traits;

trait CustomResponse
{

  public function success($data = [], $message = 'Operation Successful.', $code = "200")
  {
    return $this->response($data, $message, $code);
  }
  public function error($data = [], $message = 'Operation failed.', $code = "500")
  {
    return $this->response($data, $message, $code);
  }
  public function validationFailed($data = [], $message = 'Validation failed.', $code = "400")
  {
    return $this->response($data, $message, $code);
  }

  public function response($data = [], $message = '', $code = '')
  {
    if (empty($data) && $code !== "200") {
      return response()->json([
        'responseCode' => $code,
        'responseMessage' => $message,

      ], 200);
    }

    return response()->json([
      'responseCode' => $code,
      'responseMessage' => $message,
      'data' => $data
    ], 200);
  }
}
