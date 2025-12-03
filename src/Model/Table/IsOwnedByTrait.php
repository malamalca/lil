<?php
declare(strict_types=1);

namespace Lil\Model\Table;

trait IsOwnedByTrait
{
    /**
     * Returns default counter
     *
     * @param \Lil\Model\Table\uuid $entityId Entity id.
     * @param \Lil\Model\Table\uuid $ownerId Owner Id.
     * @param string $ownerField Owner field.
     * @return mixed
     */
    public function isOwnedBy(uuid $entityId, uuid $ownerId, string $ownerField = 'owner_id'): mixed
    {
        $conditions = ['id' => $entityId, $ownerField => $ownerId];
        $ret = $this->exists($conditions);

        return $ret;
    }
}
