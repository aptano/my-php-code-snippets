<?php
/**
 * Created by PhpStorm.
 * User: Gabriel Homorogan
 * Date: 12.01.2016
 */

class Admin_Service_User extends Admin_Service_Abstract {

    protected $_classname = 'Janitor_Core_User';

    public function create($pData)
    {
        $returndata = array();
        foreach ($pData as $key => $itemdata) {
            try {
                $user = new Janitor_Core_User();
                $data = (array)$itemdata;
                $user->create($data, true);
                $returndata[$key] = $user->toArray();

            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }
        return array($this->_return_success_key => true,
            $this->_return_data_key => 'fjgkldjglfdkgjfdl',
            $this->_return_message_key => 'Action create for model ' . $this->_classname . ' was successfull');
    }

    public function getRoles($pParams)
    {
        $user_id = $pParams->user_id;
        $user = new Janitor_Core_User($user_id);
        return array($this->_return_success_key=>true, $this->_return_data_key=>$user->getRoles(true));
    }

    public function getGroups($pParams)
    {
        $user_id = $pParams->user_id;
        $user = new Janitor_Core_User($user_id);
        return array($this->_return_success_key=>true, $this->_return_data_key=>$user->getGroups(true));
    }

    public function registerRoles($pParams)
    {
        $user_id = $pParams->user_id;
        $user = new Janitor_Core_User($user_id);
        $user->unregisterRelatedObject('roles','all');
        foreach(explode(',',$pParams->roles) as $role_id) {
            $user->registerRelatedObject('roles', $role_id);
        }
    }

    public function registerGroups($pParams)
    {
        $user_id = $pParams->user_id;
        $user = new Janitor_Core_User($user_id);
        $user->unregisterRelatedObject('groups','all');
        foreach(explode(',',$pParams->groups) as $group_id) {
            $user->registerRelatedObject('groups', $group_id);
        }
    }
}
