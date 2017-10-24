<?php
defined('BASEPATH') OR exit('No direct script access allowed');


Class User extends Frontend_Controller { 
    
    
    public function init() { 
        
    var_dump($this->user_elastic->checkIndexExist());    

    $this->load->view('header/header', $this->data);
    }
    
    
   public function show() { 
       
   }
  
    
    public function registrations(){ 
        
        $this->load->view('header/header',$this->data);
        $this->load->view('user/registration');
    }
    
    public function registration() { 
        
        if(!$this->input->is_ajax_request()) {
            exit("not valid reuest");
        }
        
        $form_rules = array(
        array(
                'field' => 'username',
                'label' => 'Username',
                'rules' => 'trim|required|min_length[5]|max_length[12]|callback_username_check'
        ),
        array(  
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'trim|required|min_length[8]',
                'errors' => array(
                        'required' => 'You must provide a %s.',
                ),
        ),
        array(
                'field' => 'passconf',
                'label' => 'Password Confirmation',
                'rules' => 'trim|required|matches[password]'
        ),
        array(
                'field' => 'email',
                'label' => 'Email',
                'rules' => 'trim|required|valid_email'
        )
       );
        $this->form_validation->set_rules($form_rules);
        if ($this->form_validation->run() === True)
                {
                    $this->user_elastic->setName($this->input->post('username'));                    
                    $this->user_elastic->setEmail($this->input->post('email'));  
                    $this->user_elastic->setPassword($this->input->post('password'));   
                  
                   $this->user_elastic->createUpdateUser();
      
                        echo '<div class="container-fluid"><div class="col-md-14" style="background-color:green;">
                                 Your web account is submited
                                </div></div>';
                }
                else
                {
                        echo '<div class="container-fluid"><div class="col-md-14" style="background-color:pink;">'
                                  .validation_errors().
                                '</div></div>';
                }
        
       
    }
    public function username_check($str)
        {
               if ($this->user_elastic->checkUserName($str))
                {
                        return True;     
                }else
                {
                        $this->form_validation->set_message('username_check', 'The name '.$str.' is already occupied');
                        return False;              
                } 
        }
        
      public function logins(){
        
        $this->load->view('header/header',$this->data);
        $this->load->view('user/login');
           
        }
        
      public function login() { 
        
       $form_rules = array(
        array(
                'field' => 'username',
                'label' => 'Username',
                'rules' => 'trim|required|min_length[5]|max_length[12]'
        ),
        array(
                'field' => 'password',
                'label' => 'Password',
                 'rules' => 'trim|required|min_length[8]',
                'errors' => array(
                        'required' => 'You must provide a %s.',
                ),
        )
       );
       $this->form_validation->set_rules($form_rules);
        if ($this->form_validation->run() === True){
            $this->user_elastic->setPassword($this->input->post('password'));
            $this->user_elastic->setName($this->input->post('username'));
           if($this->user_elastic->logIn($this->input->post('username')) == 0){
              $newLoginUser = array(
           'username'  => $this->user_elastic->getName(),
           'email'     => $this->user_elastic->getEmail(),
           'logged_in' => TRUE
          );
           $this->session->set_userdata($newLoginUser); 
           
            echo '<div class="container-fluid"><div class="col-md-14" style="background-color:green;">
                                 You are logged in
                                </div></div>';
           } else { 
                  echo '<div class="container-fluid"><div class="col-md-14" style="background-color:pink;">
                                  wrong password or username
                                </div></div>';
           }
        }else {
               echo '<div class="container-fluid"><div class="col-md-14" style="background-color:pink;">'
                                  .validation_errors().
                                '</div></div>';
        }
               }    
    
    public function logout() { 
    $userToUnset = ['username','email','logged_in'];  
    $this->session->unset_userdata($userToUnset);
    $this->load->view('header/header',$this->data);
    
    }
    
    public function create() { 
        
    }
    
    public function delate() { 
        
    }
   
    public function update() { 
        
    }
    
    
}