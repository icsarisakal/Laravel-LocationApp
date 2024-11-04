<?php

namespace App\Interfaces;

interface LocationRepositoryInterface
{
    //
    public function getAll();
    public function getDetail($id);
    public function store(array $data);
    public function update(array $data,$id);
    public function delete($id);
    public function makeRoute(array $data);
}
