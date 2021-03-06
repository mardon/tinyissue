<?php

/*
 * This file is part of the Tinyissue package.
 *
 * (c) Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tinyissue\Http\Controllers;

use Illuminate\Http\Request;
use Tinyissue\Form\FilterIssue as FilterForm;
use Tinyissue\Form\Note as NoteForm;
use Tinyissue\Form\Project as Form;
use Tinyissue\Http\Requests\FormRequest;
use Tinyissue\Model\Project;
use Tinyissue\Model\Project\Issue;
use Tinyissue\Model\Project\Note;

/**
 * ProjectController is the controller class for managing request related to a project
 *
 * @author Mohamed Alsharaf <mohamed.alsharaf@gmail.com>
 */
class ProjectController extends Controller
{
    /**
     * Display activity for a project
     *
     * @param Project $project
     *
     * @return \Illuminate\View\View
     */
    public function getIndex(Project $project)
    {
        $activities = $project->activities()
            ->with('activity', 'issue', 'user', 'assignTo', 'comment', 'note')
            ->orderBy('created_at', 'DESC')
            ->take(10)
            ->get();

        return view('project.index', [
            'project'               => $project,
            'active'                => 'activity',
            'activities'            => $activities,
            'open_issues_count'     => $project->openIssuesCount()->count(),
            'closed_issues_count'   => $project->closedIssuesCount()->count(),
            'assigned_issues_count' => $this->auth->user()->assignedIssuesCount($project->id),
            'notes_count'           => $project->notes()->count(),
            'sidebar'               => 'project'
        ]);
    }

    /**
     * Display issues for a project
     *
     * @param FilterForm $filterForm
     * @param Request    $request
     * @param Project    $project
     * @param int        $status
     *
     * @return \Illuminate\View\View
     */
    public function getIssues(FilterForm $filterForm, Request $request, Project $project, $status = Issue::STATUS_OPEN)
    {
        $active = $status == Issue::STATUS_OPEN ? 'open_issue' : 'closed_issue';
        $issues = $project->listIssues($status, $request->all());
        if ($status == Issue::STATUS_OPEN) {
            $closedIssuesCount = $project->closedIssuesCount()->count();
            $openIssuesCount = $issues->count();
        } else {
            $closedIssuesCount = $issues->count();
            $openIssuesCount = $project->openIssuesCount()->count();
        }

        return view('project.index', [
            'project'               => $project,
            'active'                => $active,
            'issues'                => $issues,
            'open_issues_count'     => $openIssuesCount,
            'closed_issues_count'   => $closedIssuesCount,
            'assigned_issues_count' => $this->auth->user()->assignedIssuesCount($project->id),
            'notes_count'           => $project->notes()->count(),
            'sidebar'               => 'project',
            'filterForm'            => $filterForm,
        ]);
    }

    /**
     * Display issues assigned to current user for a project
     *
     * @param Project $project
     *
     * @return \Illuminate\View\View
     */
    public function getAssigned(Project $project)
    {
        $issues = $project->listAssignedIssues($this->auth->user()->id);

        return view('project.index', [
            'project'               => $project,
            'active'                => 'issue_assigned_to_you',
            'issues'                => $issues,
            'open_issues_count'     => $project->openIssuesCount()->count(),
            'closed_issues_count'   => $project->closedIssuesCount()->count(),
            'assigned_issues_count' => $issues->count(),
            'notes_count'           => $project->notes()->count(),
            'sidebar'               => 'project'
        ]);
    }

    /**
     * Display notes for a project
     *
     * @param Project  $project
     * @param NoteForm $form
     *
     * @return \Illuminate\View\View
     */
    public function getNotes(Project $project, NoteForm $form)
    {
        $notes = $project->notes()->with('createdBy')->get();

        return view('project.index', [
            'project'               => $project,
            'active'                => 'notes',
            'notes'                 => $notes,
            'open_issues_count'     => $project->openIssuesCount()->count(),
            'closed_issues_count'   => $project->closedIssuesCount()->count(),
            'assigned_issues_count' => $this->auth->user()->assignedIssuesCount($project->id),
            'notes_count'           => $notes->count(),
            'sidebar'               => 'project',
            'noteForm'              => $form,
        ]);
    }

    /**
     * Edit the project
     *
     * @param Project $project
     * @param Form    $form
     *
     * @return \Illuminate\View\View
     */
    public function getEdit(Project $project, Form $form)
    {
        return view('project.edit', [
            'form'    => $form,
            'project' => $project,
            'sidebar' => 'project'
        ]);
    }

    /**
     * To update project details
     *
     * @param Project             $project
     * @param FormRequest\Project $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEdit(Project $project, FormRequest\Project $request)
    {
        // Delete the project
        if ($request->has('delete-project')) {
            $project->delete();

            return redirect('projects')
                ->with('notice', trans('tinyissue.project_has_been_deleted'));
        }

        $project->update($request->all());

        return redirect('projects')
            ->with('notice', trans('tinyissue.project_has_been_updated'));
    }

    /**
     * Ajax: returns list of users that are not in the project
     *
     * @param Project $project
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getInactiveUsers(Project $project)
    {
        $users = $project->usersNotIn();

        return response()->json($users);
    }

    /**
     * Ajax: add user to the project
     *
     * @param Project $project
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postAssign(Project $project, Request $request)
    {
        $status = false;
        if ($request->has('user_id')) {
            $project->assignUser($request->input('user_id'));
            $status = true;
        }

        return response()->json(['status' => $status]);
    }

    /**
     * Ajax: remove user from the project
     *
     * @param Project $project
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postUnassign(Project $project, Request $request)
    {
        $status = false;
        if ($request->has('user_id')) {
            $project->unassignUser($request->input('user_id'));
            $status = true;
        }

        return response()->json(['status' => $status]);
    }

    /**
     * To add a new note to the project
     *
     * @param Project          $project
     * @param Note             $note
     * @param FormRequest\Note $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postAddNote(Project $project, Note $note, FormRequest\Note $request)
    {
        $note->setRelation('project', $project);
        $note->setRelation('createdBy', $this->auth->user());
        $note->createNote($request->all());

        return redirect($note->to())->with('notice', trans('tinyissue.your_note_added'));
    }

    /**
     * Ajax: To update project note
     *
     * @param Project $project
     * @param Note    $note
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postEditNote(Project $project, Project\Note $note, Request $request)
    {
        $body = '';
        if ($request->has('body')) {
            $note->setRelation('project', $project);
            $note->body = $request->input('body');
            $note->save();
            $body = \Html::format($note->body);
        }

        return response()->json(['status' => true, 'text' => $body]);
    }

    /**
     * Ajax: to delete a project note
     *
     * @param Project $project
     * @param Note    $note
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDeleteNote(Project $project, Project\Note $note)
    {
        $note->delete();

        return response()->json(['status' => true]);
    }
}
