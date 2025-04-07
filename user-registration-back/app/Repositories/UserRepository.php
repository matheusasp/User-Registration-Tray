<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{
    /**
     * Find user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Find user by ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Get filtered users with pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getFilteredUsers(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = User::query();
        

        if (!empty($filters['name'])) {
            $query->where('name', 'LIKE', "%{$filters['name']}%");
        }
        

        if (!empty($filters['cpf'])) {

            $cpf = preg_replace('/[^0-9]/', '', $filters['cpf']);
            $query->where('cpf', 'LIKE', "%{$cpf}%");
        }
        
        return $query->paginate($perPage);
    }

    /**
     * Create or update user.
     *
     * @param array $data
     * @param User|null $user
     * @return User
     */
    public function createOrUpdate(array $data, ?User $user = null): User
    {

        if ($user) {
            $user->update($data);
            return $user;
        }
        
        return User::create($data);
    }

    /**
     * Get all users.
     *
     * @return Collection
     */
    public function getAllUsers(): Collection
    {
        return User::all();
    }
}