<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Helpers\ApiResponse;
use App\Http\Requests\CallbackTransactionRequest;
use App\Http\Requests\CreateTransactionRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Balance;
use App\Models\Currency;
use App\Models\Payway;
use App\Models\PaywayCurrency;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Token;

class ApiController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::query()->create($request->all());
        $currencies = Currency::query()->get();
        foreach ($currencies as $currency) {
            Balance::query()->create([
                'currency_id' => $currency->id,
                'user_id' => $user->id
            ]);
        }

        return ApiResponse::sendResponse(['id' => $user->id]);
    }

    public function login(LoginRequest $request): JsonResponse
    {

        if (! $token = auth()->attempt($request->all())) {
            Log::error('user invalid');
            throw new ApiException(
                errorCode: 401,
                httpCode: 401
            );
        }

        if (! auth()->user()->is_active) {
            throw new ApiException(
                401,
                ['message' => 'User is blocked'],
                401
            );
        }

        return ApiResponse::sendResponse($this->respondWithToken($token));
    }

    public function getCurrencyList(): JsonResponse
    {
        $items = Currency::query()
            ->select([
                'id',
                'name',
                'is_active',
                'is_crypto',
                'precision',
            ])
            ->get();
        return ApiResponse::sendResponse([
            'items' => $items,
            'total' => $items->count()
        ]);
    }

    public function getPaywayList(): JsonResponse
    {
        $items = Payway::query()
            ->select([
                'id',
                'name',
                'is_active',
                'limit'
            ])
            ->get();
        return ApiResponse::sendResponse([
            'items' => $items,
            'total' => $items->count()
        ]);
    }

    public function getBalances(): JsonResponse
    {
        if (! Gate::allows('is-admin')) {
            throw new ApiException(
                errorCode: 403,
                httpCode: 403
            );
        }
        $users = User::query()
            ->select(['id', 'name'])
            ->get();

        $items = [];
        foreach ($users as $user) {
            $items[] = [
                'id' => $user->id,
                'name' => $user->name,
                'balances' => Balance::query()
                    ->where('user_id', $user->id)
                    ->join('currencies','balances.currency_id', '=', 'currencies.id')
                    ->select([
                        'balances.currency_id',
                        'balances.value',
                        'currencies.name as currency_name'
                    ])
                    ->get()
            ];
        }
        return ApiResponse::sendResponse([
            'items' => $items,
            'total' => $users->count()
        ]);
    }

    public function getUserBalance(): JsonResponse
    {
        $items = Balance::query()
            ->where('user_id', auth()->user()->id)
            ->join('currencies','balances.currency_id', '=', 'currencies.id')
            ->select([
                'balances.currency_id',
                'balances.value',
                'currencies.name as currency_name'
            ])
            ->get();
        return ApiResponse::sendResponse([
            'items' => $items,
            'total' => $items->count()
        ]);
    }

    public function getPaywayCurrencyList(): JsonResponse
    {
        $items = PaywayCurrency::query()
            ->join('payways','payway_currencies.payway_id', '=', 'payways.id')
            ->join('currencies','payway_currencies.currency_id', '=', 'currencies.id')
            ->select([
                'payway_currencies.id',
                'payway_currencies.currency_id',
                'payway_currencies.payway_id',
                'payway_currencies.is_active',
                'payway_currencies.max',
                'payway_currencies.min',
                'payway_currencies.fee',
                'payways.name as payway_name',
                'currencies.name as currency_name'
            ])
            ->get();
        return ApiResponse::sendResponse([
            'items' => $items,
            'total' => $items->count()
        ]);
    }

    public function updateUser(int $id, UpdateUserRequest $request): JsonResponse
    {
        if (! Gate::allows('is-admin') || $id === auth()->user()->id) {
            throw new ApiException(
                errorCode: 403,
                httpCode: 403
            );
        }
        $user = User::query()->find($id);
        if (! $user) {
            throw new ApiException(
                errorCode: 404,
                httpCode: 404
            );
        }
        $user->update(['is_active' => $request->is_active]);
        return ApiResponse::sendResponse([
            'item' => [
                'id' => $user->id,
                'is_active' => $user->is_active,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function deleteUser(int $id): JsonResponse
    {
        if (! Gate::allows('is-admin') || $id === auth()->user()->id) {
            throw new ApiException(
                errorCode: 403,
                httpCode: 403
            );
        }
        $user = User::query()->find($id);
        if (! $user) {
            throw new ApiException(
                errorCode: 404,
                httpCode: 404
            );
        }
        Balance::query()->where('user_id', $id)->delete();
        return ApiResponse::sendResponse([
            'result' => $user->delete()
        ]);
    }

    public function createTransaction(CreateTransactionRequest $request): JsonResponse
    {
        $pwc = PaywayCurrency::query()->find($request->get('payway_currency_id'));

        //todo:check user balance

        $dayLimit = Transaction::query()
            ->where('user_id', auth()->user()->id)
            ->where('payway_currency_id', $request->get('payway_currency_id'))
            ->whereDate('created_at', Carbon::now()->toDateString())
            ->selectRaw('SUM(sum) as sum')
            ->value('sum');

        if ($dayLimit + $request->get('sum') > $pwc->payway->limit) {
            throw new ApiException(
                422,
                ['message' => 'Day limit increased'],
                422
            );
        }
        $trx = Transaction::query()
            ->create([
                'is_out' => $request->get('is_out'),
                'sum' => $request->get('sum'),
                'status' => Transaction::STATUS_PENDING,
                'fee' => $pwc->fee,
                'user_id' => auth()->user()->id,
                'payway_currency_id' => $request->get('payway_currency_id')
            ]);

        return ApiResponse::sendResponse([
            'item' => $trx
        ]);
    }

    public function logout(): JsonResponse
    {
        try {
            auth()->logout(true);
        } catch (\Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                // Token is Invalid
                throw new ApiException(errorCode: 401, httpCode: 401);
            } elseif ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                // Token is Expired(need login)
                throw new ApiException(errorCode: 401,httpCode: 401);
            } else {
                throw new ApiException(errorCode: 404, httpCode: 404);
            }
        }
        return ApiResponse::sendResponse([
            'result' => true
        ]);
    }
    public function callback(CallbackTransactionRequest $request): JsonResponse
    {
        $status = $request->get('status');
        $trx = Transaction::query()->find($request->get('id'));

        if ($status === Transaction::STATUS_SUCCESS) {
            $balance = Balance::query()
                ->where('user_id', $trx->user_id)
                ->where('currency_id', $trx->paywayCurrency->currency_id)
                ->first();

            $calcBalance = $balance->value;
            if ($trx->is_out) {
                $calcBalance -= $trx->sum;
            } else {
                $calcBalance += $trx->sum;
            }
            $balance->update([
                'value' => $calcBalance
            ]);
        }
        $trx->update([
            'status' => $status
        ]);
        return ApiResponse::sendResponse([
            'item' => $trx
        ]);
    }
    /**
     * Get the token .
     * @param  string $token
     *
     * @return array
     */
    protected function respondWithToken(string $token): array
    {
        $parseToken = JWTAuth::decode(new Token($token));
        $createTime = Carbon::parse($parseToken->get('nbf'));

        return [
            'auth_token' => $token,
            'create_at' => $createTime,
            'expire_at' => Carbon::parse($parseToken->get('exp'))
        ];
    }
}
