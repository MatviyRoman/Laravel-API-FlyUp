<?php

namespace App\Repositories;

use App\Order;
use App\ServiceUnit;
use Carbon\Carbon;

class OrderRepository
{
    /**
     * @param int $service_id
     * @param int $location_id
     * @param string $start
     * @param string $end
     * @return array
     */
    public function getAvailableServiceUnitsIds(int $service_id, int $location_id, string $start, string $end)
    {
        $start = Carbon::parse($start)->startOfDay();
        $end = Carbon::parse($end)->endOfDay();

        $serviceUnitsIds = $this->getServiceUnitsIds($service_id, $location_id);

        $takenUnitsIds = Order::select('service_unit_id')
            ->where('service_id', $service_id)
            ->where('start', '<=', $end->toDateTimeString())
            ->where('end', '>=', $start->toDateTimeString())
            ->groupBy('service_unit_id')
            ->get()
            ->pluck('service_unit_id');

        return array_diff($serviceUnitsIds->all(), $takenUnitsIds->all());
    }

    /**
     * @param int $service_id
     * @return mixed
     */
    public function getServiceLocations(int $service_id)
    {
        $serviceLocations = ServiceUnit::select('location_id')
            ->where('service_id', $service_id)
            ->groupBy('location_id')
            ->get()
            ->pluck('location_id');

        return $serviceLocations->all();
    }

    /**
     * @param int $service_id
     * @param int $location_id
     * @return mixed
     */
    public function getServiceUnitsIds(int $service_id, int $location_id)
    {
        $serviceUnitsIds = ServiceUnit::where('service_id', $service_id)
            ->where('location_id', $location_id)
            ->get(['id'])
            ->pluck('id');

        return $serviceUnitsIds;
    }

    /**
     * @param array $serviceUnitsIds
     * @param string $start // datetime string
     * @param string $end // datetime string
     * @return mixed
     */
    public function getServiceUnitsOrders(array $serviceUnitsIds, string $start, string $end)
    {
        $orders = Order::whereIn('service_unit_id', $serviceUnitsIds)
            ->where('start', '<=', $end)
            ->where('end', '>=', $start)
            ->get();

        return $orders;
    }

    /**
     * @param array $requestData
     * @return array
     */
    public function getAvailableDates(array $requestData)
    {
        $start = Carbon::parse($requestData['start'])->startOfDay();
        $end = Carbon::parse($requestData['end'])->endOfDay();

        $serviceUnitsIds = $this->getServiceUnitsIds($requestData['service_id'], $requestData['location_id']);

        $serviceUnitsCount = $serviceUnitsIds->count();

        $orders = $this->getServiceUnitsOrders($serviceUnitsIds->all(), $start->toDateTimeString(), $end->toDateTimeString());

        $dayStart = $start->copy();
        $dayEnd = $dayStart->copy()->endOfDay();

        $result = [];

        while ($dayEnd->lte($end)) {
            $ordersCount = $orders->where('start', '<=', $dayEnd->toDateTimeString())
                ->where('end', '>=', $dayStart->toDateTimeString())
                ->count();

            if ($ordersCount < $serviceUnitsCount) {
                array_push($result, $dayStart->toDateString());
            }

            $dayStart->addDay();
            $dayEnd->addDay();
        }

        return $result;
    }


    public function changeStatus(array $requestData)
    {

    }
}