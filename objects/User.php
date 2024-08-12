<?php

class User
{
    private $conn;
    private $table_name = "users";
    public $id;
    public $name;
    public $login;
    public $password;
    public $email;
    public $dateReg;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Добавление нового пользователя
    public function create() {

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->login = htmlspecialchars(strip_tags($this->login));
        $passwordHash = password_hash($this->password, PASSWORD_DEFAULT);

        $query = $this->conn->query("SELECT `login`, email FROM {$this->table_name} WHERE `login` = {$this->name} OR email = {$this->email}");
        $rowCount = $query -> rowCount();

        if($rowCount > 0) {
           return false;
        }
        else {
            $stmt = $this->conn->prepare("INSERT INTO {$this->table_name} (`name`,`email`,`login`,`password`,`date_reg`) VALUES(:name,:email,:login,:password,:date_reg)");
            
            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":password", $passwordHash);
            $stmt->bindParam(":login", $this->login);
            $stmt->bindParam(":date_reg", $this->$dateReg);

            $stmt-> execute();
            return true;
        }           
    }

    // Редактирование пользователя
    public function update() {

        $stmt = $this->conn -> prepare("UPDATE {$this->table_name} SET `name` = :name, `login` = :login, `password` = :password, email = :email WHERE id = {$this->id}");

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->login = htmlspecialchars(strip_tags($this->login));
        $passwordHash = password_hash($this->password, PASSWORD_DEFAULT);

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $passwordHash);
        $stmt->bindParam(":login", $this->login);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Удаление пользователя
    public function delete() {

        $del = $this->conn -> query("DELETE FROM {$this->table_name} WHERE id = {$this->id}");
        if($del) {
            return true;
        }
        else {
            return false;
        }
    }

    // Авторизация пользователя
    public function auth() {

        $query = $this->conn -> query("SELECT * FROM {$this->table_name} WHERE `login`= {$this->login}");
        if($query->rowCount() > 0) {
            $passwordInDb = $query->fetch();
            if(password_verify($this->password, $passwordInDb['password'])) {
                return true;
            }
            else{
                return false;
            }  
        }
        else{
            return false;
        }      
    }

    // Получение информации о пользователе
    public function read() {

        $user = $this->conn -> query("SELECT * FROM {$this->table_name} WHERE id = {$this->id}");
        $row = $user->fetch(PDO::FETCH_ASSOC);

        $this->name = $row["name"];
        $this->login = $row["login"];
        $this->email = $row["email"];
        $this->dateReg = $row["date_reg"];
    }
}