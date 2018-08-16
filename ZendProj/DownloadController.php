<?php
class DownloadController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout()->disableLayout();
    }
    
    public function soapzipAction() {
        $conf = Zend_Registry::get('config');
        $neededpassword = $conf->export->soap->password;
        $neededuser = $conf->export->soap->username;
        $password = $this->getRequest()->getParam('password');
        $user = $this->getRequest()->getParam('user');
        if($password && $user && $password == $neededpassword && $user==$neededuser) {
            $filename = $this->getRequest()->getParam('filename');
            $tmp_file = $conf->export->soap->tmpfolder . '/' . $filename;
            if(file_exists($tmp_file)) {
                if($file = file_get_contents($tmp_file)) {
                    $this->getResponse()->setHeader('Content-Type', 'application/zip');
                    $this->getResponse()->setHeader('Content-Disposition', 'attachment; filename=' . $filename);
                    echo $file;
                } else {
                    $this->getResponse()->setHttpResponseCode(500);
                    $deb = Janitor_Core_Debugger::getInstance();
                    $deb->debug(Janitor_Core_Debugger::TYPE_FATAL_ERROR,'TestController.php - downloadAction(): could not open source file ' . $conf->export->soap->tmpfolder . '/' . $filename . ' for download');
                }
            } else {
                $this->getResponse()->setHttpResponseCode(404);
                $deb = Janitor_Core_Debugger::getInstance();
                $deb->debug(Janitor_Core_Debugger::TYPE_FATAL_ERROR,'TestController.php - downloadAction(): could not open source file ' . $conf->export->soap->tmpfolder . '/' . $filename . ' for download , file does not exist ...');
            }
        } else {
            $this->getResponse()->setHttpResponseCode(403);
            echo 'Forbidden!';
        }
    }
}