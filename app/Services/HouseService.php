<?php

namespace App\Services;

use App\Models\House;

class HouseService
{
    protected $houseModel;

    public function __construct(House $houseModel)
    {
        $this->houseModel = $houseModel;
    }

    public function list()
    {
        return $this->houseModel->with('houseOwner')->whereNull('deleted_at');
    }

    public function all()
    {
        return $this->houseModel->with('houseOwner')->whereNull('deleted_at')->all();
    }

    public function find($id)
    {
        return $this->houseModel->with('houseOwner')->find($id);
    }

    public function create(array $data)
    {
        return $this->houseModel->create($data);
    }

    public function update(array $data, $id)
    {
        $dataInfo = $this->houseModel->findOrFail($id);

        $dataInfo->update($data);

        return $dataInfo;
    }

    public function delete($id)
    {
        $dataInfo = $this->houseModel->find($id);

        if (!empty($dataInfo)) {

            $dataInfo->deleted_at = date('Y-m-d H:i:s');

            $dataInfo->status = 'Deleted';

            return ($dataInfo->save());
        }
        return false;
    }

    public function changeStatus($request)
    {
        $dataInfo = $this->houseModel->findOrFail($request->id);

        $dataInfo->update(['status' => $request->status]);

        return $dataInfo;
    }

    public function activeList()
    {
        return $this->houseModel->with('houseOwner')->whereNull('deleted_at')->where('status', 'Active')->get();
    }

    public function houseAddressByHouseId($id)
    {
        $houseDetails = $this->houseModel->find($id);
        return $houseDetails->address ?? '';
    }

}
