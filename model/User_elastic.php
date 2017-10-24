<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
Class User_elastic {
    
    private $id;
    private $name; 
    private $email;
    private $password; 
    private $adddata;
    private $updatedata; 
    private $curentID;
    protected $cI;
    
   function setCurentId($cur_id){
        $this->curentID =$cur_id+1;
    }
    function getCurentId(){
        return $this->curentID;
    }
    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getEmail() {
        return $this->email;
    }

    function getPassword() {
        return $this->password;
    }

    function getAdddata() {
        return $this->adddata;
    }

    function getUpdatedata() {
        return $this->updatedata;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setEmail($email) {
        $this->email = $email;
    }

   function setPassword($password) {
       $this->cI->encryption->initialize(
        array(
                'cipher' => 'aes-128',
                'mode' => 'CFB'   
        )
             );
       $this->password = $this->cI->encryption->encrypt($password);
    }

    function setAdddata($adddata) {
        $this->adddata = $adddata;
    }

    function setUpdatedata($updatedata) {
        $this->updatedata = $updatedata;
    }
    
    public function passwordConfirm($confirmPassword) {
     
      return strcmp($this->cI->encryption->decrypt($this->getPassword()),$this->cI->encryption->decrypt($confirmPassword));
    }

    const mapingIndex = 'user_index'; 
    const mapingType = 'my_type';
    
    public function __construct() {
       $this->cI =& get_instance();
       $this->setId(-1);
       $this->setAdddata('');
       $this->setEmail('');
       $this->setName('');
       $this->setPassword('');
       $this->setUpdatedata(NULL);
       $this->setUpdatedata(NUll);  
    
    }
    
    public static function eventsUser() { 
        $this->cI->events->valuableRegister();
    }
    
    
    public function userInit() { 
    $conn = Create::connection(); 
    $params = [
    'index' => self::mapingIndex,
    'body' => [
        'settings' => [
            'number_of_shards' => 3,
            'number_of_replicas' => 2
        ],
        'mappings' => [
            self::mapingType => [
                '_source' => [
                    'enabled' => true
                ],
                'properties' => [
                    'name' => [
                        'type' => 'text'
                    ],
                    'email' => [
                        'type' => 'text'
                    ],
                    'password' => [
                        'type' => 'text'
                    ],
                    'adddate' => [ 
                        'type' => 'date', 
                        'format' => 'Y-m-d H:m:s'                           
                        ],
                    'updatedate' => [
                        'type' => 'date', 
                        'format' => 'Y-m-d H:m:s'
                    ],
                    'id' => [
                       'type' => 'long'
                        ]
                    ]
                ]
            ]
        ]
    ];
    $response = $conn->indices()->create($params);
    return $response;
    }
    
    
    public function getMappingUser() { 
        $conn = Create::connection();
        $params = ['index' => self::mapingIndex];
        $response = $conn->indices()->getMapping($params);
    
        return $response;
    }
    public static function getMappingUserType() { 
        $conn = Create::connection(); 
        $params = ['type' =>self::mapingType];
        $response = $conn->indices()->getMapping($params);
        
        return $response;
    }
    
    public function deleteIndex() {  
        $conn = Create::connection();
        $params = ['index' => self::mapingIndex];
        $response = $conn->indices()->delete($params);
        return $response;    
        
    }

    public static function initUbdate() { 
        
        $conn = Create::connection();
        $params = [
        'index' => self::mapingIndex,
        'type' => self::mapingType,
        'body' => [
          'mapping' =>  [ 
            'properties' =>  
               ['id' => ['type' => 'long']]
              ]
           ]
         ];
      // Update the index mapping
     $reaults = $conn->indices()->putMapping($params);
      return $reaults; 
    }
    
    public static function searchLastMaxId(){ 
        $conn = Create::connection();         
        $id = NULL;
        $params = [
    'index' => 'user_index',
    'type' => 'my_type',
    'body' => [
        
        'sort' => ['id' => ['order'=>'desc',
             'mode' => 'min'] ], 
        ]
       ]; 
        $results = $conn->search($params);
        if ($results['hits']['total'] >= 1) {  
          $id = $results = $results['hits']['hits'];
       } else { 
          return $id = -1;
       }
      return (int)$id[0]['_id'];
    }
    public function getIdMax() { 
     
     $this->setCurentId(self::searchLastMaxId());
    //  return self::searchLastMaxId();
     return $this->getCurentId();
    }
   public function createUpdateUser() { 
        $this->setCurentId(self::searchLastMaxId());
       
         $conn = Create::connection();
         if($this->getId() == -1){ 
          $created = $conn->index([
          'index' => self::mapingIndex, 
          'type' => self::mapingType, 
          'id' => $this->getCurentId(),
          'body' => [   
              'name' => $this->getName(), 
              'email' => $this->getEmail(), 
              'password' => $this->getPassword(),
              'adddata' => date("Y-m-d H:m:s"), 
              'updatedate' => date("y-m-y H:m:s"),
              'id' => $this->getCurentId()
              ]
      ]);
          return $created;
         } else { 
          $updated = $conn->update([
           'index' => self::mapingIndex, 
          'type' => self::mapingType, 
          'id' => $first,
          'body' => [   
              'name' => $this->getName(), 
              'email' => $this->getEmail(), 
              'password' => $this->getPassword(),
              'adddata' => $this->getAdddata(), 
              'updatedate' => date("y-m-y H:m:s"),
              'id' => $this->getId()
              ] 
          ]);
          
          return $updated;
         }
     return false;
    }
    
    public function checkUserName($username){ 
    $conn = Create::connection();
    $params = [
    'index' => self::mapingIndex,
    'type' => self::mapingType,
    'body' => [
        'query' => [
            'match' => [
                'name' => $username
            ]
        ]
    ]
];
    $results = $conn->search($params);
     if ($results['hits']['total'] >= 1) {  
       $results = $results['hits']['hits'];
       return false;   
       }else {
       return true;
     }
   return true; 
    }
   public function logIn($username) { 
 
    $conn = Create::connection();
    $params = [
    'index' => self::mapingIndex,
    'type' => self::mapingType,
    'body' => [
        'query' => [
            'match' => [
                'name' => $username
            ]
        ]
    ]
]; 
    $results = $conn->search($params);
     if ($results['hits']['total'] >= 1) {  
       $results = $results['hits']['hits'];  
       $this->setName($results[0]['_source']['name']);
       $this->setEmail($results[0]['_source']['email']);
     return $this->cI->events->trigger('password',$results[0]['_source']['password'],'string'); 
     }else {
         return false;
     }
   }
    
   
   public static function checkIndexExist() {   
    $conn = Create::connection();
    $params = [
    'index' => self::mapingIndex
    ];
    try {
     $results = $conn->search($params);
    }
    catch(Exception $e) {
    if(json_decode($e->getMessage())->error->root_cause[0]->type == 'index_not_found_exception'){
       return false;
    }
      return false;
    }
    return true; 
  }

  
}
 
    
