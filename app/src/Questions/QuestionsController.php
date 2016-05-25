<?php

namespace Anax\Questions;

/**
 * A controller for questions.
 *
 */
class QuestionsController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

    public function initialize()
    {
        $this->questions = new \Anax\Questions\Question();
        $this->questions->setDI($this->di);
    }

    public function listAction()
    {
        $this->initialize();

        $all = $this->questions->findAll();
        $tags = $this->questions->findAllTAgs();

        $this->theme->setTitle("List all questions");
        $this->views->add('questions/list-all', [
            'title' => "All questions",
            'questions' => $all,
            'tags'  => $tags
        ]);
    }

    public function listLatestQuestionsAction(){
        $this->initialize();

        $all = $this->questions->findLatestQuestions();
        $tags = $this->questions->findAllTAgs();

        $this->theme->setTitle("List latest questions");
        $this->views->add('questions/list-all', ['questions' => $all,'tags' => $tags], 'column1');
    }

    public function listByTagAction($tag = null)
    {
        $this->initialize();

        $theTag = $this->questions->findTag($tag);
        $tagID = $theTag->tag_id;

        $all = $this->questions->getQuestionsByTag($tagID);
        $tags = $this->questions->findAllTAgs();

        $this->theme->setTitle("List all questions");
        $this->views->add('questions/list-all', [
            'title' => "All questions by tag",
            'questions' => $all,
            'tags'  => $tags
        ]);
    }

    public function listMostPopularTagsAction(){
        $this->initialize();

        $all = $this->questions->getMostPopularTags();

        $this->theme->setTitle("List most popular tags");
        $this->views->add('project/list-popular-tags', ['tags' => $all,], 'sidebar');
    }

    public function deletedAction()
    {
        $this->initialize();

        $all = $this->questions->findDeleted();


        $this->theme->setTitle("questions that are in the trash");
        $this->views->add('questions/list-all', [
            'questions' => $all,
            'title' => "questions that are in the trash",
        ]);
    }

    public function idAction($id = null)
    {
        $this->initialize();

        $question = $this->questions->find($id);


        $form = new \Mos\HTMLForm\CForm();

        $form = $form->create([], [
            'question_id' => [
                'type'        => 'hidden',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value' => $id
            ],
            'user_id' => [
                'type'        => 'hidden',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value' => $question->id
            ],
            'answer' => [
                'class'       => 'form-control min-height',
                'placeholder' => 'Write your answer with markdown-syntax',
                'type'        => 'textarea',
                'label'       => '',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'submit' => [
                'class'       => 'btn btn-md btn-info btn-block',
                'type'        => 'submit',
                'value'       => 'Create answer',
                'callback'  => function($form) {
                    $form->saveInSession = true;
                    return true;
                }
            ],
        ]);

        // Check the status of the form
        $status = $form->check();

        if ($status === true) {
            $this->dispatcher->forward([
                'controller' => 'questions',
                'action'     => 'answer',
            ]);

        } else if ($status === false) {

            var_dump('Check method returned false');
            die;
        }

        $question = $this->questions->find($id);
        $tags = $this->questions->findAllTAgs();
        $answers = $this->questions->getAnswersForQuestion($id);
        $comments = $this->questions->findAllComments($answers);


        $this->theme->setTitle("View question with id");
        $this->views->add('questions/view', [
            'question' => $question,
            'tags'  => $tags,
            'form'  => $form->getHTML(),
            'answers' => $answers,
            'comments' => $comments
        ]);
    }

    public function commentIDAction($id = null){
        $this->initialize();

        $comment = $this->questions->getAnswerByID($id);


        $form = new \Mos\HTMLForm\CForm();

        $form = $form->create([], [
            'comment_id' => [
                'type'        => 'hidden',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value' => $id
            ],
            'question_id' => [
                'type'        => 'hidden',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value' => $comment->question_id
            ],
            'user_id' => [
                'type'        => 'hidden',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value' => $comment->user_id
            ],
            'comment' => [
                'class'       => 'form-control min-height-100',
                'placeholder' => 'Write your comment with markdown-syntax',
                'type'        => 'textarea',
                'label'       => '',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'submit' => [
                'class'       => 'btn btn-md btn-info btn-block',
                'type'        => 'submit',
                'value'       => 'Add comment',
                'callback'  => function($form) {
                    $form->saveInSession = true;
                    return true;
                }
            ],
        ]);

        // Check the status of the form
        $status = $form->check();

        if ($status === true) {
            $this->dispatcher->forward([
                'controller' => 'questions',
                'action'     => 'comment',
            ]);

        } else if ($status === false) {

            var_dump('Check method returned false');
            die;
        }

        $answer = $this->questions->getAnswerByID($id);


        $this->theme->setTitle("View question with id");
        $this->views->add('questions/comment', [
            'form'  => $form->getHTML(),
            'answer' => $answer,
        ]);
    }

    public function createAction(){

        $this->initialize();


        $form = new \Mos\HTMLForm\CForm();

        $form = $form->create([], [

            'title' => [
                'class'       => 'form-control',
                'placeholder' => 'What is your programming question?',
                'type'        => 'text',
                'label'       => 'Title',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'content' => [
                'class'       => 'form-control',
                'type'        => 'textarea',
                'label'       => 'Question (markdown supported)',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'tags' => [
                'class'       => 'form-control',
                'placeholder' => 'max 5 tags. Separate with comma',
                'type'        => 'text',
                'label'       => 'Tags',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'submit' => [
                'class'       => 'btn btn-md btn-info btn-block',
                'type'        => 'submit',
                'value'       => 'Create question',
                'callback'  => function($form) {
                    $form->saveInSession = true;
                    return true;
                }
            ],
        ]);

        // Check the status of the form
        $status = $form->check();

        if ($status === true) {

            $this->dispatcher->forward([
                'controller' => 'questions',
                'action'     => 'save',
            ]);

        } else if ($status === false) {

            var_dump('Check method returned false');
            die;
        }

        $this->views->add('questions/create', [
            'title' => 'Create user',
            'content' => $form->getHTML()
        ]);
    }

    public function editAction($id = null)
    {
        $this->initialize();

        $user = $this->questions->find($id);


        $form = new \Mos\HTMLForm\CForm();

        $form = $form->create([], [
            'id' => [
                'type'        => 'hidden',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value' => $id
            ],
            'username' => [
                'type'        => 'text',
                'label'       => 'Username/username',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value' => $user->username
            ],
            'name' => [
                'type'        => 'text',
                'label'       => 'Name',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value' => $user->name
            ],
            'password' => [
                'type'        => 'password',
                'label'       => 'Password',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value' => $user->password
            ],
            'email' => [
                'type'        => 'text',
                'required'    => true,
                'validation'  => ['not_empty', 'email_adress'],
                'value' => $user->email
            ],
            'submit' => [
                'type'        => 'submit',
                'callback'  => function($form) {
                    $form->saveInSession = true;
                    return true;
                }
            ],
        ]);

        // Check the status of the form
        $status = $form->check();

        if ($status === true) {

            $this->dispatcher->forward([
                'controller' => 'questions',
                'action'     => 'save',
            ]);

        } else if ($status === false) {

            var_dump('Check method returned false');
            die;
        }

        $this->views->add('me/page', [
            'title' => 'Add user',
            'content' => $form->getHTML()
        ]);
    }

    public function deleteAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }

        $this->questions->delete($id);

        $url = $this->url->create('questions');
        $this->response->redirect($url);
    }

    public function saveAction(){

        $this->initialize();

        $now = date('Y-m-d H:i:s');

        $user = [
            'question_id'  => $this->request->getPost('id'),
            'title'        => $this->request->getPost('title'),
            'content'      => $this->request->getPost('content'),
            'made'         => $now,
            'user_id'      => $this->session->getUserID(),
            'tags'         => $this->request->getPost('tags'),
        ];


        $this->questions->save($user);

        $url = $this->url->create('questions');
        $this->response->redirect($url);
    }

    public function answerAction(){
        $this->initialize();

        $now = date('Y-m-d H:i:s');

        $answer = [
            'question_id'  => $this->request->getPost('question_id'),
            'user_id'      => $this->session->get('userID'),
            'content'      => $this->request->getPost('answer'),
            'time_created'         => $now,
        ];

        $this->questions->saveAnswer($answer);

        $idUrl = "questions/id/". $this->request->getPost('question_id');

        $url = $this->url->create($idUrl);
        $this->response->redirect($url);
    }

    public function commentAction(){
        $this->initialize();

        $now = date('Y-m-d H:i:s');

        $user = [
            'comment_parent_id'  => $this->request->getPost('comment_id'),
            'user_id'      => $this->session->get('userID'),
            'content'      => $this->request->getPost('comment'),
            'time_created'         => $now,
        ];

        $this->questions->saveComment($user);

        $idUrl = "questions/id/". $this->request->getPost('question_id');

        $url = $this->url->create($idUrl);
        $this->response->redirect($url);
    }

    public function upvoteAction($id = null){
        $this->initialize();

        $this->questions->upvote($id);


        $questionUrl = "questions";
        $url = $this->url->create($questionUrl);
        $this->response->redirect($url);
    }

    public function downvoteAction($id = null){
        $this->initialize();

        $this->questions->downvote($id);

        $questionUrl = "questions";
        $url = $this->url->create($questionUrl);
        $this->response->redirect($url);
    }

    public function tagsAction(){
        $this->initialize();

        $all = $this->questions->getAllTags();
        $this->views->add('tags/list-all', [
            'title' => "All Tags",
            'tags'  => $all
        ]);
    }

    public function acceptAction($id = null){

        $this->initialize();

        $res = $this->questions->getAnswerByID($id);

        $this->questions->acceptAnswer($id);

        $idUrl = "questions/id/". $res->question_id;
        $url = $this->url->create($idUrl);
        $this->response->redirect($url);
    }

}