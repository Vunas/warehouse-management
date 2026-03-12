<?php

namespace App\Repositories\Traits;

trait CanDelete
{
    public function delete($id)
    {
        $model = $this->findById($id);
        return $model->delete();
    }
}