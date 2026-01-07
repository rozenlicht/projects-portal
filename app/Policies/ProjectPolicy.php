<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Administrator', 'Staff member - supervisor', 'Researcher']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        return $user->hasAnyRole(['Administrator', 'Staff member - supervisor', 'Researcher']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Administrator', 'Staff member - supervisor', 'Researcher']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        // Administrators can update all projects
        if ($user->hasRole('Administrator')) {
            return true;
        }

        // Researchers can update projects owned by their group leader or projects they supervise
        if ($user->hasRole('Researcher')) {
            // Check if project is owned by their group leader
            $groupLeaderId = $user->group?->group_leader_id;
            if ($groupLeaderId && $project->project_owner_id === $groupLeaderId) {
                return true;
            }
            
            // Check if user is a supervisor of the project
            return $project->supervisors->contains($user->id);
        }

        // Staff member - supervisors can update their own projects or projects with supervisors in groups they lead
        if ($user->hasRole('Staff member - supervisor')) {
            // Check if they own the project
            if ($project->project_owner_id === $user->id) {
                return true;
            }
            
            // Check if at least one supervisor is in a group they lead
            $groupsLedByUser = \App\Models\Group::where('group_leader_id', $user->id)->pluck('id');
            if ($groupsLedByUser->isNotEmpty()) {
                // Load supervisors and check if any are in groups led by this user
                $project->load('supervisorLinks.supervisor.group');
                foreach ($project->supervisorLinks as $supervisorLink) {
                    $supervisor = $supervisorLink->supervisor;
                    // Only check User supervisors (not external supervisors)
                    if ($supervisor instanceof User && $supervisor->group_id && $groupsLedByUser->contains($supervisor->group_id)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        // Only administrators can delete projects
        return $user->hasRole('Administrator');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Project $project): bool
    {
        return $user->hasRole('Administrator');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        return $user->hasRole('Administrator');
    }
}
