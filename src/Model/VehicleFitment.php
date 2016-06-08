<?php

namespace Uparts\Model;

class VehicleFitment
{
    private $id;
    private $isStandard;
    private $frb;
    private $loadIndex;
    private $speedRating;
    private $sizeDescription;
    private $prefix;
    private $loadDescription;
    private $sectionWidth;
    private $aspectRatio;
    private $rimSize;
    private $fitmentName;
    private $notes;

    public function __construct($id, $isStandard, $frb, $loadIndex, $speedRating, $sizeDescription, $prefix, $loadDescription, $sectionWidth, $aspectRatio, $rimSize, $fitmentName, $notes)
    {
        /**
         * @todo: This constructor should throw an exception when values do not satisfy
         * domain validation rules.
         */
        $this->id = $id;
        $this->isStandard = $isStandard;
        $this->frb = $frb;
        $this->loadIndex = $loadIndex;
        $this->speedRating = $speedRating;
        $this->sizeDescription = $sizeDescription;
        $this->prefix = $prefix;
        $this->loadDescription = $loadDescription;
        $this->sectionWidth = $sectionWidth;
        $this->aspectRatio = $aspectRatio;
        $this->rimSize = $rimSize;
        $this->fitmentName = $fitmentName;
        $this->notes = $notes;
    }

    public function getId()
    {
        return $this->id;
    }

    public function isStandard()
    {
        return $this->isStandard;
    }

    public function getFrb()
    {
        return $this->frb;
    }

    public function getLoadIndex()
    {
        return $this->loadIndex;
    }

    public function getSpeedRating()
    {
        return $this->speedRating;
    }

    public function getSizeDescription()
    {
        return $this->sizeDescription;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function getLoadDescription()
    {
        return $this->loadDescription;
    }

    public function getSectionWidth()
    {
        return $this->sectionWidth;
    }

    public function getAspectRatio()
    {
        return $this->aspectRatio;
    }

    public function getRimSize()
    {
        return $this->rimSize;
    }

    public function getFitmentName()
    {
        return $this->fitmenName;
    }

    public function getNotes()
    {
        return $this->notes;
    }

    public static function fromApiItem($item, $note)
    {
        /** 
         * @todo: This should throw exceptions when the required fields do not exist.
         * @todo: In the case of $isStandard, I'm assuming this is how it should work. I would want to
         * ask a domain expert to be sure, though.
         */
        $id = $item["CARTIREID"];
        $isStandard = $item["STDOROPT"] === "S"; 
        $frb = $item["FRB"];
        $loadIndex = $item["LOADINDEX"];
        $speedRating = $item["SPEEDRATING"];
        $sizeDescription = $item["SIZEDESCRIPTION"];
        $prefix = $item["PREFIX"];
        $loadDescription = $item["LOADDESCRIPTION"];
        $sectionWidth = $item["SECTIONWIDTH"];
        $aspectRatio = $item["ASPECTRATIO"];
        $rimSize = $item["RIM"];
        $fitmentName = $item["FITMENT"];

        return new self(
            $id,
            $isStandard,
            $frb,
            $loadIndex,
            $speedRating,
            $sizeDescription,
            $prefix,
            $loadDescription,
            $sectionWidth,
            $aspectRatio,
            $rimSize,
            $fitmentName,
            $note
        );
    }
}
