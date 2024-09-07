<?php

namespace App\Services;

use App\Models\SavedProperty;

class SavedPropertyService
{
    protected $savedpropertyModel;

    public function __construct(SavedProperty $savedpropertyModel)
    {
        $this->savedpropertyModel = $savedpropertyModel;
    }

    public function list()
    {
        return $this->savedpropertyModel->with('tenant','houseOwner','flat')->whereNull('deleted_at');
    }

    public function all()
    {
        return $this->savedpropertyModel->with('tenant','houseOwner','flat')->whereNull('deleted_at')->all();
    }

    public function find($id)
    {
        return $this->savedpropertyModel->with('tenant','houseOwner','flat')->find($id);
    }

    public function create(array $data)
    {
        return $this->savedpropertyModel->create($data);
    }

    public function update(array $data, $id)
    {
        $dataInfo = $this->savedpropertyModel->findOrFail($id);

        $dataInfo->update($data);

        return $dataInfo;
    }

    public function delete($id)
    {
        $dataInfo = $this->savedpropertyModel->find($id);

        if (!empty($dataInfo)) {

            $dataInfo->deleted_at = date('Y-m-d H:i:s');

            $dataInfo->status = 'Deleted';

            return ($dataInfo->save());
        }
        return false;
    }

    public function changeStatus($request)
    {
        $dataInfo = $this->savedpropertyModel->findOrFail($request->id);

        $dataInfo->update(['status' => $request->status]);

        return $dataInfo;
    }

    public function activeList()
    {
        return $this->savedpropertyModel->with('tenant','houseOwner','flat')->whereNull('deleted_at')->where('status', 'Active')->get();
    }

}
