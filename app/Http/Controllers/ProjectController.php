<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Tag;
use App\Models\TagCategory;
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
        
        $query = Project::with(['supervisors', 'tags', 'owner', 'organization'])
            ->available();

        if ($type && in_array($type, ['internship', 'bachelor_thesis', 'master_thesis'])) {
            $query->where('type', $type);
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
            $query->whereHas('supervisors', function ($q) use ($supervisorSlug) {
                $q->where('users.slug', $supervisorSlug);
            });
        }

        if ($withCompany !== null) {
            if ($withCompany === 'yes') {
                $query->whereNotNull('organization_id');
            } elseif ($withCompany === 'no') {
                $query->whereNull('organization_id');
            }
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

        // Get supervisors for filter
        $supervisors = \App\Models\User::whereHas('supervisedProjects', function ($q) {
            $q->available();
        })
        ->orderBy('name')
        ->get();

        return view('projects.index', [
            'projects' => $projects,
            'selectedType' => $type,
            'natureTags' => $natureTags,
            'sectionTags' => $sectionTags,
            'focusTags' => $focusTags,
            'supervisors' => $supervisors,
            'selectedNature' => $natureTagSlug,
            'selectedSection' => $sectionTagSlug,
            'selectedFocus' => $focusTagSlug,
            'selectedSupervisor' => $supervisorSlug,
            'selectedWithCompany' => $withCompany,
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
            'organization'
        ]);

        return view('projects.show', [
            'project' => $project,
        ]);
    }
}
