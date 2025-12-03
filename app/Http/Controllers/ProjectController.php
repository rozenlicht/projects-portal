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
        $natureTagId = $request->get('nature');
        $sectionTagId = $request->get('section');
        $focusTagId = $request->get('focus');
        $withCompany = $request->get('with_company');
        
        $query = Project::with(['supervisors', 'tags', 'owner', 'organization'])
            ->available();

        if ($type && in_array($type, ['internship', 'bachelor_thesis', 'master_thesis'])) {
            $query->where('type', $type);
        }

        if ($natureTagId) {
            $query->whereHas('tags', function ($q) use ($natureTagId) {
                $q->where('tags.id', $natureTagId)
                  ->where('tags.category', TagCategory::Nature->value);
            });
        }

        if ($sectionTagId) {
            $query->whereHas('tags', function ($q) use ($sectionTagId) {
                $q->where('tags.id', $sectionTagId)
                  ->where('tags.category', TagCategory::Group->value);
            });
        }

        if ($focusTagId) {
            $query->whereHas('tags', function ($q) use ($focusTagId) {
                $q->where('tags.id', $focusTagId)
                  ->where('tags.category', TagCategory::Focus->value);
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

        return view('projects.index', [
            'projects' => $projects,
            'selectedType' => $type,
            'natureTags' => $natureTags,
            'sectionTags' => $sectionTags,
            'focusTags' => $focusTags,
            'selectedNature' => $natureTagId,
            'selectedSection' => $sectionTagId,
            'selectedFocus' => $focusTagId,
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
