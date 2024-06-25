<?php

namespace App\Repositories\Admin;

use App\Http\Resources\VOrders\Admin\AdminOrderGridResource;
use App\Order;
use Illuminate\Http\Exceptions\HttpResponseException;
use VklComponents\VklTable\VklTableBuilder;

class AdminOrderRepository
{
    /**
     * Get grid of user Orders
     *
     * @param array $requestData
     * @return mixed
     */
    public function getAdminGrid(array $requestData)
    {
        $builder = Order::with(['service', 'serviceUnit', 'user', 'admin'])
            ->leftJoin('service_units', 'service_units.id', 'orders.service_unit_id')
            ->groupBy('orders.id')
            ->select('orders.*');

        $table = new VklTableBuilder($builder, $requestData, AdminOrderGridResource::class);

        $table->setCustomFilterForColumn('location_id', function ($query, $filterValues) use ($requestData) {
            $query->whereIn('service_units.location_id', $filterValues);
        });

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
        $order = self::find($orderId);

        return $order;
    }

    /**
     * Update Order
     *
     * @param array $requestData
     * @return mixed
     */
    public function updateOrder(array $requestData)
    {
        $order = self::find($requestData['id']);

        $order->update($requestData);

        return $order;
    }

    /**
     * Change order status
     *
     * @param array $requestData (id, status)
     * @return mixed
     */
    public function changeStatus(array $requestData)
    {
        $order = self::find($requestData['id']);

        $order->status = $requestData['status'];
        $order->save();

        return $order;
    }

    /**
     * Delete Order
     * @param int $orderId
     * @return mixed
     */
    public function deleteOrder(int $orderId)
    {
        $order = self::find($orderId);

        return $order->delete();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function find(int $id)
    {
        $entity = Order::find($id);

        if (!$entity) {
            throw new HttpResponseException(response('Not found', 404));
        }

        return $entity;
    }
}