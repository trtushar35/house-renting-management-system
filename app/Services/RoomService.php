<?php

namespace App\Services;

use App\Models\Room;

class RoomService
{
    protected $roomModel;

    public function __construct(Room $roomModel)
    {
        $this->roomModel = $roomModel;
    }

    public function list()
    {
        return $this->roomModel->with('house')->whereNull('deleted_at');
    }

    public function all()
    {
        return $this->roomModel->with('house')->whereNull('deleted_at')->all();
    }

    public function find($id)
    {
        return $this->roomModel->with('house')->find($id);
    }

    public function create(array $data)
    {
        return $this->roomModel->create($data);
    }

    public function update(array $data, $id)
    {
        $dataInfo = $this->roomModel->findOrFail($id);

        $dataInfo->update($data);

        return $dataInfo;
    }

    public function delete($id)
    {
        $dataInfo = $this->roomModel->find($id);

        if (!empty($dataInfo)) {

            $dataInfo->deleted_at = date('Y-m-d H:i:s');

            $dataInfo->status = 'Deleted';

            return ($dataInfo->save());
        }
        return false;
    }

    public function changeStatus($request)
    {
        $dataInfo = $this->roomModel->findOrFail($request->id);

        $dataInfo->update(['status' => $request->status]);

        return $dataInfo;
    }

    public function activeList()
    {
        return $this->roomModel->with('house')->whereNull('deleted_at')->where('status', 'Active')->get();
    }

    public function bookingPriceByRoomId($room_id)
    {
        $booking = $this->roomModel->find($room_id);
        // dd($booking->rent);
        return $booking->rent ?? 0;
    }

}
