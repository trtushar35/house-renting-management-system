<?php

namespace App\Services;

use App\Models\Tenant;

class TenantService
{
    protected $tenantModel;

    public function __construct(Tenant $tenantModel)
    {
        $this->tenantModel = $tenantModel;
    }

    public function list()
    {
        return $this->tenantModel->whereNull('deleted_at');
    }

    public function all()
    {
        return $this->tenantModel->whereNull('deleted_at')->all();
    }

    public function find($id)
    {
        return $this->tenantModel->find($id);
    }

    public function create(array $data)
    {
        return $this->tenantModel->create($data);
    }

    public function update(array $data, $id)
    {
        $dataInfo = $this->tenantModel->findOrFail($id);

        $dataInfo->update($data);

        return $dataInfo;
    }

    public function delete($id)
    {
        $dataInfo = $this->tenantModel->find($id);

        if (!empty($dataInfo)) {

            $dataInfo->deleted_at = date('Y-m-d H:i:s');

            $dataInfo->status = 'Deleted';

            return ($dataInfo->save());
        }
        return false;
    }

    public function changeStatus($request)
    {
        $dataInfo = $this->tenantModel->findOrFail($request->id);

        $dataInfo->update(['status' => $request->status]);

        return $dataInfo;
    }

    public function activeList()
    {
        return $this->tenantModel->whereNull('deleted_at')->where('status', 'Active')->get();
    }

}
