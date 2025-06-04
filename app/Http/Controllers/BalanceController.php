<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\BalanceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BalanceController extends Controller
{
    public function index(Request $request): JsonResource
    {
        $balances = $request->user()->balances()->get();

        return BalanceResource::collection($balances);
    }
}
