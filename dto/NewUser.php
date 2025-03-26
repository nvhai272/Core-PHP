<?php
class NewUser
{
    public string $password;
    public string $status;
    public string $facebook_id;
    public string $name;
    public string $email;
    public string $avatar;
    public int $ins_id;

    public function __construct(array $data)
    {
        $this->password    = $data["password"];
        $this->status      = $data["status"];
        $this->facebook_id = $data["facebook_id"]?? '';
        $this->name        = $data["name"];
        $this->email       = $data["email"];
        $this->avatar      = $data["avatar"] ?? '';
        $this->ins_id      = $data["ins_id"] ?? 1;
    }
}
