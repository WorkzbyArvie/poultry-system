<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ClientRequest;

class ClientRequestPolicy
{
    /**
     * Only superadmin can approve client requests.
     */
    public function approve(User $user, ClientRequest $clientRequest): bool
    {
        return $user->role === 'superadmin';
    }

    /**
     * Only superadmin can reject client requests.
     */
    public function reject(User $user, ClientRequest $clientRequest): bool
    {
        return $user->role === 'superadmin';
    }

    /**
     * Only superadmin can view verifications.
     */
    public function viewVerifications(User $user): bool
    {
        return $user->role === 'superadmin';
    }
}
