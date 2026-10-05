<?php

namespace App\Policies;

use App\Models\Reserve;
use App\Models\User;

class ReservePolicy
{
    /**
     * Um user autenticado pode listar reservas (o controller filtra as dele).
     * O admin também — mas via rota /admin/reserves com indexAdmin.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * O dono vê a sua reserva. Admin vê qualquer.
     */
    public function view(User $user, Reserve $reserve): bool
    {
        return $user->id === $reserve->user_id || $user->isAdmin();
    }

    /**
     * Qualquer user autenticado pode criar reservas.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Atualizar/cancelar: dono ou admin.
     * A regra de "só pending pode ser cancelada" fica no controller,
     * porque é regra de domínio, não de autorização.
     */
    public function update(User $user, Reserve $reserve): bool
    {
        return $user->id === $reserve->user_id || $user->isAdmin();
    }

    public function delete(User $user, Reserve $reserve): bool
    {
        return $user->id === $reserve->user_id || $user->isAdmin();
    }

    public function restore(User $user, Reserve $reserve): bool
    {
        return false;
    }

    public function forceDelete(User $user, Reserve $reserve): bool
    {
        return false;
    }
}
