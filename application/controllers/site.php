<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Site extends CI_Controller 
{
	public function __construct( )
	{
		parent::__construct();
		
		$this->is_logged_in();
	}
	function is_logged_in( )
	{
		$is_logged_in = $this->session->userdata( 'logged_in' );
		if ( $is_logged_in !== 'true' || !isset( $is_logged_in ) ) {
			redirect( base_url() . 'index.php/login', 'refresh' );
		} //$is_logged_in !== 'true' || !isset( $is_logged_in )
	}
	function checkaccess($access)
	{
		$accesslevel=$this->session->userdata('accesslevel');
		if(!in_array($accesslevel,$access))
			redirect( base_url() . 'index.php/site?alerterror=You do not have access to this page. ', 'refresh' );
	}
	public function index()
	{
		$access = array("1","2");
		$this->checkaccess($access);
		$data[ 'page' ] = 'dashboard';
		$data[ 'title' ] = 'Welcome';
		$this->load->view( 'template', $data );	
	}
	public function createuser()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['accesslevel']=$this->user_model->getaccesslevels();
		$data[ 'status' ] =$this->user_model->getstatusdropdown();
		$data[ 'logintype' ] =$this->user_model->getlogintypedropdown();
//        $data['category']=$this->category_model->getcategorydropdown();
		$data[ 'page' ] = 'createuser';
		$data[ 'title' ] = 'Create User';
		$this->load->view( 'template', $data );	
	}
	function createusersubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','Name','trim|required|max_length[30]');
		$this->form_validation->set_rules('email','Email','trim|required|valid_email|is_unique[user.email]');
		$this->form_validation->set_rules('password','Password','trim|required|min_length[6]|max_length[30]');
		$this->form_validation->set_rules('confirmpassword','Confirm Password','trim|required|matches[password]');
		$this->form_validation->set_rules('accessslevel','Accessslevel','trim');
//		$this->form_validation->set_rules('status','status','trim|');
		$this->form_validation->set_rules('contact','contact','trim');
		$this->form_validation->set_rules('address','address','trim');
//		$this->form_validation->set_rules('json','json','trim');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data['accesslevel']=$this->user_model->getaccesslevels();
            $data[ 'status' ] =$this->user_model->getstatusdropdown();
            $data[ 'logintype' ] =$this->user_model->getlogintypedropdown();
            $data['category']=$this->category_model->getcategorydropdown();
            $data[ 'page' ] = 'createuser';
            $data[ 'title' ] = 'Create User';
            $this->load->view( 'template', $data );	
		}
		else
		{
            $name=$this->input->post('name');
            $email=$this->input->post('email');
            $password=$this->input->post('password');
            $accesslevel=$this->input->post('accesslevel');
//            $status=$this->input->post('status');
            $contact=$this->input->post('contact');
            $address=$this->input->post('address');
//            $json=$this->input->post('json');
//            $category=$this->input->post('category');
            
			if($this->user_model->create($name,$email,$password,$accesslevel,$contact,$address)==0)
			$data['alerterror']="New user could not be created.";
			else
			$data['alertsuccess']="User created Successfully.";
			$data['redirect']="site/viewusers";
			$this->load->view("redirect",$data);
		}
	}
    function viewusers()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['page']='viewusers';
        $data['base_url'] = site_url("site/viewusersjson");
        
		$data['title']='View Users';
		$this->load->view('template',$data);
	} 
    function viewusersjson()
	{
		$access = array("1");
		$this->checkaccess($access);
        
        
        $elements=array();
        $elements[0]=new stdClass();
        $elements[0]->field="`user`.`id`";
        $elements[0]->sort="1";
        $elements[0]->header="ID";
        $elements[0]->alias="id";
        
        
        $elements[1]=new stdClass();
        $elements[1]->field="`user`.`name`";
        $elements[1]->sort="1";
        $elements[1]->header="Name";
        $elements[1]->alias="name";
        
        $elements[2]=new stdClass();
        $elements[2]->field="`user`.`email`";
        $elements[2]->sort="1";
        $elements[2]->header="Email";
        $elements[2]->alias="email";
        
        $elements[3]=new stdClass();
        $elements[3]->field="`user`.`contact`";
        $elements[3]->sort="1";
        $elements[3]->header="Contact";
        $elements[3]->alias="contact";
        
        $elements[4]=new stdClass();
        $elements[4]->field="`user`.`timestamp`";
        $elements[4]->sort="1";
        $elements[4]->header="Timestamp";
        $elements[4]->alias="timestamp";
        
        $elements[5]=new stdClass();
        $elements[5]->field="`user`.`address`";
        $elements[5]->sort="1";
        $elements[5]->header="address";
        $elements[5]->alias="address";
       
        $elements[6]=new stdClass();
        $elements[6]->field="`accesslevel`.`name`";
        $elements[6]->sort="1";
        $elements[6]->header="Accesslevel";
        $elements[6]->alias="accesslevelname";
       
//        $elements[7]=new stdClass();
//        $elements[7]->field="`statuses`.`name`";
//        $elements[7]->sort="1";
//        $elements[7]->header="Status";
//        $elements[7]->alias="status";
       
        
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
       
        $data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `user` LEFT OUTER JOIN `accesslevel` ON `accesslevel`.`id`=`user`.`accesslevel` ");
        
		$this->load->view("json",$data);
	} 
    
    
	function edituser()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data[ 'status' ] =$this->user_model->getstatusdropdown();
		$data['accesslevel']=$this->user_model->getaccesslevels();
		$data[ 'logintype' ] =$this->user_model->getlogintypedropdown();
		$data['before']=$this->user_model->beforeedit($this->input->get('id'));
		$data['page']='edituser';
		$data['page2']='block/userblock';
		$data['title']='Edit User';
		$this->load->view('template',$data);
	}
	function editusersubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		
		$this->form_validation->set_rules('name','Name','trim|required|max_length[30]');
		$this->form_validation->set_rules('email','Email','trim|required|valid_email');
		$this->form_validation->set_rules('password','Password','trim|min_length[6]|max_length[30]');
		$this->form_validation->set_rules('confirmpassword','Confirm Password','trim|matches[password]');
		$this->form_validation->set_rules('accessslevel','Accessslevel','trim');
//		$this->form_validation->set_rules('status','status','trim|');
		$this->form_validation->set_rules('contact','contact','trim');
		$this->form_validation->set_rules('address','address','trim');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data[ 'status' ] =$this->user_model->getstatusdropdown();
			$data['accesslevel']=$this->user_model->getaccesslevels();
            $data[ 'logintype' ] =$this->user_model->getlogintypedropdown();
			$data['before']=$this->user_model->beforeedit($this->input->post('id'));
			$data['page']='edituser';
//			$data['page2']='block/userblock';
			$data['title']='Edit User';
			$this->load->view('template',$data);
		}
		else
		{
            
            $id=$this->input->get_post('id');
            $name=$this->input->get_post('name');
            $email=$this->input->get_post('email');
            $password=$this->input->get_post('password');
            $accesslevel=$this->input->get_post('accesslevel');
//            $status=$this->input->get_post('status');
            $contact=$this->input->get_post('contact');
            $address=$this->input->get_post('address');
//            $category=$this->input->get_post('category');
            
			if($this->user_model->edit($id,$name,$email,$password,$accesslevel,$contact,$address)==0)
			$data['alerterror']="User Editing was unsuccesful";
			else
			$data['alertsuccess']="User edited Successfully.";
			
			$data['redirect']="site/viewusers";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
			
		}
	}
	
	function deleteuser()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->user_model->deleteuser($this->input->get('id'));
//		$data['table']=$this->user_model->viewusers();
		$data['alertsuccess']="User Deleted Successfully";
		$data['redirect']="site/viewusers";
			//$data['other']="template=$template";
		$this->load->view("redirect",$data);
	}
	function changeuserstatus()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->user_model->changestatus($this->input->get('id'));
		$data['table']=$this->user_model->viewusers();
		$data['alertsuccess']="Status Changed Successfully";
		$data['redirect']="site/viewusers";
        $data['other']="template=$template";
        $this->load->view("redirect",$data);
	}
    
    //societyfacility
    function viewsocietyfacility()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['page']='viewsocietyfacility';
        $data['base_url'] = site_url("site/viewsocietyfacilityjson");
        
		$data['title']='View Society Facility';
		$this->load->view('template',$data);
	} 
    function viewsocietyfacilityjson()
	{
		$access = array("1");
		$this->checkaccess($access);
        
        
        $elements=array();
        $elements[0]=new stdClass();
        $elements[0]->field="`societyfacility`.`id`";
        $elements[0]->sort="1";
        $elements[0]->header="ID";
        $elements[0]->alias="id";
        
        
        $elements[1]=new stdClass();
        $elements[1]->field="`societyfacility`.`name`";
        $elements[1]->sort="1";
        $elements[1]->header="Name";
        $elements[1]->alias="name";
        
        
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
       
        $data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `societyfacility`");
        
		$this->load->view("json",$data);
	} 
    
    public function createsocietyfacility()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data[ 'page' ] = 'createsocietyfacility';
		$data[ 'title' ] = 'Create Society Facility';
		$this->load->view( 'template', $data );	
	}
	function createsocietyfacilitysubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','Name','trim|required');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
            $data[ 'page' ] = 'createsocietyfacility';
            $data[ 'title' ] = 'Create  Society Facility';
            $this->load->view( 'template', $data );	
		}
		else
		{
            $name=$this->input->post('name');
			if($this->societyfacility_model->create($name)==0)
			$data['alerterror']="New Society Facility could not be created.";
			else
			$data['alertsuccess']="Society Facility created Successfully.";
			$data['redirect']="site/viewsocietyfacility";
			$this->load->view("redirect",$data);
		}
	}
    
	function editsocietyfacility()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['page']='editsocietyfacility';
		$data['title']='Edit Society Facility';
		$data['before']=$this->societyfacility_model->beforeedit($this->input->get('id'));
		$this->load->view('template',$data);
	}
	function editsocietyfacilitysubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		
		$this->form_validation->set_rules('name','Name','trim|required');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data['page']='editsocietyfacility';
            $data['before']=$this->societyfacility_model->beforeedit($this->input->get('id'));
			$data['title']='Edit Society Facility';
			$this->load->view('template',$data);
		}
		else
		{
            
            $id=$this->input->get_post('id');
            $name=$this->input->get_post('name');
			if($this->societyfacility_model->edit($id,$name)==0)
			$data['alerterror']="Society Facility Editing was unsuccesful";
			else
			$data['alertsuccess']="Society Facility edited Successfully.";
			
			$data['redirect']="site/viewsocietyfacility";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
			
		}
	}
	
	function deletesocietyfacility()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->societyfacility_model->deletesocietyfacility($this->input->get('id'));
		$data['alertsuccess']="Society Facility Deleted Successfully";
		$data['redirect']="site/viewsocietyfacility";
		$this->load->view("redirect",$data);
	}
    
    //amenity
    function viewamenity()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['page']='viewamenity';
        $data['base_url'] = site_url("site/viewamenityjson");
        
		$data['title']='View Amenity';
		$this->load->view('template',$data);
	} 
    function viewamenityjson()
	{
		$access = array("1");
		$this->checkaccess($access);
        
        
        $elements=array();
        $elements[0]=new stdClass();
        $elements[0]->field="`amenity`.`id`";
        $elements[0]->sort="1";
        $elements[0]->header="ID";
        $elements[0]->alias="id";
        
        
        $elements[1]=new stdClass();
        $elements[1]->field="`amenity`.`name`";
        $elements[1]->sort="1";
        $elements[1]->header="Name";
        $elements[1]->alias="name";
        
        $elements[2]=new stdClass();
        $elements[2]->field="`amenity`.`image`";
        $elements[2]->sort="1";
        $elements[2]->header="Image";
        $elements[2]->alias="image";
        
        
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
       
        $data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `amenity`");
        
		$this->load->view("json",$data);
	} 
    
    public function createamenity()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data[ 'page' ] = 'createamenity';
		$data[ 'title' ] = 'Create Amenity';
		$this->load->view( 'template', $data );	
	}
	function createamenitysubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','Name','trim|required');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
            $data[ 'page' ] = 'createamenity';
            $data[ 'title' ] = 'Create Amenity';
            $this->load->view( 'template', $data );	
		}
		else
		{
            $name=$this->input->post('name');
            
            $config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$this->load->library('upload', $config);
			$filename="image";
			$image="";
			if (  $this->upload->do_upload($filename))
			{
				$uploaddata = $this->upload->data();
				$image=$uploaddata['file_name'];
                
                $config_r['source_image']   = './uploads/' . $uploaddata['file_name'];
                $config_r['maintain_ratio'] = TRUE;
                $config_t['create_thumb'] = FALSE;///add this
                $config_r['width']   = 800;
                $config_r['height'] = 800;
                $config_r['quality']    = 100;
                //end of configs

                $this->load->library('image_lib', $config_r); 
                $this->image_lib->initialize($config_r);
                if(!$this->image_lib->resize())
                {
                    echo "Failed." . $this->image_lib->display_errors();
                }  
                else
                {
                    $image=$this->image_lib->dest_image;
                }
                
			}
            
			if($this->amenity_model->create($name,$image)==0)
			$data['alerterror']="New Amenity could not be created.";
			else
			$data['alertsuccess']="Amenity created Successfully.";
			$data['redirect']="site/viewamenity";
			$this->load->view("redirect",$data);
		}
	}
    
	function editamenity()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['page']='editamenity';
		$data['title']='Edit Amenity';
		$data['before']=$this->amenity_model->beforeedit($this->input->get('id'));
		$this->load->view('template',$data);
	}
	function editamenitysubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		
		$this->form_validation->set_rules('name','Name','trim|required');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data['page']='editamenity';
            $data['before']=$this->amenity_model->beforeedit($this->input->get('id'));
			$data['title']='Edit Amenity';
			$this->load->view('template',$data);
		}
		else
		{
            
            $id=$this->input->get_post('id');
            $name=$this->input->get_post('name');
            
            $config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$this->load->library('upload', $config);
			$filename="image";
			$image="";
			if (  $this->upload->do_upload($filename))
			{
				$uploaddata = $this->upload->data();
				$image=$uploaddata['file_name'];
                
                $config_r['source_image']   = './uploads/' . $uploaddata['file_name'];
                $config_r['maintain_ratio'] = TRUE;
                $config_t['create_thumb'] = FALSE;///add this
                $config_r['width']   = 800;
                $config_r['height'] = 800;
                $config_r['quality']    = 100;
                //end of configs

                $this->load->library('image_lib', $config_r); 
                $this->image_lib->initialize($config_r);
                if(!$this->image_lib->resize())
                {
                    echo "Failed." . $this->image_lib->display_errors();
                }  
                else
                {
                    $image=$this->image_lib->dest_image;
                }
                
			}
            
            if($image=="")
            {
                $image=$this->amenity_model->getamenityimagebyid($id);
                $image=$image->image;
            }
            
			if($this->amenity_model->edit($id,$name,$image)==0)
			$data['alerterror']="Amenity Editing was unsuccesful";
			else
			$data['alertsuccess']="Amenity edited Successfully.";
			
			$data['redirect']="site/viewamenity";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
			
		}
	}
	
	function deleteamenity()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->amenity_model->deleteamenity($this->input->get('id'));
		$data['alertsuccess']="Amenity Deleted Successfully";
		$data['redirect']="site/viewamenity";
		$this->load->view("redirect",$data);
	}
    
    
    //builder
    function viewbuilder()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['page']='viewbuilder';
        $data['base_url'] = site_url("site/viewbuilderjson");
        
		$data['title']='View builder';
		$this->load->view('template',$data);
	} 
    function viewbuilderjson()
	{
		$access = array("1");
		$this->checkaccess($access);
        
        
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
    
    public function createbuilder()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data[ 'page' ] = 'createbuilder';
		$data[ 'title' ] = 'Create builder';
		$this->load->view( 'template', $data );	
	}
	function createbuildersubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','Name','trim|required');
		$this->form_validation->set_rules('email','email','trim');
		$this->form_validation->set_rules('contact','contact','trim');
		$this->form_validation->set_rules('address','address','trim');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
            $data[ 'page' ] = 'createbuilder';
            $data[ 'title' ] = 'Create builder';
            $this->load->view( 'template', $data );	
		}
		else
		{
            $name=$this->input->post('name');
            $email=$this->input->post('email');
            $contact=$this->input->post('contact');
            $address=$this->input->post('address');
            
			if($this->builder_model->create($name,$email,$contact,$address)==0)
			$data['alerterror']="New builder could not be created.";
			else
			$data['alertsuccess']="builder created Successfully.";
			$data['redirect']="site/viewbuilder";
			$this->load->view("redirect",$data);
		}
	}
    
	function editbuilder()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['page']='editbuilder';
		$data['title']='Edit builder';
		$data['before']=$this->builder_model->beforeedit($this->input->get('id'));
		$this->load->view('template',$data);
	}
	function editbuildersubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		
		$this->form_validation->set_rules('name','Name','trim|required');
		$this->form_validation->set_rules('email','email','trim');
		$this->form_validation->set_rules('contact','contact','trim');
		$this->form_validation->set_rules('address','address','trim');
        
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data['page']='editbuilder';
            $data['before']=$this->builder_model->beforeedit($this->input->get('id'));
			$data['title']='Edit builder';
			$this->load->view('template',$data);
		}
		else
		{
            
            $id=$this->input->get_post('id');
            $name=$this->input->get_post('name');
            $email=$this->input->post('email');
            $contact=$this->input->post('contact');
            $address=$this->input->post('address');
            
			if($this->builder_model->edit($id,$name,$email,$contact,$address)==0)
			$data['alerterror']="builder Editing was unsuccesful";
			else
			$data['alertsuccess']="builder edited Successfully.";
			
			$data['redirect']="site/viewbuilder";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
			
		}
	}
	
	function deletebuilder()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->builder_model->deletebuilder($this->input->get('id'));
		$data['alertsuccess']="Builder Deleted Successfully";
		$data['redirect']="site/viewbuilder";
		$this->load->view("redirect",$data);
	}
    
    
    //leasetype
    function viewleasetype()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['page']='viewleasetype';
        $data['base_url'] = site_url("site/viewleasetypejson");
        
		$data['title']='View leasetype';
		$this->load->view('template',$data);
	} 
    function viewleasetypejson()
	{
		$access = array("1");
		$this->checkaccess($access);
        
        
        $elements=array();
        $elements[0]=new stdClass();
        $elements[0]->field="`leasetype`.`id`";
        $elements[0]->sort="1";
        $elements[0]->header="ID";
        $elements[0]->alias="id";
        
        
        $elements[1]=new stdClass();
        $elements[1]->field="`leasetype`.`name`";
        $elements[1]->sort="1";
        $elements[1]->header="Name";
        $elements[1]->alias="name";
        
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
       
        $data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `leasetype`");
        
		$this->load->view("json",$data);
	} 
    
    public function createleasetype()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data[ 'page' ] = 'createleasetype';
		$data[ 'title' ] = 'Create leasetype';
		$this->load->view( 'template', $data );	
	}
	function createleasetypesubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','Name','trim|required');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
            $data[ 'page' ] = 'createleasetype';
            $data[ 'title' ] = 'Create leasetype';
            $this->load->view( 'template', $data );	
		}
		else
		{
            $name=$this->input->post('name');
            
			if($this->leasetype_model->create($name)==0)
			$data['alerterror']="New Lease Type could not be created.";
			else
			$data['alertsuccess']="Lease Type created Successfully.";
			$data['redirect']="site/viewleasetype";
			$this->load->view("redirect",$data);
		}
	}
    
	function editleasetype()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['page']='editleasetype';
		$data['title']='Edit Lease Type';
		$data['before']=$this->leasetype_model->beforeedit($this->input->get('id'));
		$this->load->view('template',$data);
	}
	function editleasetypesubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		
		$this->form_validation->set_rules('name','Name','trim|required');
        
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data['page']='editleasetype';
            $data['before']=$this->leasetype_model->beforeedit($this->input->get('id'));
			$data['title']='Edit Lease Type';
			$this->load->view('template',$data);
		}
		else
		{
            
            $id=$this->input->get_post('id');
            $name=$this->input->get_post('name');
            
			if($this->leasetype_model->edit($id,$name)==0)
			$data['alerterror']="Lease Type Editing was unsuccesful";
			else
			$data['alertsuccess']="Lease Type edited Successfully.";
			
			$data['redirect']="site/viewleasetype";
			$this->load->view("redirect",$data);
			
		}
	}
	
	function deleteleasetype()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->leasetype_model->deleteleasetype($this->input->get('id'));
		$data['alertsuccess']="Lease Type Deleted Successfully";
		$data['redirect']="site/viewleasetype";
		$this->load->view("redirect",$data);
	}
    
    
    //propertytype
    function viewpropertytype()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['page']='viewpropertytype';
        $data['base_url'] = site_url("site/viewpropertytypejson");
        
		$data['title']='View Property Type';
		$this->load->view('template',$data);
	} 
    function viewpropertytypejson()
	{
		$access = array("1");
		$this->checkaccess($access);
        
        
        $elements=array();
        $elements[0]=new stdClass();
        $elements[0]->field="`propertytype`.`id`";
        $elements[0]->sort="1";
        $elements[0]->header="ID";
        $elements[0]->alias="id";
        
        
        $elements[1]=new stdClass();
        $elements[1]->field="`propertytype`.`name`";
        $elements[1]->sort="1";
        $elements[1]->header="Name";
        $elements[1]->alias="name";
        
        
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
       
        $data["message"]=$this->chintantable->query($pageno,$maxrow,$orderby,$orderorder,$search,$elements,"FROM `propertytype`");
        
		$this->load->view("json",$data);
	} 
    
    public function createpropertytype()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data[ 'page' ] = 'createpropertytype';
		$data[ 'title' ] = 'Create Property Type';
		$this->load->view( 'template', $data );	
	}
	function createpropertytypesubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','Name','trim|required');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
            $data[ 'page' ] = 'createpropertytype';
            $data[ 'title' ] = 'Create  Property Type';
            $this->load->view( 'template', $data );	
		}
		else
		{
            $name=$this->input->post('name');
			if($this->propertytype_model->create($name)==0)
			$data['alerterror']="New Property Type could not be created.";
			else
			$data['alertsuccess']="Property Type created Successfully.";
			$data['redirect']="site/viewpropertytype";
			$this->load->view("redirect",$data);
		}
	}
    
	function editpropertytype()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['page']='editpropertytype';
		$data['title']='Edit Property Type';
		$data['before']=$this->propertytype_model->beforeedit($this->input->get('id'));
		$this->load->view('template',$data);
	}
	function editpropertytypesubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		
		$this->form_validation->set_rules('name','Name','trim|required');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data['page']='editpropertytype';
            $data['before']=$this->propertytype_model->beforeedit($this->input->get('id'));
			$data['title']='Edit Property Type';
			$this->load->view('template',$data);
		}
		else
		{
            
            $id=$this->input->get_post('id');
            $name=$this->input->get_post('name');
			if($this->propertytype_model->edit($id,$name)==0)
			$data['alerterror']="Property Type Editing was unsuccesful";
			else
			$data['alertsuccess']="Property Type edited Successfully.";
			
			$data['redirect']="site/viewpropertytype";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
			
		}
	}
	
	function deletepropertytype()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->propertytype_model->deletepropertytype($this->input->get('id'));
		$data['alertsuccess']="Property Type Deleted Successfully";
		$data['redirect']="site/viewpropertytype";
		$this->load->view("redirect",$data);
	}
    
    
     
    //property
    function viewproperty()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['page']='viewproperty';
        $data['base_url'] = site_url("site/viewpropertyjson");
        
		$data['title']='View property';
		$this->load->view('template',$data);
	} 
    function viewpropertyjson()
	{
		$access = array("1");
		$this->checkaccess($access);
        
        
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
    
    public function createproperty()
	{
		$access = array("1");
		$this->checkaccess($access);
        $data['category']=$this->property_model->getcategorydropdown();
        $data['builder']=$this->builder_model->getbuilderdropdown();
        $data['listingowner']=$this->user_model->getlistingownerdropdown();
        $data['listedby']=$this->property_model->getlistedbydropdown();
        $data['furnishing']=$this->property_model->getfurnishingdropdown();
        $data['leasetype']=$this->leasetype_model->getleasetypedropdown();
        $data['propertytype']=$this->propertytype_model->getpropertytypedropdown();
        $data['status']=$this->property_model->getstatusdropdown();
        $data['negotiable']=$this->property_model->getnegotiabledropdown();
        $data['bathroom']=$this->property_model->getbathroomdropdown();
        $data['bhk']=$this->property_model->getbhkdropdown();
        $data['iscommercial']=$this->property_model->getiscommercialdropdown();
        $data['verified']=$this->property_model->getverifieddropdown();
		$data[ 'page' ] = 'createproperty';
		$data[ 'title' ] = 'Create property';
		$this->load->view( 'template', $data );	
	}
	function createpropertysubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->form_validation->set_rules('name','Name','trim|required');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
            $data['category']=$this->property_model->getcategorydropdown();
            $data['builder']=$this->builder_model->getbuilderdropdown();
            $data['listingowner']=$this->user_model->getlistingownerdropdown();
            $data['listedby']=$this->property_model->getlistedbydropdown();
            $data['furnishing']=$this->property_model->getfurnishingdropdown();
            $data['leasetype']=$this->leasetype_model->getleasetypedropdown();
            $data['propertytype']=$this->propertytype_model->getpropertytypedropdown();
            $data['status']=$this->property_model->getstatusdropdown();
            $data['negotiable']=$this->property_model->getnegotiabledropdown();
            $data['bathroom']=$this->property_model->getbathroomdropdown();
            $data['bhk']=$this->property_model->getbhkdropdown();
            $data['iscommercial']=$this->property_model->getiscommercialdropdown();
            $data['verified']=$this->property_model->getverifieddropdown();
            $data[ 'page' ] = 'createproperty';
            $data[ 'title' ] = 'Create property';
            $this->load->view( 'template', $data );	
		}
		else
		{
            $name=$this->input->post('name');
            
            $config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$this->load->library('upload', $config);
			$filename="image";
			$image="";
			if (  $this->upload->do_upload($filename))
			{
				$uploaddata = $this->upload->data();
				$image=$uploaddata['file_name'];
                
                $config_r['source_image']   = './uploads/' . $uploaddata['file_name'];
                $config_r['maintain_ratio'] = TRUE;
                $config_t['create_thumb'] = FALSE;///add this
                $config_r['width']   = 800;
                $config_r['height'] = 800;
                $config_r['quality']    = 100;
                //end of configs

                $this->load->library('image_lib', $config_r); 
                $this->image_lib->initialize($config_r);
                if(!$this->image_lib->resize())
                {
                    echo "Failed." . $this->image_lib->display_errors();
                }  
                else
                {
                    $image=$this->image_lib->dest_image;
                }
                
			}
            
			if($this->property_model->create($name,$image)==0)
			$data['alerterror']="New property could not be created.";
			else
			$data['alertsuccess']="property created Successfully.";
			$data['redirect']="site/viewproperty";
			$this->load->view("redirect",$data);
		}
	}
    
	function editproperty()
	{
		$access = array("1");
		$this->checkaccess($access);
		$data['page']='editproperty';
		$data['title']='Edit property';
		$data['before']=$this->property_model->beforeedit($this->input->get('id'));
		$this->load->view('template',$data);
	}
	function editpropertysubmit()
	{
		$access = array("1");
		$this->checkaccess($access);
		
		$this->form_validation->set_rules('name','Name','trim|required');
		if($this->form_validation->run() == FALSE)	
		{
			$data['alerterror'] = validation_errors();
			$data['page']='editproperty';
            $data['before']=$this->property_model->beforeedit($this->input->get('id'));
			$data['title']='Edit property';
			$this->load->view('template',$data);
		}
		else
		{
            
            $id=$this->input->get_post('id');
            $name=$this->input->get_post('name');
            
            $config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$this->load->library('upload', $config);
			$filename="image";
			$image="";
			if (  $this->upload->do_upload($filename))
			{
				$uploaddata = $this->upload->data();
				$image=$uploaddata['file_name'];
                
                $config_r['source_image']   = './uploads/' . $uploaddata['file_name'];
                $config_r['maintain_ratio'] = TRUE;
                $config_t['create_thumb'] = FALSE;///add this
                $config_r['width']   = 800;
                $config_r['height'] = 800;
                $config_r['quality']    = 100;
                //end of configs

                $this->load->library('image_lib', $config_r); 
                $this->image_lib->initialize($config_r);
                if(!$this->image_lib->resize())
                {
                    echo "Failed." . $this->image_lib->display_errors();
                }  
                else
                {
                    $image=$this->image_lib->dest_image;
                }
                
			}
            
            if($image=="")
            {
                $image=$this->property_model->getpropertyimagebyid($id);
                $image=$image->image;
            }
            
			if($this->property_model->edit($id,$name,$image)==0)
			$data['alerterror']="property Editing was unsuccesful";
			else
			$data['alertsuccess']="property edited Successfully.";
			
			$data['redirect']="site/viewproperty";
			//$data['other']="template=$template";
			$this->load->view("redirect",$data);
			
		}
	}
	
	function deleteproperty()
	{
		$access = array("1");
		$this->checkaccess($access);
		$this->property_model->deleteproperty($this->input->get('id'));
		$data['alertsuccess']="property Deleted Successfully";
		$data['redirect']="site/viewproperty";
		$this->load->view("redirect",$data);
	}
    
    
    
}
?>