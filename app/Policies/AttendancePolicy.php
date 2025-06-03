<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttendancePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; // Tous les utilisateurs authentifiés peuvent voir la liste
    }

    public function view(User $user, Attendance $attendance)
    {
        return $user->id === $attendance->student_id || // L'étudiant peut voir ses propres présences
               $user->hasRole(['admin', 'professor']); // Les admins et profs peuvent voir toutes les présences
    }

    public function create(User $user)
    {
        return $user->hasRole(['admin', 'professor']); // Seuls les admins et profs peuvent créer
    }

    public function update(User $user, Attendance $attendance)
    {
        return $user->hasRole(['admin', 'professor']); // Seuls les admins et profs peuvent modifier
    }

    public function delete(User $user, Attendance $attendance)
    {
        return $user->hasRole(['admin', 'professor']); // Seuls les admins et profs peuvent supprimer
    }

    public function bulkStore(User $user)
    {
        return $user->hasRole(['admin', 'professor']); // Seuls les admins et profs peuvent faire des ajouts en masse
    }
} 