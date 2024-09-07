<?php

namespace App\Services;

use App\Models\Payment;

class PaymentService
{
    protected $paymentModel;

    public function __construct(Payment $paymentModel)
    {
        $this->paymentModel = $paymentModel;
    }

    public function list()
    {
        return $this->paymentModel->with(['booking.tenant', 'booking.flat.house.houseOwner'])
            ->whereNull('deleted_at');
    }

    public function all()
    {
        return $this->paymentModel->with(['booking.tenant', 'booking.flat.house.houseOwner'])
            ->whereNull('deleted_at')->get();
    }

    public function find($id)
    {
        return $this->paymentModel->with(['booking.tenant', 'booking.flat.house.houseOwner'])->find($id);
    }

    public function create(array $data)
    {
        return $this->paymentModel->create($data);
    }

    public function update(array $data, $id)
    {
        $dataInfo = $this->paymentModel->findOrFail($id);

        $dataInfo->update($data);

        return $dataInfo;
    }

    public function delete($id)
    {
        $dataInfo = $this->paymentModel->find($id);

        if (!empty($dataInfo)) {

            $dataInfo->deleted_at = date('Y-m-d H:i:s');

            $dataInfo->status = 'Deleted';

            return ($dataInfo->save());
        }
        return false;
    }

    public function changeStatus($request)
    {
        $dataInfo = $this->paymentModel->findOrFail($request->id);

        $dataInfo->update(['status' => $request->status]);

        return $dataInfo;
    }

    public function activeList()
    {
        return $this->paymentModel->with('booking')->whereNull('deleted_at')->where('status', 'Active')->get();
    }

}
