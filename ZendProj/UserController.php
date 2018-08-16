<?php
/**
 * Created by PhpStorm.
 * User: Gabriel Homorogan
 * Date: 12.01.2016
 */

class UserController extends Janitor_Controller_Action
{
    public function listAction()
    {
        $userObj = new Multizine_User();
        $users = $userObj->listAll(null,array('lastname','ASC'));
        $userlist = array();
        foreach($users as $user) {
            $userlist[] = new Multizine_User($user['user_id']);
        }
        $this->view->assign('users',$userlist);
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $user = new Multizine_User($id);
        $form = new Form_User();
        if($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if($form->isValid($data)) {
                $roles = $data['roles'];
                $groups = $data['groups'];
                $publication_editors = $data['publication_editors'];
                $issue_editors = $data['issue_editors'];
                $publication_authors = $data['publication_authors'];
                $issue_authors = $data['issue_authors'];
                $password = $data['password'];

                unset($data['send']);
                unset($data['roles']);
                unset($data['groups']);
                unset($data['publication_editors']);
                unset($data['issue_editors']);
                unset($data['publication_authors']);
                unset($data['issue_authors']);
                unset($data['multiselect']);
                unset($data['password']);
                unset($data['retype_password']);

                $user->update($data, true);
                if($password != '') {
                    $user->setNewPassword($password);
                }
                $user->unregisterRelatedObject('roles','all');
                foreach($roles as $role_id) {
                    $user->registerRelatedObject('roles',$role_id);
                }

                $user->unregisterRelatedObject('groups','all');
                foreach($groups as $group_id) {
                    $user->registerRelatedObject('groups',$group_id);
                }

                $user->unregisterRelatedObject('publications_editor','all');
                foreach($publication_editors as $publication_id) {
                    $user->registerRelatedObject('publications_editor',$publication_id);
                }

                $user->unregisterRelatedObject('issues_editor','all');
                foreach($issue_editors as $issue_id) {
                    $user->registerRelatedObject('issues_editor',$issue_id);
                }

                $user->unregisterRelatedObject('publications_author','all');
                foreach($publication_authors as $publication_id) {
                    $user->registerRelatedObject('publications_author',$publication_id);
                }

                $user->unregisterRelatedObject('issues_author','all');
                foreach($issue_authors as $issue_id) {
                    $user->registerRelatedObject('issues_author',$issue_id);
                }
                $this->_flashMessenger->addMessage(array('success','Der Benutzer ' . $data['name'] . ' wurde erfolgreich aktualisiert'));
                $this->redirect('/user/list');
            }
        } else {
            $data = $user->toArray();
            $data['roles'] = array();
            $data['groups'] = array();
            $data['publication_editors'] = array();
            $data['publication_authors'] = array();
            $data['issue_editors'] = array();
            $data['issue_authors'] = array();

            if(is_array($user->getValue('roles'))) {
                foreach($user->getValue('roles') as $role) {
                    $data['roles'][] = $role->getId();
                }
            }
            if(is_array($user->getValue('groups'))) {
                foreach($user->getValue('groups') as $group) {
                    $data['groups'][] = $group->getId();
                }
            }
            if(is_array($user->getValue('publications_editor'))) {
                foreach($user->getValue('publications_editor') as $peditor) {
                    $data['publication_editors'][] = $peditor->getId();
                }
            }
            if(is_array($user->getValue('publications_author'))) {
                foreach($user->getValue('publications_author') as $pauthor) {
                    $data['publication_authors'][] = $pauthor->getId();
                }
            }
            if(is_array($user->getValue('issues_editor'))) {
                foreach($user->getValue('issues_editor') as $ieditor) {
                    $data['issue_editors'][] = $ieditor->getId();
                }
            }
            if(is_array($user->getValue('issues_author'))) {
                foreach($user->getValue('issues_author') as $iauthor) {
                    $data['issue_authors'][] = $iauthor->getId();
                }
            }
            $form->populate($data);
            $this->view->assign('form',$form);
        }
    }

    public function createAction()
    {
        $form = new Form_User();
        if($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if($form->isValid($data)) {
                if(isset($data['roles'])) {
                    $roles = $data['roles'];
                }
                if(isset($data['groups'])) {
                    $groups = $data['groups'];
                }
                if(isset($data['publication_editors'])) {
                    $publication_editors = $data['publication_editors'];
                    unset($data['publication_editors']);
                } else {
                    $publication_editors = array();
                }
                if(isset($data['issue_editors'])) {
                    $issue_editors = $data['issue_editors'];
                    unset($data['issue_editors']);
                } else {
                    $issue_editors = array();
                }
                if(isset($data['publication_authors'])) {
                    $publication_authors = $data['publication_authors'];
                    unset($data['publication_authors']);
                } else {
                    $publication_authors = array();
                }
                if(isset($data['issue_authors'])) {
                    $issue_authors = $data['issue_authors'];
                    unset($data['issue_authors']);
                } else {
                    $issue_authors = array();
                }

                unset($data['send']);
                unset($data['roles']);
                unset($data['groups']);
                unset($data['multiselect']);
                unset($data['retype_password']);

                $user = new Multizine_User();
                if('' == $data['password']) {
                    unset($data['password']);
                }

                $user->create($data, true);
                //$user->activate();

                foreach($roles as $role_id) {
                    $user->registerRelatedObject('roles',$role_id);
                }

                foreach($groups as $group_id) {
                    $user->registerRelatedObject('groups',$group_id);
                }

                foreach($publication_editors as $publication_id) {
                    $publication = new Multizine_Publication($publication_id);
                    $publication->registerRelatedObject('editors',$user->getId());
                }

                foreach($issue_editors as $issue_id) {
                    $issue = new Multizine_Issue($issue_id);
                    $issue->registerRelatedObject('editors',$user->getId());
                }

                foreach($publication_authors as $publication_id) {
                    $publication = new Multizine_Publication($publication_id);
                    $publication->registerRelatedObject('authors',$user->getId());
                }

                foreach($issue_authors as $issue_id) {
                    $issue = new Multizine_Issue($issue_id);
                    $issue->registerRelatedObject('authors',$user->getId());
                }

                $form->reset();
                $this->view->assign('success_message','Der Benutzer ' . $data['name'] . ' wurde erfogreich angelegt');
            } else {
                $form->populate($data);
                $this->view->assign('error_message','Es sind Fehler aufgetreten. Bitte überprüfen Sie die Angaben');
            }
        }
        $this->view->assign('form',$form);
    }

    public function updateAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        if($this->getRequest()->getPost()) {
            $data = Janitor_Core_HelperFunctions::escapeUserInput($this->getRequest()->getPost());
            $userdata = $data;
            $password = $userdata['password'];
            $retyped_password = $userdata['retype_password'];
            Zend_Debug::dump($userdata);
            unset($userdata['frompage']);
            unset($userdata['retype_password']);
            unset($userdata['password']);
            Zend_Debug::dump($userdata);
            $currentuser = Janitor_Core_Auth::getInstance()->getData()->user;
            $user = new Multizine_User($currentuser->getId());
            $user->update($userdata);
            $this->_flashMessenger->addMessage(array('success','Ihre Benutzerdaten wurden erfolgreich aktualisiert.'));
            if($password != '') {
                if($password === $retyped_password) {
                    $user->setNewPassword($password);
                } else {
                    $this->_flashMessenger->addMessage(array('danger','Die beiden Passwörter stimmen nicht überein. Bitte geben Sie Ihr neues Passwort erneut ein.'));
                }
            }
            $this->redirect($data['frompage']);
        }
    }

    public function destroyAction()
    {
        $user = new Multizine_User($this->getRequest()->getParam('id'));
        $data = $user->toArray();
        $user->destroy();
        $this->_flashMessenger->addMessage(array('success','Der Benutzer ' . $data['name'] .  ' wurde erfolgreich gelöscht.'));
        $this->redirect('/user/list');
    }

    public function reactivateAction()
    {
        $this->_helper->layout()->setLayout('login');
        $conf = Zend_Registry::get('config');
        $this->view->assign('title', $conf->page->name);
        $checkString = $this->getRequest()->getParam('cs');
        $userobj = new Multizine_User();
        $userinfo = $userobj->listAll(array('checkstring'=>$checkString));
        if(is_array($userinfo) && count($userinfo) > 0) {
            $user = new Multizine_User($userinfo[0]['user_id']);
            $user->activate();
            $user->resetLoginFailureCount();
            $this->_flashMessenger->addMessage(array('success', 'Ihr Benutzerkonto wurde erfolgreich reaktiviert.<br><br>Sollten Sie Ihr Passwort vergessen haben, klicken Sie bitte <a href="/user/lostpassword">&gt;&gt;hier</a>.'));
            $this->redirect('/index/login');
        } else {
            $this->view->assign('errormessage_invalid','Dieser Link ist nicht mehr gültig!');
        }
    }

    public function resetpasswordAction()
    {
        $this->_helper->layout()->setLayout('login');
        $conf = Zend_Registry::get('config');
        $this->view->assign('title', $conf->page->name);
        $checkString = $this->getRequest()->getParam('cs');
        $userobj = new Multizine_User();
        $userinfo = $userobj->listAll(array('checkstring'=>$checkString));
        $this->view->assign('checkstring',$checkString);
        if(is_array($userinfo) && count($userinfo) > 0) {
            if($this->getRequest()->isPost()) {
                $password = $this->getRequest()->getParam('password');
                $passcheck = $this->getRequest()->getParam('passcheck');
                if($password == $passcheck) {
                    try {
                        $user = new Multizine_User($userinfo[0]['user_id']);
                        $user->setNewPassword($password);
                        $this->_flashMessenger->addMessage(array('success', 'Ihr neues Passwort wurde erfolgreich gespeichert.'));
                        $this->redirect('/index/login');
                    } catch(Exception $e) {
                        $debug = Janitor_Core_Debugger::getInstance();
                        $debug->debug(Janitor_Core_Debugger::TYPE_FATAL_ERROR,$e->getMessage());
                    }
                } else {
                    $this->view->assign('errormessage','Die eingegebenen Passwörter stimmen nicht überein');
                }
            }
        } else {
            $this->view->assign('errormessage_invalid','Dieser Link ist nicht mehr gültig!');
        }
    }

    public function activateAction()
    {
        $this->_helper->layout()->setLayout('login');
        $conf = Zend_Registry::get('config');
        $this->view->assign('title', $conf->page->name);
        $checkString = $this->getRequest()->getParam('cs');
        $userobj = new Multizine_User();
        $userinfo = $userobj->listAll(array('checkstring'=>$checkString));
        $this->view->assign('checkstring',$checkString);
        if(is_array($userinfo) && count($userinfo) > 0) {
            $this->view->assign('username',$userinfo[0]['name']);
            if($this->getRequest()->isPost()) {
                $password = $this->getRequest()->getParam('password');
                $passcheck = $this->getRequest()->getParam('passcheck');
                if($password == $passcheck) {
                    try {
                        $user = new Multizine_User($userinfo[0]['user_id']);
                        try {
                            $user->setNewPassword($password);
                            $user->activate();
                            $this->_flashMessenger->addMessage(array('success', 'Ihr Passwort wurde erfolgreich gespeichert.<br>Sie können sich nun anmelden.'));
                            $this->redirect('/index/login/activationusername/' . base64_encode($userinfo[0]['name']));
                        } catch(Exception $e) {
                            $this->view->assign('errormessage',$e->getMessage());
                        }

                    } catch(Exception $e) {
                        $debug = Janitor_Core_Debugger::getInstance();
                        $debug->debug(Janitor_Core_Debugger::TYPE_FATAL_ERROR,$e->getMessage());
                    }
                } else {
                    $this->view->assign('errormessage','ERROR_PASSWORD_MISSMATCH');
                }
            }
        } else {
            $this->_flashMessenger->addMessage(array('warning','Der aufgerufene Link ist nicht mehr gültig. Wahrscheinlich haben Sie die Registrierung bereits abgeschlossen. Sie können sich direkt im System mit Ihrem Benutzernamen und Ihrem Passwort anmelden.'));
            $this->redirect('/index/login/');
        }
    }

    public function lostpasswordAction()
    {
        $this->_helper->layout()->setLayout('login');
        $conf = Zend_Registry::get('config');
        $this->view->assign('title', $conf->page->name);
        if($this->getRequest()->isPost()) {
            $username = Janitor_Core_HelperFunctions::escapeUserInput($this->getRequest()->getParam('username'));
            $userobj = new Multizine_User();
            $notfound_error = false;
            $userinfo = $userobj->listAll(array('name'=>$username),null,array(0,1));
            if(is_array($userinfo) && count($userinfo) > 0) {
                try {
                    $user = new Multizine_User($userinfo[0]['user_id']);
                    $user->sendLostPasswordMail();
                    $this->view->assign('successmessage','Es wurde eine E-Mail an die Adresse gesendet, die mit Ihrem Benutzer-Account verknüpft ist. Bitte folgen Sie den Anweisungen in der E-Mail');
                } catch(Exception $e) {
                    $notfound_error = true;
                    Zend_Debug::dump($e->getMessage());
                }
            } else {
                $notfound_error = true;
            }
            if(true === $notfound_error) {
                $this->view->assign('notfoundmessage','Der angegebene Benutzername konnte im System nicht gefunden werden');
            }
        }
    }
}