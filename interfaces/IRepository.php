<?php
interface IRepository
{

    public function fetchAll();
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function checkExistedEmail(string $email);
    // public function search(array $data);

}
?>