<?php

namespace App\Services;

use App\Models\Booking;

class BookingService
{
    protected $bookingModel;

    public function __construct(Booking $bookingModel)
    {
        $this->bookingModel = $bookingModel;
    }

    public function list()
    {
        return $this->bookingModel->with('flat', 'room', 'tenant')->whereNull('deleted_at');
    }

    public function all()
    {
        return $this->bookingModel->with('flat', 'room', 'tenant')->whereNull('deleted_at')->all();
    }

    public function find($id)
    {
        return $this->bookingModel->with('flat', 'room', 'tenant')->find($id);
    }

    public function create(array $data)
    {
        return $this->bookingModel->create($data);
    }

    public function update(array $data, $id)
    {
        $dataInfo = $this->bookingModel->findOrFail($id);

        $dataInfo->update($data);

        return $dataInfo;
    }

    public function delete($id)
    {
        $dataInfo = $this->bookingModel->find($id);

        if (!empty($dataInfo)) {

            $dataInfo->deleted_at = date('Y-m-d H:i:s');

            $dataInfo->status = 'Deleted';

            return ($dataInfo->save());
        }
        return false;
    }

    public function changeStatus($request)
    {
        $dataInfo = $this->bookingModel->findOrFail($request->id);

        $dataInfo->update(['status' => $request->status]);

        return $dataInfo;
    }

    public function activeList()
    {
        return $this->bookingModel->with(['tenant', 'flat.house.houseOwner'])
            ->whereNull('deleted_at')
            ->where('status', 'Active')
            ->get();
    }

    public function bookingPriceByBookingId($booking_id)
    {
        $booking = $this->bookingModel->find($booking_id);
        return $booking->rent ?? 0;
    }
}
