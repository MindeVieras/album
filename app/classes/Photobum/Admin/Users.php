<?php

namespace Photobum\Admin;


use Photobum\Utilities\General;
use DB\SQL\Mapper;
use Photobum\Config;

class Users extends Admin
{

    public function __construct()
    {
        parent::__construct();
        $this->model = New Mapper($this->db, 'users');
        $this->page['title']= 'Users Manager';
        $this->page['body_class']= 'users';
        $this->page['section']= 'users';

    }

    public function view($params)
    {
        $this->auth();
        $this->results = $this->db->exec("SELECT * FROM users ORDER BY id ASC LIMIT 45");
        $template = $this->twig->loadTemplate('Admin/User/view.html');
        echo $template->render(array(
            'page' => $this->page,
            'data' => $this->results,
            'user' => $this->f3->get('SESSION.cw_cms_admin')
        ));
    }


    public function add()
    {
        $this->auth();
        if ($this->f3->get('VERB') == 'POST') {
            $user = $this->f3->get('POST');
            $editMode = $user['id'] ? true : false;

            if ($editMode) {
                $this->model->load(['id=?', $user['id']]);

                if ($this->model->dry()) {
                    General::flushJsonResponse(['ack'=>'Error', 'msg'=>'Couldn\'t edit this news item']);
                }

                // if (!$editMode){
                //     // also save settings               
                //     $sett = $this->initOrm('user_settings', true);
                //     $sett->user_id = 1;
                //     $sett->menu_collapsed = 1;
                //     $sett->save();

                // }

            } else {
                $this->model->load(['username=?', $user['email']]);
            }

            //Ensure username is unique.
            if ($this->model->dry()) {
                //Ensure access level is between 1 and 100.
                if (!$user['access_level'] >= 1 && !$user['access_level'] <= 100) {
                    General::flushJsonResponse(['ack'=>'Error', 'msg'=>'Access Level Out Of Bounds. (1-100)']);
                }
                if (!$editMode || ($editMode && $user['password'])) {

                    // //Ensure both password fields match.
                    // if ($user['password'] !== $user['confirm_password']) {
                    //     General::flushJsonResponse(['ack' => 'Error', 'msg' => 'Passwords do not match.']);
                    // }
                    // //Ensure password is longer than 6 characters.
                    // if (strlen($user['password']) < 8) {
                    //     General::flushJsonResponse(['ack' => 'Error', 'msg' => 'Your password must be at least six characters long.']);
                    // }
                    // //Ensure password contains numbers.
                    // if (!preg_match("#[0-9]+#", $user['password'])) {
                    //     General::flushJsonResponse(['ack' => 'Error', 'msg' => 'Your password must contain at least one number.']);
                    // }
                    // //Ensure password contains UPPERCASE characters.
                    // if (!preg_match("#[A-Z]+#", $user['password'])) {
                    //     General::flushJsonResponse(['ack' => 'Error', 'msg' => 'Your password must contain upper case characters.']);
                    // }
                    // //Ensure password lowercase characters.
                    // if (!preg_match("#[a-z]+#", $user['password'])) {
                    //     General::flushJsonResponse(['ack' => 'Error', 'msg' => 'Your password must contain lower case characters.']);
                    // }
                    $password = password_hash($user['password'], PASSWORD_DEFAULT);
                }

                $this->model->username = $user['email'];
                if ($password) {
                    $this->model->password = $password;
                }
                //$this->model->display_name = $user['display_name'];
                //$this->model->attribution_name = $user['attribution_name'];
                $this->model->access_level = $user['access_level'];
                //$this->model->person = $user['person_id'];
                $this->model->active = intval($user['status'] == 'true');
                $this->model->save();

                if (!$editMode){
                    // also save settings               
                    $sett = $this->initOrm('user_settings', true);
                    $sett->user_id = $this->model->id;
                    $sett->menu_collapsed = 1;
                    $sett->save();

                }

                $data = ['ack' => 'ok'];

                
                General::flushJsonResponse($data);
            } else {
                $data = ['ack' => 'Error', 'msg' => 'This username is already in use'];
                General::flushJsonResponse($data);
            }
        }else{
            $template = $this->twig->loadTemplate('Admin/User/add.html');
            echo $template->render([
                'persons' => General::getPersons(),
                'page' => $this->page
            ]);
        }
    }

    public function edit($params)
    {
        $this->auth();
        $this->model->load(['id=?', $params['id']]);
        $template = $this->twig->loadTemplate('Admin/User/edit.html');
        echo $template->render([
            'user' => $this->model->cast(),
            'persons' => General::getPersons($params['id']),
            'page' => $this->page
        ]);
    }

    public function delete($params)
    {
        $this->auth();
        if ($this->f3->get('VERB') == 'DELETE') {
            $this->model->load(['id=?', $params['id']]);
            if(!$this->model->dry()){
                $this->model->erase();
                General::flushJsonResponse([ack=>'OK']);
            }

        }
        General::flushJsonResponse([ack=>'Error', 'msg'=>'Could not delete user']);
    }


}