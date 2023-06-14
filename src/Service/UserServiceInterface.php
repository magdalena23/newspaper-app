<?php
/**
 * User Service Interface
 */

namespace App\Service;

use App\Entity\User;

/**
 * Interface User Service.
 */
interface UserServiceInterface
{
    /**
     * Save entity.
     *
     * @param User $user User entity
     */
    public function save(User $user): void;

    /**
     * Delete entity.
     *
     * @param User $user User entity
     */
    public function delete(User $user): void;

}
