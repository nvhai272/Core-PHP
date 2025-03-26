<?php
class NewAdmin
{
    public string $password;
    public string $role_type;
    public string $name;
    public string $email;
    public string $avatar;

    public function __construct(array $data)
    {
        $this->password  = $data["password"];
        $this->role_type = $data["role_type"];
        $this->name      = $data["name"];
        $this->email     = $data["email"];
        $this->avatar    = $data["avatar"] ?? '';
    }

}
