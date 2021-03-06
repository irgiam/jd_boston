<?php
/**
 * @version		$Id: controller.php $
 * @package		Joomla.Site
 * @subpackage	com_testimonies
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		kiennh
 * This component was generated by http://xipat.com/ - 2015
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Testimonies Component Controller
 *
 * @package		Joomla.Site
 * @subpackage	com_testimonies
 * @since		1.5
 */
class TestimoniesController extends JControllerLegacy
{
	public $default_view = 'list';
	
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$cachable = true;

		$safeurlparams = array('catid'=>'INT','id'=>'INT','cid'=>'ARRAY','year'=>'INT','month'=>'INT','limit'=>'INT','limitstart'=>'INT',
			'showall'=>'INT','return'=>'BASE64','filter'=>'STRING','filter_order'=>'CMD','filter_order_Dir'=>'CMD','filter-search'=>'STRING','print'=>'BOOLEAN','lang'=>'CMD');

		parent::display($cachable, $safeurlparams);

		return $this;
	}
    
    public function save() {
        
        $mainframe = JFactory::getApplication(); 
        $data = new StdClass;
        $model=$this->getModel('list');
        $post=JRequest::getVar('jform','','post','',JREQUEST_ALLOWRAW);
        //$data->id=$post['id'];
        $data->name=$post['name'];
        $data->email=$post['email'];
        $data->company_name=$post['company_name'];
        $data->rating=$post['rating'];
        $data->position=$post['position'];
        $data->website_url=$post['website_url'];
        $data->comment=$post['comment'];
        $data->state=0;
        $data->created=date("Y-m-d h:i:s"); 

       $code= JRequest::get('recaptcha_response_field');   
       if($post['captcha']) {  
           JPluginHelper::importPlugin('captcha');
           $dispatcher = JDispatcher::getInstance();
           $res = $dispatcher->trigger('onCheckAnswer',$code);
           if(!$res[0]){
                $message = JText::_('<span style="font-size: 14px;font-weight:bold;color: red;">'.JText::_("TESTIMONIAL_CAPTCHA_MESSAGE").'</span>');
                $this->setRedirect('index.php?option=com_testimonies', $message);
                return false;
           } 
       }
                     
        //upload avatar
        $jinput = JFactory::getApplication()->input;
        $files = $jinput->files->get('jform');
        $file = $files['avatar']; 
        $avatar = $this->upload($file);
        $data->avatar = $avatar; 
        
        // save to db 
        $id = $model->save('#__testimonies',$data,'');
        
                
        // Send email to admin
        $MailFrom     = $mainframe->getCfg('mailfrom');
        $FromName     = $mainframe->getCfg('fromname');
        $params = JComponentHelper::getParams('com_testimonies');  
        $rec = explode(',',$params->get('recipient_email'));

        // Prepare email body
        $prefix = JText::sprintf('ENQUIRY_TEXT', JURI::base());                
        $subject    = 'New personal testimonial has been submitted';  
        $approve_link = JUri::base().'administrator/index.php?option=com_testimonies&view=item&layout=edit&id='.$id;
        
        $body     = sprintf(JText::_('TESTIMONIAL_MESSAGE'),$data->name,$data->email,$data->company_name,$data->website_url,$data->comment,$approve_link);

        $mail = JFactory::getMailer();

        $mail->addRecipient( $rec );
        $mail->setSender( array( $data->email, $data->name ) );
        $mail->setSubject($subject );
        $mail->setBody( $body );
        $sent = $mail->Send();
           
      //  $msg = JText::_( 'Thank you for submit testimonial! The testimonial will be reviewed and approved.');
        $link = JRoute::_('index.php?option=com_testimonies&layout=thankyou', false);
        $this->setRedirect($link, $msg);        
    }
    
    public function upload($files)
    {        
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');  

        $path = JPATH_SITE.'/'."images/testimonies";   
        if (!JFolder::exists($path)){
             JFolder::create($path, 0755);          
        }   
                
        //check for filesize
        $fileSize = $files['size'];
        if($fileSize > 2000000)
        {
            echo JText::_( 'FILE BIGGER THAN 2MB' );
            return; 
        }
         
        //check the file extension is ok
        $fileName = $files['name'];
        $uploadedFileNameParts = explode('.',$fileName);
        $uploadedFileExtension = array_pop($uploadedFileNameParts);
         
        $validFileExts = explode(',', 'jpeg,jpg,png,gif');
         
        //assume the extension is false until we know its ok
        $extOk = false;
         
        //go through every ok extension, if the ok extension matches the file extension (case insensitive)
        //then the file extension is ok
        foreach($validFileExts as $key => $value)
        {
            if( preg_match("/$value/i", $uploadedFileExtension ) )
            {
                $extOk = true;
            }
        }
         
        if ($extOk == false) 
        {
            echo JText::_( 'INVALID EXTENSION' );
                return;
        }
         
        //the name of the file in PHP's temp directory that we are going to move to our folder
        $fileTemp = $files['tmp_name'];
         
        //for security purposes, we will also do a getimagesize on the temp file (before we have moved it 
        //to the folder) to check the MIME type of the file, and whether it has a width and height
        $imageinfo = getimagesize($fileTemp);
         
        //we are going to define what file extensions/MIMEs are ok, and only let these ones in (whitelisting), rather than try to scan for bad
        //types, where we might miss one (whitelisting is always better than blacklisting) 
        $okMIMETypes = 'image/jpeg,image/pjpeg,image/png,image/x-png,image/gif';
        $validFileTypes = explode(",", $okMIMETypes);        
         
        //if the temp file does not have a width or a height, or it has a non ok MIME, return
        if( !is_int($imageinfo[0]) || !is_int($imageinfo[1]) ||  !in_array($imageinfo['mime'], $validFileTypes) )
        {
            echo JText::_( 'INVALID FILETYPE' );
                return;
        }
         
        //lose any special characters in the filename
        $fileName = preg_replace("/[^A-Za-z0-9]/i", ".", $fileName);        
            
        $uploadPath = JPATH_ROOT.'/images/testimonies/'.$fileName;
        
        if(!JFile::upload($fileTemp, $uploadPath)) 
            {
                echo JText::_( 'ERROR MOVING FILE' );
                return;
            }  
        return $fileName;   

    }   
                
}
