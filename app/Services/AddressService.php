<?php

namespace App\Services;

use App\Repositories\Interfaces\AddressRepositoryInterface;

class AddressService
{
    protected $addressRepo;

    public function __construct(AddressRepositoryInterface $addressRepo)
    {
        $this->addressRepo = $addressRepo;
    }

    public function getUserAddresses($userId)
    {
        return $this->addressRepo->getByUserId($userId);
    }

    public function createAddress(array $data)
    {
        return $this->addressRepo->create($data);
    }

    public function updateAddress($id, array $data)
    {
        return $this->addressRepo->update($id, $data);
    }

    public function deleteAddress($id)
    {
        return $this->addressRepo->delete($id);
    }
}