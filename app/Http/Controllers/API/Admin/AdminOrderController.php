<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChangeOrderStatusRequest;
use App\Http\Requests\Admin\GerServiceAvailabilityRequest;
use App\Http\Requests\Admin\GridOrderRequest;
use App\Http\Requests\Admin\UpdateOrderRequest;
use App\Http\Requests\Admin\CreateOrderRequest;
use App\Http\Resources\VOrders\Admin\AdminOrderResource;
use App\Repositories\Admin\AdminOrderRepository;
use App\Repositories\Online\OnlineOrderRepository;
use App\Repositories\OrderRepository;

/**
 * Class AdminOrderController
 * @package App\Http\Controllers\API\Orders\Admin
 */
class AdminOrderController extends Controller
{
    /**
     * @var AdminOrderRepository
     */
    private $orderRepository;

    /**
     * @var OrderRepository
     */
    private $commonOrderRepository;

    /**
     * @var OnlineOrderRepository
     */
    private $onlineOrderRepository;

    function __construct(
        AdminOrderRepository $orderRepository,
        OnlineOrderRepository $onlineOrderRepository,
        OrderRepository $commonOrderRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->onlineOrderRepository = $onlineOrderRepository;
        $this->commonOrderRepository = $commonOrderRepository;
    }

    /**
     * Create new Order
     *
     * @param CreateOrderRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Symfony\Component\HttpFoundation\Response
     */
    public function create(CreateOrderRequest $request)
    {
        $requestData = $request->validated();

        $order = $this->onlineOrderRepository->createOrder($requestData);

        return response(['id' => $order->id]);
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

        return $this->orderRepository->getAdminGrid($requestData);
    }

    /**
     * Show Order
     *
     * @param int $id
     * @return AdminOrderResource
     */
    public function show(int $id)
    {
        $order = $this->orderRepository->getOrder($id);

        return new AdminOrderResource($order);
    }

    /**
     * Update Order
     *
     * @param UpdateOrderRequest $request
     * @return AdminOrderResource
     */
    public function update(UpdateOrderRequest $request)
    {
        $requestData = $request->validated();

        $order = $this->orderRepository->updateOrder($requestData);

        return new AdminOrderResource($order);
    }

    /**
     * @param int $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function delete(int $id)
    {
        $this->orderRepository->deleteOrder($id);

        return response(['status' => 'success'], 200);
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
     * Change order status
     *
     * @param ChangeOrderStatusRequest $request
     * @return AdminOrderResource
     */
    public function changeStatus(ChangeOrderStatusRequest $request)
    {
        $requestData = $request->validated();

        return new AdminOrderResource($this->orderRepository->changeStatus($requestData));
    }
}
