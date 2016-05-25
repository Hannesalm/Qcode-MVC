<?php

namespace Anax\Users;

/**
 * A controller for users and admin related events.
 *
 */
class UsersController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;


    public function initialize()
    {
        $this->users = new \Anax\Users\User();
        $this->users->setDI($this->di);
    }

    public function listAction()
    {
        $this->initialize();

        $all = $this->users->findAll();
        $log = "";
        if($this->users->isAuthenticated()){
            $log = "Loged in";
        }

        $this->theme->setTitle("List all users");
        $this->views->add('users/list-all', [
            'users' => $all,
            'title' => "All users",
            'log'   => $log,
        ]);
    }

    public function listMostActiveUsersAction(){
        $this->initialize();

        $all = $this->users->findMostActive();

        $this->theme->setTitle("List most active users");
        $this->views->add('project/list-all-active-users', ['users' => $all,], 'sidebar');

    }

    public function activeAction()
    {
        $this->initialize();

        $all = $this->users->findActive();


        $this->theme->setTitle("Users that are active");
        $this->views->add('users/list-all', [
            'users' => $all,
            'title' => "Users that are active",
        ]);
    }

    public function inactiveAction()
    {
        $this->initialize();

        $all = $this->users->findDeactive();


        $this->theme->setTitle("Users that are active");
        $this->views->add('users/list-all', [
            'users' => $all,
            'title' => "Users that are active",
        ]);
    }

    public function deletedAction()
    {
        $this->initialize();

        $all = $this->users->findDeleted();


        $this->theme->setTitle("Users that are in the trash");
        $this->views->add('users/list-all', [
            'users' => $all,
            'title' => "Users that are in the trash",
        ]);
    }

    public function idAction($id = null)
    {
        $this->initialize();

        $this->questions = new \Anax\Questions\Question();
        $this->questions->setDI($this->di);

        $user = $this->users->findQuestionsForUser($id);
        $userAnswers = $this->users->findAnswersForUser($id);
        $userComments = $this->users->findCommentsForUser($id);
        $usersQuestions = $this->questions->findUserQuestions($id);
        $tags = $this->questions->findAllTagsForUser($id);

        $this->theme->setTitle("View user");
        $this->views->add('users/view', [
            'user' => $user,
            'user_answers' => $userAnswers,
            'comments' => $userComments,
            'tags' => $tags,
            'usersQuestions' => $usersQuestions,

        ]);
    }

    public function addAction($username = null)
    {
        $username = $_POST['username'];

        if (!isset($username)) {
            dump($_SESSION);
            dump($_POST);
            die("Missing username");
        }

        $now = gmdate('Y-m-d H:i:s');

        $this->users->save([
            'id'        => $this->request->getPost('id'),
            'username' => $username,
            'email' => $username . '@mail.se',
            'name' => 'Mr/Mrs ' . $username,
            'password' => password_hash($username, PASSWORD_DEFAULT),
            'created' => $now,
            'active' => $now,
            'status' => 'Active'
        ]);

        // $url = $this->url->create('users/id/' . $this->users->id);
        $url = $this->url->create('users');
        $this->response->redirect($url);

        // $this->response->redirect($url);
    }

    public function createAction(){

        $this->initialize();


        $form = new \Mos\HTMLForm\CForm();

        $form = $form->create([], [

            'username' => [
                'class'       => 'form-control',
                'placeholder' => 'Enter your username',
                'type'        => 'text',
                'label'       => 'Username/username',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'name' => [
                'class'       => 'form-control',
                'placeholder' => 'Enter your name',
                'type'        => 'text',
                'label'       => 'Name',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'password' => [
                'class'       => 'form-control',
                'placeholder' => 'Enter your password',
                'type'        => 'password',
                'label'       => 'Password',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'email' => [
                'class'       => 'form-control',
                'placeholder' => 'Gravatar supported',
                'type'        => 'text',
                'required'    => true,
                'validation'  => ['not_empty', 'email_adress'],
            ],
            'submit' => [
                'class'       => 'btn btn-md btn-info btn-block',
                'type'        => 'submit',
                'value'       => 'Create account',
                'callback'  => function($form) {
                    $form->saveInSession = true;
                    return true;
                }
            ],
        ]);

        // Check the status of the form
        $status = $form->check();

        if ($status === true) {
            $user = $this->users->findUser($this->request->getPost('username'));

            if($user){
                $form = $form->create([], [

                    'username' => [
                        'class'       => 'form-control btn-danger',
                        'placeholder' => 'Username allready in use',
                        'type'        => 'text',
                        'label'       => 'Username/username',
                        'required'    => true,
                        'validation'  => ['not_empty'],
                    ],
                    'name' => [
                        'class'       => 'form-control',
                        'placeholder' => 'Enter your name',
                        'type'        => 'text',
                        'label'       => 'Name',
                        'required'    => true,
                        'validation'  => ['not_empty'],
                    ],
                    'password' => [
                        'class'       => 'form-control',
                        'placeholder' => 'Enter your password',
                        'type'        => 'password',
                        'label'       => 'Password',
                        'required'    => true,
                        'validation'  => ['not_empty'],
                    ],
                    'email' => [
                        'class'       => 'form-control',
                        'placeholder' => 'Gravatar supported',
                        'type'        => 'text',
                        'required'    => true,
                        'validation'  => ['not_empty', 'email_adress'],
                    ],
                    'submit' => [
                        'class'       => 'btn btn-md btn-info btn-block',
                        'type'        => 'submit',
                        'value'       => 'Create account',
                        'callback'  => function($form) {
                            $form->saveInSession = true;
                            return true;
                        }
                    ],
                ]);
            } else {
                $this->dispatcher->forward([
                    'controller' => 'users',
                    'action'     => 'save',
                ]);
            }

        } else if ($status === false) {

            var_dump('Check method returned false');
            die;
        }
        $this->theme->setTitle("Create account");
        $this->views->add('project/login', [
            'title' => 'Create user',
            'content' => $form->getHTML()
        ]);
    }

    public function editAction($id = null)
    {
        $this->initialize();

        $user = $this->users->find($id);

        $form = new \Mos\HTMLForm\CForm();

        $form = $form->create([], [
            'id' => [
                'type'        => 'hidden',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value' => $id
            ],
            'created' => [
                'type'        => 'hidden',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value' => $user->created
            ],
            'username' => [
                'class'       => 'form-control',
                'type'        => 'text',
                'label'       => 'Username/username',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value' => $user->username
            ],
            'name' => [
                'class'       => 'form-control',
                'type'        => 'text',
                'label'       => 'Name',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value' => $user->name
            ],
            'password' => [
                'class'       => 'form-control',
                'type'        => 'password',
                'label'       => 'Password',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value' => $user->password
            ],
            'email' => [
                'class'       => 'form-control',
                'type'        => 'text',
                'required'    => true,
                'validation'  => ['not_empty', 'email_adress'],
                'value' => $user->email
            ],
            'presentation' => [
                'class'       => 'form-control min-height',
                'type'        => 'textarea',
                'label'       => 'Presentation(write with markdown)',
                'required'    => true,
                'validation'  => ['not_empty'],
                'value' => $user->presentation
            ],
            'submit' => [
                'class'       => 'btn btn-md btn-info btn-block',
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
                'controller' => 'users',
                'action'     => 'save',
            ]);

        } else if ($status === false) {

            var_dump('Check method returned false');
            die;
        }
        $this->theme->setTitle("Edit user");
        $this->views->add('users/edit', [
            'content' => $form->getHTML(),
            'user' => $user
        ]);
    }

    public function deleteAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }

        $this->users->delete($id);

        $url = $this->url->create('users');
        $this->response->redirect($url);
    }

    public function softDeleteAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }

        $now = gmdate('Y-m-d H:i:s');

        $user = $this->users->find($id);

        $user->deleted = $now;
        $user->save();

        $url = $this->url->create('users');
        $this->response->redirect($url);
    }

    public function undoSoftDeleteAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }

        $user = $this->users->find($id);

        $user->deleted = null;
        $user->save();

        $url = $this->url->create('users');
        $this->response->redirect($url);
    }

    public function deactivateAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }

        $user = $this->users->find($id);

        $user->status = 'Deactive';
        $user->save();

        $url = $this->url->create('users');
        $this->response->redirect($url);
    }

    public function activateAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }

        $user = $this->users->find($id);

        $user->status = 'Active';
        $user->save();

        $url = $this->url->create('users');
        $this->response->redirect($url);
    }

    public function saveAction(){

        $this->initialize();

        $now = gmdate('Y-m-d H:i:s');

        $id = $this->request->getPost('id');
        $email = $this->request->getPost('email');
        $user = [
            'id'        => $this->request->getPost('id'),
            'name' => $this->request->getPost('name'),
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'created' => $now,
            'presentation' => $this->request->getPost('presentation'),
            'gravatar' => $this->users->get_gravatar($email)
        ];


        if(isset($id)){
            $this->users->updateUser($user);
        } else {
            $id = $this->users->create($user);
        }

        $idUrl = "users/id/" . $id;

        $url = $this->url->create($idUrl);
        $this->response->redirect($url);
    }

    public function loginAction(){

        $this->initialize();

        $form = new \Mos\HTMLForm\CForm();

        $form = $form->create([], [
            'username' => [
                'class'       => 'form-control',
                'placeholder' => 'Username',
                'type'        => 'text',
                'label'       => 'Username',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'password' => [
                'class'       => 'form-control',
                'placeholder' => 'Password',
                'type'        => 'password',
                'label'       => 'Password',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
            'submit' => [
                'class'       => 'btn btn-md btn-info btn-block',
                'type'        => 'submit',
                'value'       => 'Enter',
                'callback'  => function($form) {
                    $form->saveInSession = true;
                    return true;
                }
            ],
        ]);

        // Check the status of the form
        $status = $form->check();

        if ($status === true) {

            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');
            $password_from_database = "";

            if (isset($username) && isset($password)) {

                $res = $this->users->findUser($username);

                if($res){
                    $password_from_database = $res[0]->password;
                } else {
                    $form = $form->create([], [
                        'username' => [
                            'class'       => 'form-control',
                            'placeholder' => 'Username',
                            'type'        => 'text',
                            'label'       => 'Username',
                            'required'    => true,
                            'validation'  => ['not_empty'],
                        ],
                        'password' => [
                            'class'       => 'form-control btn-danger',
                            'placeholder' => 'Password',
                            'type'        => 'password',
                            'label'       => 'Wrong password',
                            'required'    => true,
                            'validation'  => ['not_empty'],
                        ],
                        'submit' => [
                            'class'       => 'btn btn-md btn-info btn-block',
                            'type'        => 'submit',
                            'value'       => 'Enter',
                            'callback'  => function($form) {
                                $form->saveInSession = true;
                                return true;
                            }
                        ],
                    ]);
                }

                if(password_verify($password, $password_from_database)){
                    $this->users->setSessionVariablesAtLogin($res);

                    $url = $this->di->get('url')->create('questions');
                    $this->response->redirect($url);
                } else {
                    $form = $form->create([], [
                        'username' => [
                            'class'       => 'form-control',
                            'placeholder' => 'Username',
                            'type'        => 'text',
                            'label'       => 'Username',
                            'required'    => true,
                            'validation'  => ['not_empty'],
                        ],
                        'password' => [
                            'class'       => 'form-control btn-danger',
                            'placeholder' => 'Password',
                            'type'        => 'password',
                            'label'       => 'Wrong password',
                            'required'    => true,
                            'validation'  => ['not_empty'],
                        ],
                        'submit' => [
                            'class'       => 'btn btn-md btn-info btn-block',
                            'type'        => 'submit',
                            'value'       => 'Enter',
                            'callback'  => function($form) {
                                $form->saveInSession = true;
                                return true;
                            }
                        ],
                    ]);
                }
            }

        } else if ($status === false) {

            var_dump('Check method returned false');
            die;
        }

        $this->theme->setTitle("Login");

        $this->views->add('project/login', [
            'content' => $form->getHTML(),
        ]);

    }

    public function logoutAction(){
        $this->users->logOut();

        $url = $this->url->create('');
        $this->response->redirect($url);
    }

}