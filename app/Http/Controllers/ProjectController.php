<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Project;
use App\Models\ProjectSupervisor;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type');
        $natureTagSlug = $request->get('nature');
        $sectionTagSlug = $request->get('section');
        $focusTagSlug = $request->get('focus');
        $supervisorSlug = $request->get('supervisor');
        $withCompany = $request->get('with_company');
        $groupId = $request->get('group');
        $supervisorName = null;

        $query = Project::with(['supervisors', 'tags', 'owner', 'organization', 'types'])
            ->available();

        if ($type) {
            $query->whereHas('types', function ($q) use ($type) {
                $q->where('project_types.slug', $type);
            });
        }

        if ($natureTagSlug) {
            $query->whereHas('tags', function ($q) use ($natureTagSlug) {
                $q->where('tags.slug', $natureTagSlug)
                    ->where('tags.category', TagCategory::Nature->value);
            });
        }

        if ($sectionTagSlug) {
            $query->whereHas('tags', function ($q) use ($sectionTagSlug) {
                $q->where('tags.slug', $sectionTagSlug)
                    ->where('tags.category', TagCategory::Group->value);
            });
        }

        if ($focusTagSlug) {
            $query->whereHas('tags', function ($q) use ($focusTagSlug) {
                $q->where('tags.slug', $focusTagSlug)
                    ->where('tags.category', TagCategory::Focus->value);
            });
        }

        if ($supervisorSlug) {
            // Supervisor is a polymorphic relationship, so we need to get the supervisor by the slug via projectSupervisor->supervisor
            $supervisor = ProjectSupervisor::with(['supervisor'])->whereHas('supervisor', function ($q) use ($supervisorSlug) {
                $q->where('slug', $supervisorSlug);
            })->first();
            if ($supervisor) {
                $query->whereHas('supervisorLinks', function ($q) use ($supervisor) {
                    $q->where('project_supervisor.id', $supervisor->id);
                });
                $supervisorName = $supervisor->supervisor->name;
            }
        }

        if ($withCompany !== null) {
            if ($withCompany === 'yes') {
                $query->whereNotNull('organization_id');
            } elseif ($withCompany === 'no') {
                $query->whereNull('organization_id');
            }
        }

        if ($groupId) {
            $query->whereHas('supervisorLinks', function ($q) use ($groupId) {
                $q->where('supervisor_type', User::class)
                    ->whereIn('supervisor_id', function ($subQ) use ($groupId) {
                        $subQ->select('id')
                            ->from('users')
                            ->where('group_id', $groupId);
                    });
            });
        }

        $projects = $query->latest()->paginate(12);

        // Get tags for filters
        $natureTags = Tag::where('category', TagCategory::Nature->value)
            ->orderBy('name')
            ->get();

        $sectionTags = Tag::where('category', TagCategory::Group->value)
            ->orderBy('name')
            ->get();

        $focusTags = Tag::where('category', TagCategory::Focus->value)
            ->orderBy('name')
            ->get();

        $groups = Group::with('section')
            ->orderBy('name')
            ->get();

        $supervisors = ProjectSupervisor::with(['supervisor'])->get();
        $supervisors = $supervisors->map(function ($supervisor) {
            return [
                'id' => $supervisor->id,
                'name' => $supervisor->supervisor->name,
                'type' => $supervisor->supervisor_type,
            ];
        });

        return view('projects.index', [
            'projects' => $projects,
            'selectedType' => $type,
            'natureTags' => $natureTags,
            'sectionTags' => $sectionTags,
            'focusTags' => $focusTags,
            'groups' => $groups,
            'supervisors' => $supervisors,
            'selectedNature' => $natureTagSlug,
            'selectedSection' => $sectionTagSlug,
            'selectedFocus' => $focusTagSlug,
            'selectedSupervisor' => $supervisorSlug,
            'selectedSupervisorName' => $supervisorName,
            'selectedWithCompany' => $withCompany,
            'selectedGroup' => $groupId,
        ]);
    }

    public function past()
    {
        $projects = Project::with(['supervisors', 'tags', 'owner'])
            ->past()
            ->latest()
            ->paginate(12);

        return view('projects.past', [
            'projects' => $projects,
        ]);
    }

    public function show(Project $project)
    {
        $project->load([
            'supervisors.group.section',
            'tags',
            'owner.group.section',
            'organization',
            'types'
        ]);

        return view('projects.show', [
            'project' => $project,
        ]);
    }
}
