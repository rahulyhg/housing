<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Json extends CI_Controller 
{
	
	public function getpropertybyid()
    {
        $id=$this->input->get_post('id');
        $data['message']=$this->property_model->getpropertybyid($id);
		$this->load->view('json',$data);
    }
    
	public function getbuilderbyid()
    {
        $id=$this->input->get_post('id');
        $data['message']=$this->builder_model->beforeedit($id);
		$this->load->view('json',$data);
    }
    
    function getallbuilders()
	{
        $elements=array();
        $elements[0]=new stdClass();
        $elements[0]->field="`builder`.`id`";
        $elements[0]->sort="1";
        $elements[0]->header="ID";
        $elements[0]->alias="id";
        
        
        $elements[1]=new stdClass();
        $elements[1]->field="`builder`.`name`";
        $elements[1]->sort="1";
        $elements[1]->header="Name";
        $elements[1]->alias="name";
        
        $elements[2]=new stdClass();
        $elements[2]->field="`builder`.`email`";
        $elements[2]->sort="1";
        $elements[2]->header="Email";
        $elements[2]->alias="email";
        
        $elements[3]=new stdClass();
        $elements[3]->field="`builder`.`contact`";
        $elements[3]->sort="1";
        $elements[3]->header="Contact";
        $elements[3]->alias="contact";
        
        $elements[4]=new stdClass();
        $elements[4]->field="`builder`.`address`";
        $elements[4]->sort="1";
        $elements[4]->header="Address";
        $elements[4]->alias="address";
        
        
        $search=$this->input->get_post("search");
        $pageno=$this->input->get_post("pageno");
        $orderby=$this->input->get_post("orderby");
        $orderorder=$this->input->get_post("orderorder");
        $maxrow=$this->input->get_post("maxrow");
        if($maxrow=="")
        {
            $maxrow=20;
        }
        
        if($orderby=="")
        {
            $orderby="id";
            $orderorder="ASC";
        }
       
        $data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `builder`");
        
		$this->load->view("json",$data);
	} 
    
    
    function getallproperties()
	{
        $elements=array();
        $elements[0]=new stdClass();
        $elements[0]->field="`property`.`id`";
        $elements[0]->sort="1";
        $elements[0]->header="ID";
        $elements[0]->alias="id";
        
        
        $elements[1]=new stdClass();
        $elements[1]->field="`property`.`name`";
        $elements[1]->sort="1";
        $elements[1]->header="Name";
        $elements[1]->alias="name";
        
        $elements[2]=new stdClass();
        $elements[2]->field="`property`.`email`";
        $elements[2]->sort="1";
        $elements[2]->header="email";
        $elements[2]->alias="email";
        
        $elements[3]=new stdClass();
        $elements[3]->field="`category`.`name`";
        $elements[3]->sort="1";
        $elements[3]->header="Category";
        $elements[3]->alias="categoryname";
        
        $elements[4]=new stdClass();
        $elements[4]->field="`builder`.`name`";
        $elements[4]->sort="1";
        $elements[4]->header="Builder";
        $elements[4]->alias="buildername";
        
        $elements[5]=new stdClass();
        $elements[5]->field="`property`.`price`";
        $elements[5]->sort="1";
        $elements[5]->header="Price";
        $elements[5]->alias="price";
        
        $elements[6]=new stdClass();
        $elements[6]->field="`property`.`bhk`";
        $elements[6]->sort="1";
        $elements[6]->header="BHK";
        $elements[6]->alias="bhk";
        
        $elements[7]=new stdClass();
        $elements[7]->field="`property`.`address1`";
        $elements[7]->sort="1";
        $elements[7]->header="Address1";
        $elements[7]->alias="address1";
        
        $elements[8]=new stdClass();
        $elements[8]->field="`property`.`address2`";
        $elements[8]->sort="1";
        $elements[8]->header="Address2";
        $elements[8]->alias="address2";
        
        $elements[9]=new stdClass();
        $elements[9]->field="`property`.`city`";
        $elements[9]->sort="1";
        $elements[9]->header="City";
        $elements[9]->alias="city";
        
        $elements[10]=new stdClass();
        $elements[10]->field="`property`.`builduparea`";
        $elements[10]->sort="1";
        $elements[10]->header="builduparea";
        $elements[10]->alias="builduparea";
        
        $elements[11]=new stdClass();
        $elements[11]->field="`property`.`carpetarea`";
        $elements[11]->sort="1";
        $elements[11]->header="carpetarea";
        $elements[11]->alias="carpetarea";
        
        
        $search=$this->input->get_post("search");
        $pageno=$this->input->get_post("pageno");
        $orderby=$this->input->get_post("orderby");
        $orderorder=$this->input->get_post("orderorder");
        $maxrow=$this->input->get_post("maxrow");
        if($maxrow=="")
        {
            $maxrow=20;
        }
        
        if($orderby=="")
        {
            $orderby="id";
            $orderorder="ASC";
        }
       
        $data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `property` LEFT OUTER JOIN `category` ON `category`.`id`=`property`.`category` LEFT OUTER JOIN `builder` ON `builder`.`id`=`property`.`builder`");
        
		$this->load->view("json",$data);
	} 
    
    
    public function addenquiry()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $userid=$data['userid'];
        $propertyid=$data['propertyid'];
        $message=$data['message'];
        $email=$data['email'];
        $contact=$data['contact'];
        $data['message']=$this->propertyenquiry_model->addfrontendenquiry($userid,$propertyid,$message,$email,$contact);
        $this->load->view('json',$data);
    
    }
    
    
	public function gettestimonial()
    {
        $data['message']=$this->testimonial_model->gettestimonial();
		$this->load->view('json',$data);
    }
	public function getsearchbackground()
    {
        $data['message']=$this->config_model->getsearchbackground();
		$this->load->view('json',$data);
    }
    
	public function getnewproperty()
    {
        $data['message']=$this->property_model->getnewproperty();
		$this->load->view('json',$data);
    }
	public function getvideo()
    {
        $data['message']=$this->video_model->getvideo();
		$this->load->view('json',$data);
    }
    
}   
//EndOfFile
?>