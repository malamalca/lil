<?php
namespace Lil\Model\Table;

trait IsOwnedByTrait
{
    /**
     * Returns default counter
     *
     * @param uuid $entityId Entity id.
     * @param uuid $ownerId Owner Id.
     * @param string $ownerField Owner field.
     * @return mixed
     */
    public function isOwnedBy($entityId, $ownerId, $ownerField = 'owner_id')
    {
        $conditions = ['id' => $entityId, $ownerField => $ownerId];
        $ret = $this->exists($conditions);

        return $ret;
    }
}
