<?php
class User
{
    private ?string $password;
    private string $facebook_id;
    private string $status;
    private int $id;
    private string $name;
    private string $email;
    private string $avatar;
    private int $ins_id;
    private ?int $upd_id;
    private string $ins_datetime;
    private ?string $upd_datetime;
    private string $del_flag;

    // constructor
    public function __construct(int $id, string $name, string $email, string $avatar, string $status, string $facebook_id,
        int $ins_id, ?int $upd_id, string $ins_datetime, ?string $upd_datetime, string $del_flag, ?string $password) {
        $this->id           = $id;
        $this->name         = $name;
        $this->email        = $email;
        $this->avatar       = $avatar;
        $this->ins_id       = $ins_id;
        $this->upd_id       = $upd_id ?? null;
        $this->ins_datetime = $ins_datetime;
        $this->upd_datetime = $upd_datetime ?? null;
        $this->del_flag     = $del_flag ?? '0';
        $this->password     = $password ?? '';
        $this->status       = $status ?? 1;
        $this->facebook_id  = $facebook_id;
    }

    // có thể sử dụng magic method __get() and __set() thay thế cho get, set thông thường nhưng cần cẩn thận hơn
    // cần về tìm hiểu thêm về thằng magic method vì nó hơi ảo  :)

    // getter and setter
    public function getId(): int
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getAvatar(): string
    {
        return $this->avatar;
    }
    public function getInsId(): int
    {
        return $this->ins_id;
    }
    public function getUpdId(): ?int
    {
        return $this->upd_id;
    }
    public function getDelFlag(): string
    {
        return $this->del_flag;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function getPassword(): ?string
    {
        return $this->password;
    }
    public function getInsDateTime(): string
    {
        return $this->ins_datetime;
    }

    public function getUpdDatetime(): ?string
    {
        return $this->upd_datetime;
    }

    public function getFaceBookId(): string
    {
        return $this->facebook_id;
    }

    // setter có thể xử lí logic kiểm tra ở setter => cái này các ngôn ngữ khác có không nhỉ?
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function setName(string $name): void
    {
        $this->name = $name;
    }
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
    public function setAvatar(string $avatar): void
    {
        $this->avatar = $avatar;
    }
    public function setInsId(int $ins_id): void
    {
        $this->ins_id = $ins_id;
    }

    public function setUpdId(?int $upd_id): void
    {
        $this->upd_id = $upd_id;
    }
    public function setDelFlag(string $del_flag): void
    {
        $this->del_flag = $del_flag;
    }
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }
    public function setInsDateTime(string $ins_datetime): void
    {
        $this->ins_datetime = $ins_datetime;
    }

    public function setUpdDateTime(?string $upd_datetime): void
    {
        $this->upd_datetime = $upd_datetime;
    }

    public function setFaceBookId(string $facebook_id): void
    {
        $this->facebook_id = $facebook_id;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }

    public static function fromNewUser(array $user): self
    {
        return new self(
            0,
            $user['name'] ?? '',
            $user['email'] ?? '',
            $user['avatar'] ?? null,
            $user['status'] ?? '0',
            $user['facebook_id'] ?? null,
            $_SESSION['account']['id'] ?? null,
            null, // upd_id (chưa có ai cập nhật)
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s'), // upd_datetime (không để null nữa)
            '0',
            $user['password'] ?? '' // Đã mã hóa trước khi truyền vào
        );
    }
    
}
