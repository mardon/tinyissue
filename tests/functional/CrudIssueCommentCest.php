<?php

use Tinyissue\Model\Project;

class CrudIssueCommentCest
{
    /**
     * @param FunctionalTester $I
     *
     * @actor FunctionalTester
     *
     * @return void
     */
    public function addComment(FunctionalTester $I)
    {
        $I->am('Developer User');
        $I->wantTo('add new comment to an issue');

        $admin = $I->createUser(2, 4);
        $I->amLoggedAs($admin);

        $project = $I->createProject(1, [$admin]);
        $issue = $I->createIssue(1, $admin, $admin, $project);

        $I->amOnAction('Project\IssueController@getIndex', ['project' => $project, 'issue' => $issue]);
        $I->fillField('comment', 'Comment one');
        $I->click(trans('tinyissue.comment'));
        $I->seeResponseCodeIs(200);
        $I->seeCurrentActionIs('Project\IssueController@getIndex', ['project' => $project, 'issue' => $issue]);
        $I->see('Comment one', '.comment .content');
    }

    /**
     * @param FunctionalTester\UserSteps $I
     *
     * @actor FunctionalTester\UserSteps
     *
     * @return void
     */
    public function updateComment(FunctionalTester\UserSteps $I)
    {
        $I->am('Developer User');
        $I->wantTo('edit an existing comment');

        $admin = $I->createUser(2, 4);
        $I->login($admin->email, '123', $admin->firstname);

        $project = $I->createProject(1, [$admin]);
        $issue = $I->createIssue(1, $admin, $admin, $project);
        $comment = $I->createComment(1, $admin, $issue);

        $uri = $I->getApplication()->url->action('Project\IssueController@postEditComment', ['comment' => $comment]);
        $I->sendAjaxPostRequest($uri, [
            'body'   => 'Comment one updated',
            '_token' => csrf_token(),
        ]);
        $I->seeResponseCodeIs(200);
        $I->amOnAction('Project\IssueController@getIndex', ['project' => $project, 'issue' => $issue]);
        $I->see('Comment one updated', '#comment' . $comment->id . ' .content');
    }

    /**
     * @param FunctionalTester $I
     *
     * @actor FunctionalTester
     *
     * @return void
     */
    public function deleteComment(FunctionalTester $I)
    {
        $I->am('Developer User');
        $I->wantTo('delete a comment from an issue');

        $admin = $I->createUser(2, 4);
        $I->amLoggedAs($admin);

        $project = $I->createProject(1, [$admin]);
        $issue = $I->createIssue(1, $admin, $admin, $project);
        $comment1 = $I->createComment(1, $admin, $issue);
        $comment2 = $I->createComment(2, $admin, $issue);

        $I->amOnAction('Project\IssueController@getIndex', ['project' => $project, 'issue' => $issue]);
        $I->see($comment1->comment, '#comment' . $comment1->id . ' .content');
        $I->see($comment2->comment, '#comment' . $comment2->id . ' .content');

        $uri = $I->getApplication()->url->action('Project\IssueController@getDeleteComment', ['comment' => $comment1]);
        $I->sendAjaxGetRequest($uri);
        $I->seeResponseCodeIs(200);
        $I->amOnAction('Project\IssueController@getIndex', ['project' => $project, 'issue' => $issue]);
        $I->dontSee($comment1->comment);
        $I->see($comment2->comment);
    }
}