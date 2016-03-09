<?php

namespace App\Http\Controllers\Api;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use App\Http\Controllers\Controller;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\ArraySerializer;

class ApiController extends Controller
{
    /**
     * @var \League\Fractal\Manager
     */
    protected $fractal;

    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * ApiController constructor.
     *
     * @param \League\Fractal\Manager $fractal
     */
    public function __construct(Manager $fractal)
    {
        if (config('crawler.api.use_auth')) {
            $this->middleware('Hedii\Crawler\Api\Middleware\ErrorIfNotCorrectUser');
        }

        $this->fractal = $fractal;
        $this->fractal->setSerializer(new ArraySerializer());
    }

    /**
     * Getter for statusCode
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Setter for statusCode
     *
     * @param int $statusCode Value to set
     *
     * @return self
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Respond with an item.
     *
     * @param $item
     * @param $callback
     * @param string $resourceName
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondWithItem($item, $callback, $resourceName)
    {
        $resource = new Item($item, $callback, $resourceName);
        $rootScope = $this->fractal->createData($resource);

        return $this->respondWithArray($rootScope->toArray());
    }

    /**
     * Respond with a collection of items.
     *
     * @param $collection
     * @param $callback
     * @param string $resourceName
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithCollection($collection, $callback, $resourceName)
    {
        $resource = new Collection($collection, $callback, $resourceName);
        $rootScope = $this->fractal->createData($resource);

        return $this->respondWithArray($rootScope->toArray());
    }

    /**
     * Respond with an array.
     *
     * @param array $array
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithArray(array $array, array $headers = [])
    {
        return response()->json($array, $this->statusCode, $headers);
    }

    /**
     * Created response.
     *
     * @param string $message
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithCreated($message = 'Created', array $headers = [])
    {
        return $this->setStatusCode(201)->respondWithArray(['message' => $message], $headers);
    }

    /**
     * Error response.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithError($message)
    {
        return $this->respondWithArray([
            'error' => [
                'http_code' => $this->statusCode,
                'message' => $message,
            ],
        ]);
    }

    /**
     * Forbidden error response.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorForbidden($message = 'Forbidden')
    {
        return $this->setStatusCode(403)->respondWithError($message);
    }

    /**
     * Internal server error response.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorInternalError($message = 'Internal Error')
    {
        return $this->setStatusCode(500)->respondWithError($message);
    }

    /**
     * Not found error response.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorNotFound($message = 'Resource Not Found')
    {
        return $this->setStatusCode(404)->respondWithError($message);
    }

    /**
     * Unauthorized error response.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorUnauthorized($message = 'Unauthorized')
    {
        return $this->setStatusCode(401)->respondWithError($message);
    }

    /**
     * Wrong arguments error response.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorWrongArgs($message = 'Wrong Arguments')
    {
        return $this->setStatusCode(400)->respondWithError($message);
    }
}
