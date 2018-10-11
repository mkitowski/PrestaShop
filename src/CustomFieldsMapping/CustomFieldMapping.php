<?php
namespace GetResponse\CustomFieldsMapping;

/**
 * Class CustomFieldMapping
 * @package GetResponse\CustomFieldsMapping
 */
class CustomFieldMapping
{
    const ACTIVE = 'yes';
    const INACTIVE = 'no';

    const DEFAULT_YES = 'yes';
    const DEFAULT_NO = 'no';

    /** @var int */
    private $id;

    /** @var string */
    private $value;

    /** @var string */
    private $name;

    /** @var string */
    private $active;

    /** @var string */
    private $field;

    /** @var int */
    private $default;

    /**
     * @param int $id
     * @param string $value
     * @param string $name
     * @param string $active
     * @param string $field
     * @param int $default
     */
    public function __construct($id, $value, $name, $active, $field, $default)
    {
        $this->id = $id;
        $this->value = $value;
        $this->name = $name;
        $this->active = $active;
        $this->field = $field;
        $this->default = $default;
    }

    /**
     * @param array $request
     * @return CustomFieldMapping
     */
    public static function createFromRequest(array $request)
    {
        return new self(
            $request['id'],
            $request['value'],
            $request['name'],
            1 === (int)$request['active'] ? self::ACTIVE : self::INACTIVE,
            '',
            $request['default']);
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return self::DEFAULT_YES === $this->default;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
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
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return self::ACTIVE === $this->getActive();
    }

    /**
     * @return string
     */
    public function getActive()
    {
        return $this->active;
    }
}