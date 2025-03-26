<?php
require_once __DIR__ . "/../dto/NewAdmin.php";
class Admin
{
    private string $password;
    private string $role_type;
    private int $id;
    private string $name;
    private string $email;
    private string $avatar;
    private int $ins_id;
    private ?int $upd_id;
    private string $ins_datetime;
    private ?string $upd_datetime;
    private string $del_flag;

    // Constructor
    public function __construct($data = [])
    {
        $this->id        = $data['id'] ?? 0;
        $this->name      = $data['name'] ?? '';
        $this->role_type = $data['role_type'] ?? '1';
        $this->password  = $data['password'] ?? '';
        $this->email     = $data['email'] ?? '';
        $this->avatar    = $data['avatar'] ?? '';
        $this->ins_id    = $data['ins_id'] ?? 0;
        $this->upd_id    = $data['upd_id'] ?? null;
        $this->del_flag  = $data['del_flag'] ?? '0';

        $this->ins_datetime = isset($data['ins_datetime'])
        ? $data['ins_datetime']
        : (new DateTime())->format('Y-m-d H:i:s');

        $this->upd_datetime = isset($data['upd_datetime'])
        ? $data['upd_datetime']
        : (new DateTime())->format('Y-m-d H:i:s');
    }

    // Getters
    public function getId(): int
    {return $this->id;}
    public function getName(): string
    {return $this->name;}
    public function getRoleType(): string
    {return $this->role_type;}
    public function getPassword(): string
    {return $this->password;}
    public function getEmail(): string
    {return $this->email;}
    public function getAvatar(): string
    {return $this->avatar;}
    public function getInsId(): int
    {return $this->ins_id;}
    public function getUpdId(): ?int
    {return $this->upd_id;}
    public function getInsDateTime(): string
    {return $this->ins_datetime;}
    public function getUpdDateTime(): ?string
    {return $this->upd_datetime;}
    public function getDelFlag(): string
    {return $this->del_flag;}

    // Setters
    public function setId(int $id): void
    {$this->id = $id;}
    public function setName(string $name): void
    {$this->name = $name;}
    public function setRoleType(string $role_type): void
    {$this->role_type = $role_type;}
    public function setPassword($password)
    {
        $this->password = $password;
    }
    public function setEmail(string $email): void
    {$this->email = $email;}
    public function setAvatar(string $avatar): void
    {$this->avatar = $avatar;}
    public function setInsId(int $ins_id): void
    {$this->ins_id = $ins_id;}
    public function setUpdId(?int $upd_id): void
    {$this->upd_id = $upd_id;}
    public function setDelFlag(string $del_flag): void
    {$this->del_flag = $del_flag;}

    public function setInsDateTime($datetime): void
    {
        $this->ins_datetime = is_string($datetime)
        ? $datetime
        : (new DateTime())->format('Y-m-d H:i:s');
    }

    public function setUpdDateTime($datetime): void
    {
        $this->upd_datetime = is_string($datetime)
        ? $datetime
        : (new DateTime())->format('Y-m-d H:i:s');
    }

    // Hàm chuyển đổi object thành mảng
    public function toArray()
    {
        return get_object_vars($this);
    }

    public static function fromNewAdmin(array $newAdmin): self
{
    return new self([
        'name'      => $newAdmin['name'] ?? null,
        'email'     => $newAdmin['email'] ?? null,
        'password'  => $newAdmin['password'] ?? null,
        'role_type' => $newAdmin['role_type'] ?? null,
        'avatar'    => $newAdmin['avatar'] ?? null,
        'ins_id'    => $_SESSION['account']['id'] ?? null,
    ]);
}


}