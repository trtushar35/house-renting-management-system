<?php

namespace App\Services;

use App\Models\Flat;
use Termwind\Components\Dd;

class FlatService
{
    protected $flatModel;

    public function __construct(Flat $flatModel)
    {
        $this->flatModel = $flatModel;
    }

    public function list()
    {
        return $this->flatModel->with('house')->whereNull('deleted_at');
    }

    public function all()
    {
        return $this->flatModel->with('house')->whereNull('deleted_at')->all();
    }

    public function find($id)
    {
        return $this->flatModel->with('house')->find($id);
    }

    public function create(array $data)
    {
        return $this->flatModel->create($data);
    }

    public function update(array $data, $id)
    {
        $dataInfo = $this->flatModel->findOrFail($id);

        $dataInfo->update($data);

        return $dataInfo;
    }

    public function delete($id)
    {
        $dataInfo = $this->flatModel->find($id);

        if (!empty($dataInfo)) {

            $dataInfo->deleted_at = date('Y-m-d H:i:s');

            $dataInfo->status = 'Deleted';

            return ($dataInfo->save());
        }
        return false;
    }

    public function changeStatus($request)
    {
        $dataInfo = $this->flatModel->findOrFail($request->id);

        $dataInfo->update(['status' => $request->status]);

        return $dataInfo;
    }

    public function activeList()
    {
        return $this->flatModel->with('house')->whereNull('deleted_at')->where('status', 'Active')->get();
    }

    public function availableFlatList()
    {
        return $this->flatModel->where('availability', 1)->get();
    }

    public function bookingPriceByFlatId($flat_id)
    {
        $booking = $this->flatModel->find($flat_id);
        return $booking->rent ?? 0;
    }

}
