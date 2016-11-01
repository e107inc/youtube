<?php

// Generated e107 Plugin Admin Area 

require_once('../../class2.php');
if (!getperms('P')) 
{
	e107::redirect('admin');
	exit;
}

e107::lan('youtube', 'admin', true);

class youtube_adminArea extends e_admin_dispatcher
{

	protected $modes = array(	
	
		'main'	=> array(
			'controller' 	=> 'youtube_ui',
			'path' 			=> null,
			'ui' 			=> 'youtube_form_ui',
			'uipath' 		=> null
		),		

	);	
	
	
	protected $adminMenu = array(

		'main/list'			=> array('caption'=> LAN_MANAGE, 'perm' => 'P'),
		'main/create'		=> array('caption'=> LAN_CREATE, 'perm' => 'P'),
		'main/prefs'		=> array('caption'=> LAN_PREFS, 'perm' => 'P'),		
	);

	protected $adminMenuAliases = array(
		'main/edit'	=> 'main/list'				
	);	
	
	protected $menuTitle = 'Youtube';
}

				
class youtube_ui extends e_admin_ui
{
			
		protected $pluginTitle		= 'Youtube';
		protected $pluginName		= 'youtube';
	//	protected $eventName		= 'youtube-youtube'; // remove comment to enable event triggers in admin. 		
		protected $table			= 'youtube';
		protected $pid				= 'youtube_id';
		protected $perPage			= 10; 
		protected $batchDelete		= true;
	//	protected $batchCopy		= true;		
	//	protected $sortField		= 'somefield_order';
	//	protected $orderStep		= 10;
	//	protected $tabs				= array('Tabl 1','Tab 2'); // Use 'tab'=>0  OR 'tab'=>1 in the $fields below to enable. 
		
	//	protected $listQry      	= "SELECT * FROM `#tableName` WHERE field != '' "; // Example Custom Query. LEFT JOINS allowed. Should be without any Order or Limit.
	
		protected $listOrder		= 'youtube_id DESC';
	
		protected $fields 		= array (  'checkboxes' =>   array ( 'title' => '', 'type' => null, 'data' => null, 'width' => '5%', 'thclass' => 'center', 'forced' => '1', 'class' => 'center', 'toggle' => 'e-multiselect',  ),
		  'youtube_id' =>   array ( 'title' => LAN_ID, 'data' => 'int', 'width' => '5%', 'help' => '', 'readParms' => '', 'writeParms' => '', 'class' => 'left', 'thclass' => 'left',  ),
		  'youtube_ref' =>   array ( 'title' => LAN_REF, 'type' => 'text', 'data' => 'str', 'width' => 'auto', 'help' => LAN_YOUTUBE_01, 'readParms' => '', 'writeParms' => array('size'=>'xxlarge'), 'class' => 'left', 'thclass' => 'left',  ),
		  'youtube_title' =>   array ( 'title' => LAN_TITLE, 'type' => 'text', 'data' => 'str', 'width' => 'auto', 'inline' => true, 'help' => '', 'readParms' => '', 'writeParms' => array('size'=>'xxlarge'), 'class' => 'left', 'thclass' => 'left',  ),
		  'youtube_type' =>   array ( 'title' => LAN_TYPE, 'type' => 'dropdown', 'data' => 'str', 'width' => 'auto', 'batch' => true, 'filter' => true, 'inline' => true, 'help' => '', 'readParms' => '', 'writeParms' => '', 'class' => 'left', 'thclass' => 'left',  ),
		  'youtube_sef' =>   array ( 'title' => 'Sef', 'type' => 'text', 'data' => 'str', 'inline'=>true, 'width' => 'auto', 'help' => '', 'readParms' => '', 'writeParms' => array('size'=>'xxlarge', 'sef'=>'youtube_title'), 'class' => 'left', 'thclass' => 'left',  ),
		  'youtube_subscribe' =>   array ( 'title' => LAN_SUBSCRIBE, 'type' => 'text', 'data' => 'str', 'inline'=>true, 'width' => 'auto', 'help' => LAN_YOUTUBE_02, 'readParms' => '', 'writeParms' => array('size'=>'xxlarge'), 'class' => 'left', 'thclass' => 'left',  ),


		  	  	  'options' =>   array ( 'title' => LAN_OPTIONS, 'type' => null, 'data' => null, 'width' => '10%', 'thclass' => 'center last', 'class' => 'center last', 'forced' => '1',  ),
		);		
		
		protected $fieldpref = array('youtube_title', 'youtube_ref', 'youtube_type', 'youtube_sef');
		

	//	protected $preftabs        = array('General', 'Other' );
		protected $prefs = array(
		  'visibility' =>   array ( 'title' => LAN_VISIBILITY, 'type'=>'userclass', 'data' => 'int', 'width' => 'auto', 'help' => '', 'readParms' => '', 'writeParms' => '', 'class' => 'left', 'thclass' => 'left',  ),

		); 

	
		public function init()
		{
			// Set drop-down values (if any). 
		$this->fields['youtube_type']['writeParms']['optArray'] = array(LAN_USER, LAN_YOUTUBE_03, LAN_YOUTUBE_04); 
	
		}

		
		// ------- Customize Create --------
		
		public function beforeCreate($new_data,$old_data)
		{
			return $new_data;
		}
	
		public function afterCreate($new_data, $old_data, $id)
		{
			// do something
		}

		public function onCreateError($new_data, $old_data)
		{
			// do something		
		}		
		
		
		// ------- Customize Update --------
		
		public function beforeUpdate($new_data, $old_data, $id)
		{
			return $new_data;
		}

		public function afterUpdate($new_data, $old_data, $id)
		{
			// do something	
		}
		
		public function onUpdateError($new_data, $old_data, $id)
		{
			// do something		
		}	
			
}

class youtube_form_ui extends e_admin_form_ui
{

}		
		
		
new youtube_adminArea();

require_once(e_ADMIN."auth.php");
e107::getAdminUI()->runPage();

require_once(e_ADMIN."footer.php");
exit;

?>
