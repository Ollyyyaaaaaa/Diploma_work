<?php

namespace App\Exceptions;

use App\Helpers\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;

class ApiException extends Exception
{
    protected int $httpCode;
    protected array $additionResponse;

    /**
     * ApiException constructor.
     *
     * @param int $errorCode
     * @param array $additionResponse
     * @param int $httpCode
     */
    public function __construct(int $errorCode, array $additionResponse = [], int $httpCode = 520)
    {
        $this->httpCode = $httpCode;
        $this->additionResponse = $additionResponse;
        parent::__construct( $additionResponse['message'] ?? '', $errorCode);
    }

    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
        //
    }

    /**
     * @return JsonResponse
     */
    public function render() : JsonResponse
    {
        return ApiResponse::sendResponse(
            $this->additionResponse,
            $this->code,
            $this->httpCode
        );
    }
}
