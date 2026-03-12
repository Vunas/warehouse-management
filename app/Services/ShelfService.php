<?php

namespace App\Services;

use App\Repositories\Interfaces\ShelfRepositoryInterface;

class ShelfService
{
    protected $shelfRepo;

    public function __construct(ShelfRepositoryInterface $shelfRepo)
    {
        $this->shelfRepo = $shelfRepo;
    }

    public function getShelvesByZone($zoneId)
    {
        return $this->shelfRepo->getByZoneId($zoneId);
    }

    public function createShelf(array $data)
    {
        return $this->shelfRepo->create($data);
    }

    public function updateShelf($id, array $data)
    {
        return $this->shelfRepo->update($id, $data);
    }

    public function deleteShelf($id)
    {
        return $this->shelfRepo->delete($id);
    }
}