<h2>
    @permission('project-create')
    <a href="{{ URL::to('projects/new') }}" class="add" title="New Project">@lang('tinyissue.new')</a>
    @endpermission
    @lang('tinyissue.active_projects')
    <span>@lang('tinyissue.active_projects_description')</span>
</h2>

<ul>
    @forelse ($projects as $project)
    <li>
        <a href="{{ $project->to() }}" data-project-id="{{ $project->id }}" class="project load-progress">
            <span>{{ $project->name }}</span>
        </a>
    </li>
    @empty
    @endforelse
</ul>
