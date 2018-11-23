<?php
namespace GetResponse\CustomFieldsMapping;

/**
 * Class CustomFieldMapping
 * @package GetResponse\CustomFieldsMapping
 */
class CustomFieldMapping
{
    /** @var int */
    private $id;

    /** @var string */
    private $customName;

    /** @var string */
    private $customerPropertyName;

    /** @var string */
    private $grCustomId;

    /** @var bool */
    private $isActive;

    /** @var bool */
    private $isDefault;

    /**
     * @param int $id
     * @param string $customName
     * @param string $customerPropertyName
     * @param string $grCustomId
     * @param bool $isActive
     * @param bool $isDefault
     */
    public function __construct($id, $customName, $customerPropertyName, $grCustomId, $isActive, $isDefault)
    {
        $this->id = $id;
        $this->customName = $customName;
        $this->customerPropertyName = $customerPropertyName;
        $this->grCustomId = $grCustomId;
        $this->isActive = $isActive;
        $this->isDefault = $isDefault;
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return $this->isDefault;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCustomerPropertyName()
    {
        return $this->customerPropertyName;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @return string
     */
    public function getCustomName()
    {
        return $this->customName;
    }

    /**
     * @return string
     */
    public function getGrCustomId()
    {
        return $this->grCustomId;
    }

    /**
     * @param array $params
     * @return CustomFieldMapping
     */
    public static function createFromArray(array $params)
    {
        return new self(
            $params['id'],
            $params['custom_name'],
            $params['customer_property_name'],
            $params['gr_custom_id'],
            (bool) $params['is_active'],
            (bool) $params['is_default']
        );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'custom_name' => $this->getCustomName(),
            'customer_property_name' => $this->getCustomerPropertyName(),
            'gr_custom_id' => $this->getGrCustomId(),
            'is_active' => $this->isActive(),
            'is_default' => $this->isDefault(),
        ];
    }
}
