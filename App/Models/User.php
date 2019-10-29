<?php

namespace App\Models;

use PDO;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
{

    /**
     * Error messages
     * 
     * @var array
     *  */ 
    public $errors = [];

    /**
     * Class constructor
     * 
     * @param array $data Initial property values
     * 
     * @return void
     *  */  
    public function __construct($data = [])
    {
        foreach($data as $key => $value) {
            $this->$key = $value;
        };

    }

    /**
     * Save the user model with the current propertxy values 
     * 
     * @return void
     * */ 
    public function save()
    {
        $this->validate();

        if (empty($this->errors)) {

            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $sql = 'INSERT INTO users (name, email, password_hash)
                    VALUES (:name, :email, :password_hash)';
            
            $db = static::getDB();
            $stmt = $db->prepare($sql);
    
            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
    
           return $stmt->execute();

        }

        return false;
    }

    /**
     * Validate current property values,adding validation error messages to the errors array property 
     * 
     * @return void
     * */  
    public function validate()
    {
        // name
        if ($this->name == ''){
            $this->errors[] = 'Vollständigen Namen eingeben';
        }

        // email address
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[] = 'Ungültige E-mail-Adresse';
        }

        if (static::emailExists($this->email, $this->id ?? null)) {
            $this->errors[] = 'E-Mail-Adresse bereits vergeben';
        }

        // password
        if (isset($this->password)) {

            if (strlen($this->password) < 6) {
                $this->errors[] = 'Passwort muss mindestens 6 Zeichen lang sein';
            }
    
            if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
                $this->errors[] = 'Password muss mindestens 1 Buchstaben enthalten';
            }
    
            if (preg_match('/.*\d+.*/i', $this->password) == 0) {
                $this->errors[] = 'Password muss mindestens 1 Zahl enthalten';
            }
        }
    }

    /**
     * See if a user ercord already exists with the specified email
     * 
     * @param string $email email address to search for 
     * @param string $ignore_id Return false anyway if the record found has this ID
     * 
     * @return boolean True if a record already exists with the specified email, false otherwise */ 
    public static function emailExists($email, $ignore_id = null) 
    {
       $user = static::findByEmail($email);

       if ($user) {
           if ($user->id != $ignore_id) {
               return true;
           }
       }

       return false;
    }

    /**
     * Find a user model by email address
     * 
     * @param string $email email address to search for
     * 
     * @return mixed User object if found, false otherwise 
     * */
    public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }  

    /**
     * Authenticate a user by email and password 
     * 
     * @param string $email email address
     * @param string $password password
     * 
     * @return mixed The user object or false if authentication fails
    */ 
    public static function authenticate($email, $password)
    {
        $user = static::findByEmail($email);

        if ($user) {
            if (password_verify($password, $user->password_hash)) {
                return $user;
            }
        }

        return false;
    }

    /**
     * Get all users 
     * 
     * @return mixed object collection if found, false otherwise
    */ 
    public static function getAll()
    {
        $sql = 'SELECT * FROM users';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
       
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetchAll();
    }

     /**
     * Find a user model by ID address
     * 
     * @param string $id The user ID
     * 
     * @return mixed User object if found, false otherwise 
    */
    public static function findByID($id)
    {
        $sql = 'SELECT * FROM users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }  

    /**
     * Delete a user model by ID
     * 
     * @param string $id The user ID
     * 
     * @return void 
    */ 
    public function delete()
    {
        $sql = 'DELETE FROM users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();
    }  

    /**
     * Update the user's profile
     * 
     * @param array $data Data from the edit profile form
     * 
     * @return boolean True if the data was updated, false otherwise
    */ 
    public function updateProfile($data)
    {
        $this->name = $data['name'];
        $this->email = $data['email'];

        // Only validate and update the password if a value provided
        if ($data['password'] != '') {
            $this->password = $data['password'];
        }

        $this->validate();

        if (empty($this->errors)) {
            
            $sql = 'UPDATE users
                    SET name = :name,
                        email = :email';
            
            // Add password if it's set
            if (isset($this->password)) {
                $sql .= ' , password_hash = :password_hash';
            }

            $sql .= "\nWHERE id = :id";


            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

            // Add password if it's set
            if (isset($this->password)) {
                $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
                $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            }

            return $stmt->execute();

        }

        return false;
    }

}
