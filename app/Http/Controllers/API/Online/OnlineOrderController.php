<?php

namespace App\Http\Controllers\API\Online;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GerServiceAvailabilityRequest;
use App\Http\Requests\Admin\GridOrderRequest;
use App\Http\Requests\Online\CreateOrderRequest;
use App\Http\Requests\Online\GerDiscountRequest;
use App\Http\Resources\VOrders\Online\OnlineOrderResource;
use App\Http\Resources\Online\OnlineDiscountResource;
use App\Repositories\DiscountRepository;
use App\Repositories\Online\OnlineOrderRepository;
use App\Repositories\OrderRepository;

/**
 * Class OnlineOrderController
 * @package App\Http\Controllers\API\Online
 */
class OnlineOrderController extends Controller
{
    /**
     * @var OnlineOrderRepository
     */
    private $orderRepository;

    /**
     * @var OrderRepository
     */
    private $commonOrderRepository;

    /**
     * @var DiscountRepository
     */
    private $discountRepository;

    function __construct(
        OnlineOrderRepository $orderRepository,
        DiscountRepository $discountRepository,
        OrderRepository $commonOrderRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->discountRepository = $discountRepository;
        $this->commonOrderRepository = $commonOrderRepository;
    }

    /**
     * Create new Order
     *
     * @param CreateOrderRequest $request
     * @return CreateOrderRequest
     */
    public function create(CreateOrderRequest $request)
    {
        $requestData = $request->validated();

        $user = \Auth::user();

        $this->orderRepository->createOrder($requestData, $user);

        return response(['status' => 'success'], 200);
    }

    /**
     * Get grid of user Orders
     *
     * @param GridOrderRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getGrid(GridOrderRequest $request)
    {
        $requestData = $request->validated();

        return $this->orderRepository->getGrid($requestData);
    }

    /**
     * Show Order
     *
     * @param int $id
     * @return OnlineOrderResource
     */
    public function show(int $id)
    {
        $order = $this->orderRepository->getOrder($id);

        return new OnlineOrderResource($order);
    }

    /**
     * Show service availability
     *
     * @param GerServiceAvailabilityRequest $request
     * @return array
     */
    public function availability(GerServiceAvailabilityRequest $request)
    {
        $requestData = $request->validated();

        return $this->commonOrderRepository->getAvailableDates($requestData);
    }

    /**
     * Check discount
     *
     * @param GerDiscountRequest $request
     * @return OnlineDiscountResource
     */
    public function checkDiscount(GerDiscountRequest $request)
    {
        $requestData = $request->validated();

        return new OnlineDiscountResource($this->discountRepository->getByCodeForUser($request['code']));
    }
}
