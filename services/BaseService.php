<?php
require_once __DIR__ . "/../helpers/Helper.php";

abstract class BaseService
{
    protected $repo;
    protected $fileHelper;
    public function __construct(Helper $fileHelper, $repo)
    {
        $this->repo = $repo;

        // DI là cách tốt nhất
        $this->fileHelper = $fileHelper;
        // Singleton
        //$this->fileHelper = FileHelper::getInstance(); // Lấy instance của FileHelper

    }

      public function getById($id)
    {
        return $this->repo->findById($id);
    }

    public function getAll($sort, $order, $limit, $offset)
    {
        // return $this->repo->fetchAll();
        // return $this->repo->fetchAllWithSort($sort, $order)?:[];
        return $this->repo->fetchAllWithSortAndPagination($sort, $order, $limit, $offset) ?: [];
    }

    public function delete($id)
    {
        try {
            $obj = $this->repo->findById($id);
            if (! $obj) {
                throw new Exception(NOT_FOUND);
            }
            return $this->repo->delete($id);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function searchByNameOrEmailWithPaginationAndSort($name, $email, $limit, $page, $sortBy, $sortOrder)
    {
        try {
            $offset     = ($page - 1) * $limit;
           return $data       = $this->repo->searchByNameOrEmailWithPaginationAndSort($name, $email, $limit, $offset, $sortBy, $sortOrder);
            // $totalCount = $this->repo->getTotalByNameOrEmail($name, $email);
            // $totalPages = max(ceil($$totalCount / $limit), 1);
           
            // return [
            //     'data'         => $data,
            //     // 'total_pages'  => $totalPages,
            //     // 'current_page' => $page,
            // ];
        } catch (Exception $e) {
            throw new Exception(SYSTEM_ERR . $e->getMessage());
        }
    }
    public function getTotalSearch($name, $email)
    {
        return $this->repo->getTotalByNameOrEmail($name, $email);
    }

    public function getTotal()
    {
        return $this->repo->getTotal();
    }
}
