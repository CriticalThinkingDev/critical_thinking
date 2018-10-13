<?php
class Krishinc_Autosendtrack_Adminhtml_IndexController extends Mage_Adminhtml_Controller_action
{
    public function updatepriorityAction(){

        Mage::getModel('autosendtrack/autosendtrack')->updatePriority();
        Mage::getSingleton('adminhtml/session')->addSuccess('Grouped Products priority updated.');
        $this->_redirectReferer();

    }
public function updateseriesAction(){

        Mage::getModel('autosendtrack/autosendtrack')->updateSeries();
        Mage::getSingleton('adminhtml/session')->addSuccess('Grouped Series Products priority updated.');
        $this->_redirectReferer();

    }

}
