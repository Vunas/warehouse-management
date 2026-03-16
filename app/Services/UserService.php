<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $userRepo;

    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function getAllUsers()
    {
        return $this->userRepo->all(['*'], ['roles']);
    }

    public function getPaginatedUsers($perPage = 15, array $filters = [], $sort = 'id', $dir = 'desc')
    {
        return $this->userRepo->paginate(
            $perPage,
            ['*'],
            ['roles'],
            $filters,
            $sort,
            $dir
        );
    }

    public function getUserById($id)
    {
        return $this->userRepo->findById($id, ['*'], ['roles']);
    }

    public function createUser(array $data)
    {
        // Business Logic: Mã hóa mật khẩu trước khi lưu
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->userRepo->create($data);
    }

    public function updateUser($id, array $data)
    {
        // Business Logic: Nếu có cập nhật mật khẩu thì mã hóa, không thì bỏ qua
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $this->userRepo->update($id, $data);
    }

    public function softDeleteUser($id)
    {
        return $this->userRepo->softDelete($id);
    }

    public function restoreUser($id)
    {
        return $this->userRepo->restore($id);
    }
}
