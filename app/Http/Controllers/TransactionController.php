<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request): JsonResource
    {
        $user = $request->user();

        $query = DB::table('transactions')
            ->select('transactions.*')
            ->where(function ($query) use ($user) {
                $query->whereIn('from_balance_id', function ($q) use ($user) {
                    $q->select('id')
                        ->from('balances')
                        ->where('user_id', $user->id);
                })
                    ->orWhereIn('to_balance_id', function ($q) use ($user) {
                        $q->select('id')
                            ->from('balances')
                            ->where('user_id', $user->id);
                    });
            });

        if ($searchTerm = $request->get('search')) {
            $formattedTerm = '+' . $searchTerm . '*';
            $query->whereRaw('MATCH(comment) AGAINST(? IN BOOLEAN MODE)', [$formattedTerm]);
        }

        $sort = $request->get('sort', '-created_at');
        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';

        $transactions = $query
            ->orderBy('created_at', $direction)
            ->paginate(50);

        return TransactionResource::collection($transactions);
    }

    public function getRecent(Request $request): JsonResource
    {
        $transactions = $request->user()
            ->transactions()
            ->latest()
            ->limit(5)
            ->get();

        return TransactionResource::collection($transactions);
    }
}
