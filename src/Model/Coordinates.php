<?php

namespace App\Model;

class Coordinates
{
    /**
     * @var string
     */
    protected $lat;
    /**
     * @var string
     */
    protected $lng;

    /**
     * @return string
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @param string $lat
     *
     * @return self
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * @return string
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @param string $lng
     *
     * @return self
     */
    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }
}
