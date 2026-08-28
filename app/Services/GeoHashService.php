<?php

namespace App\Services;

/**
 * @author   Saikiran Ch <saikiranchavan@gmail.com>
 * @class    Geohash
 * @description Algorithm to encode geographic coordinates to a string of letters and digits
 */
class GeoHashService
{
    const NORTH = 0;
    const EAST = 1;
    const SOUTH = 2;
    const WEST = 3;

    const EVEN = 0;
    const ODD = 1;

    protected $base32Mapping = "0123456789bcdefghjkmnpqrstuvwxyz";

    private $borderChars = [
        self::EVEN => [
            self::NORTH => 'bcfguvyz',
            self::EAST => 'prxz',
            self::SOUTH => '0145hjnp',
            self::WEST => '028b',
        ]
    ];

    private $neighborChars = [
        self::EVEN => [
            self::NORTH => '238967debc01fg45kmstqrwxuvhjyznp',
            self::EAST => '14365h7k9dcfesgujnmqp0r2twvyx8zb',
            self::SOUTH => 'bc01fg45238967deuvhjyznpkmstqrwx',
            self::WEST => 'p0r21436x8zb9dcf5h7kjnmqesgutwvy',
        ],
    ];

    public function __construct() {
        $this->neighborChars[self::ODD] = array(
            self::NORTH => $this->neighborChars[self::EVEN][self::EAST],
            self::EAST => $this->neighborChars[self::EVEN][self::NORTH],
            self::SOUTH => $this->neighborChars[self::EVEN][self::WEST],
            self::WEST => $this->neighborChars[self::EVEN][self::SOUTH],
        );

        $this->borderChars[self::ODD] = array(
            self::NORTH => $this->borderChars[self::EVEN][self::EAST],
            self::EAST => $this->borderChars[self::EVEN][self::NORTH],
            self::SOUTH => $this->borderChars[self::EVEN][self::WEST],
            self::WEST => $this->borderChars[self::EVEN][self::SOUTH],
        );
    }

    public function encode($latitude, $longitude, $geohashLength = 5)
    {
        if ($geohashLength % 2 == 0) {
            $latBitsLength = $lonBitsLength = ($geohashLength/2) * 5;
        } else {
            $latBitsLength = (ceil($geohashLength / 2) * 5) - 3;
            $lonBitsLength = $latBitsLength + 1;
        }

        $binaryString = "";
        $latbits = $this->getBits($latitude, -90, 90, $latBitsLength);
        $lonbits = $this->getBits($longitude, -180, 180, $lonBitsLength);
        $binaryLength = strlen($latbits) + strlen($lonbits);

        for ($i=1 ; $i < $binaryLength + 1; $i++) {
            if ($i%2 == 0) {
                $pos = (int)($i-2)/2;
                $binaryString .= $latbits[$pos];
            } else {
                $pos = (int)floor($i/2);
                $binaryString .= $lonbits[$pos];
            }
        }

        $hash = "";
        for ($i=0; $i< strlen($binaryString); $i+=5) {
            $n = bindec(substr($binaryString,$i,5));
            $hash = $hash . $this->base32Mapping[$n];
        }
        return $hash;
    }

    public function decode($hash, $error = false)
    {
        $hashLength = strlen($hash);
        $geohashArray = str_split($hash, 1);
        $latlonbits = "";
        
        foreach($geohashArray as $g) {
            if (($position = stripos($this->base32Mapping, $g)) !== FALSE) {
                $latlonbits .= str_pad(decbin($position), 5, "0", STR_PAD_LEFT);
            } else {
                $latlonbits .= "00000";
            }
        }

        $binaryLength = strlen($latlonbits);
        $latbits = "";
        $lonbits = "";

        for ($i = 0; $i < $binaryLength; $i++) {
            ($i % 2 == 0) ? ($lonbits .= $latlonbits[$i]) : ($latbits .= $latlonbits[$i]);
        }

        $latitude = $this->getCoordinate(-90, 90, $latbits);
        $longitude = $this->getCoordinate(-180, 180, $lonbits);

        return [
            round($latitude, $hashLength - 2), 
            round($longitude, $hashLength - 2)
        ];
    }

    public function getCoordinate($min, $max, $binaryString)
    {
        $value = ($min + $max) / 2;
        for ($i = 0; $i < strlen($binaryString); $i++) {
            $mid = ($min + $max)/2 ;
            if ($binaryString[$i] == 1){
                $min = $mid ;
            } else {
                $max = $mid;
            }
            $value = ($min + $max)/2;
        }
        return $value;
    }

    public function getBits($coordinate, $min, $max, $bitsLength)
    {
        $binaryString = "";
        $i = 0;
        while ($bitsLength > $i) {
            $mid = ($min+$max)/2;
            if ($coordinate > $mid) {
                $binaryString .= "1";
                $min = $mid;
            } else {
                $binaryString .= "0";
                $max = $mid;
            }
            $i++;
        }
        return $binaryString;
    }

    public function getNeighbors($hash) {
        $hashNorth = $this->calculateNeighbor($hash, self::NORTH);
        $hashEast = $this->calculateNeighbor($hash, self::EAST);
        $hashSouth = $this->calculateNeighbor($hash, self::SOUTH);
        $hashWest = $this->calculateNeighbor($hash, self::WEST);

        return [
            'North'     => $hashNorth,
            'East'      => $hashEast,
            'South'     => $hashSouth,
            'West'      => $hashWest,
            'NorthEast' => $this->calculateNeighbor($hashNorth, self::EAST),
            'SouthEast' => $this->calculateNeighbor($hashSouth, self::EAST),
            'SouthWest' => $this->calculateNeighbor($hashSouth, self::WEST),
            'NorthWest' => $this->calculateNeighbor($hashNorth, self::WEST),
        ];
    }

    private function calculateNeighbor($hash, $direction) {
        $length = strlen($hash);
        if ($length == 0) return '';
        
        $lastChar = $hash[$length - 1];
        $evenOrOdd = ($length - 1) % 2;
        $baseHash = substr($hash, 0, -1);
        
        if (strpos($this->borderChars[$evenOrOdd][$direction], $lastChar) !== false) {
            $baseHash = $this->calculateNeighbor($baseHash, $direction);
        }
        return $baseHash . $this->neighborChars[$evenOrOdd][$direction][strpos($this->base32Mapping, $lastChar)];
    }
}