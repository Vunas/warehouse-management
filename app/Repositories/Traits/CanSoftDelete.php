<?php

namespace App\Repositories\Traits;

use Illuminate\Database\Eloquent\Model;

trait CanSoftDelete
{
    /**
     * Xóa mềm một bản ghi theo ID
     *
     * @param int $id
     * @return bool|null
     */
    public function softDelete($id)
    {
        $model = $this->findById($id);

        if ($model instanceof Model) {
            return $model->delete(); 
        }

        return false;
    }

    /**
     * Khôi phục bản ghi đã bị xóa mềm
     *
     * @param int $id
     * @return bool|null
     */
    public function restore($id)
    {
        $model = $this->findById($id, true); 

        if ($model instanceof Model) {
            return $model->restore();
        }

        return false;
    }

    /**
     * Xóa cứng bản ghi đã bị xóa mềm
     *
     * @param int $id
     * @return bool|null
     */
    public function forceDelete($id)
    {
        $model = $this->findById($id, true);

        if ($model instanceof Model) {
            return $model->forceDelete();
        }

        return false;
    }
}
