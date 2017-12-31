<?php
/**
* rf_switch 
* @package project
* @author Wizard <sergejey@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.1 (wizard, 09:12:31 [Dec 31, 2017])
*/
//
//
class rf_switch extends module {
/**
* rf_switch
*
* Module class constructor
*
* @access private
*/
function rf_switch() {
  $this->name="rf_switch";
  $this->title="rf_switch";
  $this->module_category="<#LANG_SECTION_DEVICES#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data=0) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
 $this->getConfig();
 $out['API_URL']=$this->config['API_URL'];
 if (!$out['API_URL']) {
  $out['API_URL']='http://';
 }
 $out['API_KEY']=$this->config['API_KEY'];
 $out['API_USERNAME']=$this->config['API_USERNAME'];
 $out['API_PASSWORD']=$this->config['API_PASSWORD'];
 if ($this->view_mode=='update_settings') {
   global $api_url;
   $this->config['API_URL']=$api_url;
   global $api_key;
   $this->config['API_KEY']=$api_key;
   global $api_username;
   $this->config['API_USERNAME']=$api_username;
   global $api_password;
   $this->config['API_PASSWORD']=$api_password;
   $this->saveConfig();
   $this->redirect("?");
 }
}
/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 $this->admin($out);
}


function propertySetHandle($object, $property, $value) {
if ($property == 'rcdata')
   {
   $myobj=getObject($object);
   $NowTime = time();
   $rcdata = $value;

   $OldData = $myobj->getProperty('OldData'); 
   $OldTime = $myobj->getProperty('OldTime'); 

   $myobj->setProperty('OldData',$rcdata);
   $myobj->setProperty('OldTime',$NowTime);

   if (($OldData!=$rcdata) || (($OldData==$rcdata) && (($NowTime-$OldTime)>1)))
      {
      $objects=getObjectsByProperty('rfValue','=',$rcdata);
      if (count($objects)==0)
         {
         addClassObject('RfCodes', 'rf'.$rcdata);
            setGlobal('rf'.$rcdata.'.rfValue',$rcdata);
            setGlobal('rf'.$rcdata.'.Freq',$object);
         }
      elseif (is_array($objects)) 
         {
         foreach($objects as $obj) 
            {
            $method = getObject($obj)->getProperty('Method');
            if ($method)
	       {
	       callMethod($method);
	       }
            callMethod($obj.'.Play');
            }
         }
      }
   }
}

/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
  parent::install();

  addClass('RfSwitch');
    addClass('RfReciver','RfSwitch');
      addClassProperty('RfReciver','rcdata',7);
      addClassProperty('RfReciver','OldData');
      addClassProperty('RfReciver','OldTime');

         addClassObject('RfReciver', 'reciver315');
            addLinkedProperty('reciver315','rcdata','rf_switch');
         addClassObject('RfReciver', 'reciver433');
            addLinkedProperty('reciver433','rcdata','rf_switch');

      //addClassMethod('RfReciver','onRecive',$onReciveMethod);
  addClass('RfCodes','RfSwitch');
      addClassProperty('RfCodes','Freq');
      addClassProperty('RfCodes','Method');
      addClassProperty('RfCodes','rfValue');
      addClassProperty('RfCodes','Description');
         addClassMethod('RfCodes','Play');
 }
// --------------------------------------------------------------------
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgRGVjIDMxLCAyMDE3IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
