<?php

class GetResponseExportSettings
{
    /** @var string */
    private $listId;
    /** @var int|null */
    private $cycleDay;
    /** @var bool */
    private $updateAddress;
    /** @var bool */
    private $newsletter;
    /** @var bool */
    private $asyncExport;
    /** @var bool */
    private $exportEcommerce;

    /**
     * GetResponseExportSettings constructor.
     * @param string $listId
     * @param int|null $cycleDay
     * @param bool $updateAddress
     * @param bool $newsletter
     * @param bool $asyncExport
     * @param bool $exportEcommerce
     */
    public function __construct(
        $listId,
        $cycleDay,
        $updateAddress,
        $newsletter,
        $asyncExport,
        $exportEcommerce
    ) {
        $this->listId = $listId;
        $this->cycleDay = $cycleDay;
        $this->updateAddress = $updateAddress;
        $this->newsletter = $newsletter;
        $this->asyncExport = $asyncExport;
        $this->exportEcommerce = $exportEcommerce;
    }

    /**
     * @return string
     */
    public function getListId()
    {
        return $this->listId;
    }

    /**
     * @return int|null
     */
    public function getCycleDay()
    {
        return $this->cycleDay;
    }

    /**
     * @return bool
     */
    public function isUpdateAddress()
    {
        return $this->updateAddress;
    }

    /**
     * @return bool
     */
    public function isNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * @return bool
     */
    public function isAsyncExport()
    {
        return $this->asyncExport;
    }

    /**
     * @return bool
     */
    public function isExportEcommerce()
    {
        return $this->exportEcommerce;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return array(
            'listId' => $this->listId,
            'cycleDay' => $this->cycleDay,
            'updateAddress' => $this->updateAddress,
            'newsletter' => $this->newsletter,
            'asyncExport' => $this->asyncExport,
            'exportEcommerce' => $this->exportEcommerce
        );
    }
}
