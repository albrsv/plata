<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    public function jsonSuccessResponse(?array $data = null, ?string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    public function arraySuccessResponse(?array $data = null, ?string $message = null): array
    {
        return [
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ];
    }

    public function jsonFailureResponse(?string $message = null, mixed $error = null, int $code = 400, array $headers = []): JsonResponse
    {
        return response()->json([
            'status'  => 'failure',
            'message' => $message,
            'error'   => $error,
        ], $code, $headers);
    }

    public function jsonInvalidLoginCredentialsResponse(): JsonResponse
    {
        return $this->jsonFailureResponse(__('auth.failed'), null, 422);
    }
}
