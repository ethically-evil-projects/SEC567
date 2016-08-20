<?php
//DB SESSION CLASS
class DbSession
{
    /**
     * @var \Pdo Db Object
     */
    private $db;

    // ================
    // DB SESSION CLASS
    // =================
    public function __construct(\PDO $db){
        // Instantiate new Database object
        $this->db = $db;
        // Set handler to overide SESSION
       session_set_save_handler(
         array($this, "_open"),
         array($this, "_close"),
         array($this, "_read"),
         array($this, "_write"),
         array($this, "_destroy"),
         array($this, "_gc")
       );

       // Start the session
       @session_start();
    }

     // =====
     // OPEN
     // =====
    public function _open(){
        // If successful
        if($this->db){
            // Return True
            return true;
        }
        // Return False
        return false;
    }


    // =====
    // CLOSE
    // =====
    public function _close(){
        // Close the database connection
        // If successful
        $this->db = null;

        return true;
    }

    // =====
    // READ
    // =====
    public function _read($id){
        // Set query
        $st = $this->db->prepare(('SELECT `data` FROM `session` WHERE id = :id'));

        // Attempt execution
        // If successful

        if($st->execute(array('id' => $id))){
            // Save returned row
            $row = $st->fetch(\PDO::FETCH_ASSOC);
            if (!$row) {
                return '';
            }
            // Return the data
            return $row['data'];
        }else{
            // Return an empty string
            return '';
        }
    }

    // =====
    // WRITE
    // =====
    public function _write($id, $data){
        // Create time stamp
        $access = time();

        // Set query
        $st = $this->db->prepare('REPLACE INTO `session` VALUES (:id, :access, :data)');

        // Attempt Execution
        // If successful
        if($st->execute(array('id' => $id, 'access' => $access, 'data' => $data))){
            // Return True
            return true;
        }

        // Return False
        return false;
    }

    // =======
    // DESTROY
    // =======
    public function _destroy($id){
        $st = $this->db->prepare('DELETE FROM `session` WHERE id = :id');

        if($st->execute(array('id' => $id))){
            // Return True
            return true;
        }

        // Return False
        return false;
    }

    // ==================
    // GARBAGE COLLECTION
    // ==================
    public function _gc($max){
      // Calculate what is to be deemed old
      $old = time() - $max;

      // Set query
      $st = $this->db->prepare('DELETE FROM `session` WHERE access < :old');


      // Attempt execution
      if($st->execute(array('old' => $old))){
          // Return True
          return true;
      }

      // Return False
      return false;
    }
}
