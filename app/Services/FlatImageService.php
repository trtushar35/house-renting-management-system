<?php

namespace App\Services;

use App\Models\FlatImage;

class FlatImageService
{
    protected $flatimageModel;

    public function __construct(FlatImage $flatimageModel)
    {
        $this->flatimageModel = $flatimageModel;
    }

    public function list()
    {
        return $this->flatimageModel->with('flat')->whereNull('deleted_at');
    }

    public function all()
    {
        return $this->flatimageModel->with('flat')->whereNull('deleted_at')->all();
    }

    public function find($id)
    {
        return $this->flatimageModel->with('flat')->find($id);
    }

    public function create(array $data)
    {
        return $this->flatimageModel->create($data);
    }

    public function update(array $data, $id)
    {
        $dataInfo = $this->flatimageModel->findOrFail($id);

        $dataInfo->update($data);

        return $dataInfo;
    }

    public function delete($id)
    {
        $dataInfo = $this->flatimageModel->find($id);

        if (!empty($dataInfo)) {

            $dataInfo->deleted_at = date('Y-m-d H:i:s');

            $dataInfo->status = 'Deleted';

            return ($dataInfo->save());
        }
        return false;
    }

    public function changeStatus($request)
    {
        $dataInfo = $this->flatimageModel->findOrFail($request->id);

        $dataInfo->update(['status' => $request->status]);

        return $dataInfo;
    }

    public function activeList()
    {
        return $this->flatimageModel->with('flat')->whereNull('deleted_at')->where('status', 'Active')->get();
    }

}
