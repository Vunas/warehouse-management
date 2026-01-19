<?php

namespace App\Repositories\Interfaces;

interface SizeConversionRuleRepositoryInterface
{
    public function getAllActive();
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    
    // Tìm rule phù hợp nhất cho kích thước (Logic đặc thù)
    public function findMatchingRule($length, $width, $height);
}