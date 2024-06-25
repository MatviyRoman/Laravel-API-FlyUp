<?php

namespace App\Repositories\Online;

use App\Discount;
use App\Http\Resources\VOrders\Online\OnlineOrderGridResource;
use App\Order;
use App\Repositories\DiscountRepository;
use App\Repositories\OrderRepository;
use App\Service;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use VklComponents\VklTable\VklTableBuilder;

class OnlineOrderRepository
{
    /**
     * @var OrderRepository
     */
    private $commonOrderRepository;

    /**
     * @var DiscountRepository
     */
    private $discountRepository;

    function __construct(
        OrderRepository $commonOrderRepository,
        DiscountRepository $discountRepository
    ) {
        $this->commonOrderRepository = $commonOrderRepository;
        $this->discountRepository = $discountRepository;
    }

    /**
     * Create new order
     *
     * @param array $requestData
     * @param null $user
     * @return mixed
     */
    public function createOrder(array $requestData, $user = null)
    {
        if ($user) {
            $requestData['user_id'] = $user->id;
            $requestData['first_name'] = $user->first_name;
            $requestData['last_name'] = $user->last_name;
            $requestData['email'] = $user->email;
            $requestData['phone'] = $user->phone;
        }

        $availableServiceUnitsIds = $this->commonOrderRepository->getServiceUnitsIds($requestData['service_id'], $requestData['location_id'])->all();

        if ($availableServiceUnitsIds) {
            $requestData['service_unit_id'] = $availableServiceUnitsIds[array_rand($availableServiceUnitsIds)];
        } else {
            throw new HttpResponseException(response('Service is not available', 400));
        }

        // set prices

        $start = Carbon::parse($requestData['start'])->startOfDay();
        $end = Carbon::parse($requestData['end'])->endOfDay();

        $service = Service::find($requestData['service_id']);

        $days = $start->diffInDays($end) + 1;

        if ($days >= 3 && $days < 7) {
            $price = $service->price2;
        } elseif ($days >= 7) {
            $price = $service->price3;
        } else {
            $price = $service->price;
        }

        $requestData['origin_price'] = $days * $price;

        // check if discount code requested
        if (array_key_exists('discount_code', $requestData)) {
            $discount = $this->discountRepository->getByCodeForUser($requestData['discount_code']);

            // get price with discount
            if ($discount->type === Discount::TYPE_PERCENT) {
                $requestData['price'] = $requestData['origin_price'] - ($requestData['origin_price'] * ($discount->percent / 100));
            } else {
                $requestData['price'] = $requestData['origin_price'] - $discount->value;
            }

            if ($requestData['price'] < 0) {
                $requestData['price'] = 0;
            }
        } else {
            $requestData['price'] = $requestData['origin_price'];
        }

        $order = Order::create($requestData);

        $order->status = Order::STATUS_NEW;
        $order->save();

        return $order;
    }

    /**
     * Get grid of user Orders
     *
     * @param array $requestData
     * @return mixed
     */
    public function getGrid(array $requestData)
    {
        $userId = Auth::id();
        $builder = Order::with(['service'])
            ->where('user_id', $userId);

        $table = new VklTableBuilder($builder, $requestData, OnlineOrderGridResource::class);

        $table->setSearchableColumns(['first_name', 'last_name', 'email', 'phone']);

        return $table->resolve();
    }

    /**
     * Get Order
     *
     * @param int $orderId
     * @return mixed
     */
    public function getOrder(int $orderId)
    {
        $userId = Auth::id();
        $order = Order::where('id', $orderId)
            ->where('user_id', $userId)
            ->first();

        if (!$order) {
            throw new HttpResponseException(response('Not found', 404));
        }

        return $order;
    }
}