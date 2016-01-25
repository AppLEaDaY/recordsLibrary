<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Record
 *
 * @ORM\Table(name="records")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RecordRepository")
 */
class Record
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="upc", type="string", length=128, nullable=true, unique=true)
     */
    private $upc;

    /**
     * @var string
     *
     * @ORM\Column(name="asin", type="string", length=64, nullable=true, unique=true)
     */
    private $asin;

    /**
     * @var string
     *
     * @ORM\Column(name="cddbid", type="string", length=128, nullable=true, unique=true)
     */
    private $cddbid;

    /**
     * @var string
     *
     * @ORM\Column(name="artist", type="string", length=255)
     */
    private $artist;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="year", type="string", length=4, nullable=true)
     */
    private $year;

    /**
     * @var string
     *
     * @ORM\Column(name="mediaType", type="string", length=32, nullable=true)
     */
    private $mediaType;

    /**
     * @var int
     *
     * @ORM\Column(name="mediaCount", type="smallint", nullable=true)
     */
    private $mediaCount;

    /**
     * @var string
     *
     * @ORM\Column(name="coverImageUrl", type="string", length=255, nullable=true)
     */
    private $coverImageUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="recordLabel", type="string", length=128)
     */
    private $recordLabel;

    /**
     * @var string
     *
     * @ORM\Column(name="genre", type="string", length=128, nullable=true)
     */
    private $genre;

    /**
     * @var json_array
     *
     * @ORM\Column(name="tracksLists", type="json_array", nullable=true)
     */
    private $tracksLists;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ts", type="datetime")
     */
    private $ts;





    /* setting current timestamp upon creation */
    public function __construct()
    {
      $this->ts = new \DateTime();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set upc
     *
     * @param string $upc
     *
     * @return Record
     */
    public function setUpc($upc)
    {
        $this->upc = $upc;

        return $this;
    }

    /**
     * Get upc
     *
     * @return string
     */
    public function getUpc()
    {
        return $this->upc;
    }

    /**
     * Set asin
     *
     * @param string $asin
     *
     * @return Record
     */
    public function setAsin($asin)
    {
        $this->asin = $asin;

        return $this;
    }

    /**
     * Get asin
     *
     * @return string
     */
    public function getAsin()
    {
        return $this->asin;
    }

    /**
     * Set cddbid
     *
     * @param string $cddbid
     *
     * @return Record
     */
    public function setCddbid($cddbid)
    {
        $this->cddbid = $cddbid;

        return $this;
    }

    /**
     * Get cddbid
     *
     * @return string
     */
    public function getCddbid()
    {
        return $this->cddbid;
    }

    /**
     * Set artist
     *
     * @param string $artist
     *
     * @return Record
     */
    public function setArtist($artist)
    {
        $this->artist = $artist;

        return $this;
    }

    /**
     * Get artist
     *
     * @return string
     */
    public function getArtist()
    {
        return $this->artist;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Record
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set year
     *
     * @param string $year
     *
     * @return Record
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set mediaType
     *
     * @param string $mediaType
     *
     * @return Record
     */
    public function setMediaType($mediaType)
    {
        $this->mediaType = $mediaType;

        return $this;
    }

    /**
     * Get mediaType
     *
     * @return string
     */
    public function getMediaType()
    {
        return $this->mediaType;
    }

    /**
     * Set mediaCount
     *
     * @param integer $mediaCount
     *
     * @return Record
     */
    public function setMediaCount($mediaCount)
    {
        $this->mediaCount = $mediaCount;

        return $this;
    }

    /**
     * Get mediaCount
     *
     * @return int
     */
    public function getMediaCount()
    {
        return $this->mediaCount;
    }

    /**
     * Set coverImageUrl
     *
     * @param string $coverImageUrl
     *
     * @return Record
     */
    public function setCoverImageUrl($coverImageUrl)
    {
        $this->coverImageUrl = $coverImageUrl;

        return $this;
    }

    /**
     * Get coverImageUrl
     *
     * @return string
     */
    public function getCoverImageUrl()
    {
        return $this->coverImageUrl;
    }

    /**
     * Set recordLabel
     *
     * @param string $recordLabel
     *
     * @return Record
     */
    public function setRecordLabel($recordLabel)
    {
        $this->recordLabel = $recordLabel;

        return $this;
    }

    /**
     * Get recordLabel
     *
     * @return string
     */
    public function getRecordLabel()
    {
        return $this->recordLabel;
    }

    /**
     * Set genre
     *
     * @param string $genre
     *
     * @return Record
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * Get genre
     *
     * @return string
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * Set tracksLists
     *
     * @param array $tracksLists
     *
     * @return Record
     */
    public function setTracksLists($tracksLists)
    {
        $this->tracksLists = $tracksLists;

        return $this;
    }

    /**
     * Get tracksLists
     *
     * @return array
     */
    public function getTracksLists()
    {
        return $this->tracksLists;
    }

    /**
     * Set ts
     *
     * @param \DateTime $ts
     *
     * @return Record
     */
    public function setTs($ts)
    {
        $this->ts = $ts;

        return $this;
    }

    /**
     * Get ts
     *
     * @return \DateTime
     */
    public function getTs()
    {
        return $this->ts;
    }
}
